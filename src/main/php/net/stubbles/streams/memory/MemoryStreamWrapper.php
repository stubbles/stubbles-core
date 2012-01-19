<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\memory;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\RuntimeException;
/**
 * Stream wrapper to read and write data from/into memory via file functions.
 *
 * A memory stream is useful when you want to pass contents to a class that
 * expects the contents to be in a file because it uses PHP's file functions
 * to read the data from a file. With the memory stream wrapper you can fool
 * this class by writing the contents to memory://mydata, now you just need
 * to get the class to read the data from this URL. Example:
 *
 * $xmlUnserializer = new XmlUnserializer();
 * $data = $xmlUnserializer->unserializeFile('memory://mydata');
 */
class MemoryStreamWrapper extends BaseObject
{
    /**
     * scheme used by the stream wrapper
     */
    const SCHEME               = 'memory';
    /**
     * switch whether class has already been registered as stream wrapper or not
     *
     * @type  bool
     */
    private static $registered = false;
    /**
     * data of different memory contents
     *
     * @type  array
     */
    protected static $buffer   = array();
    /**
     * current key
     *
     * @type  string
     */
    protected $key;
    /**
     * current position in buffer
     *
     * @type  int
     */
    protected $position = 0;

    /**
     * register the stream wrapper for memory:// protocol
     *
     * @throws  RuntimeException
     */
    public static function register()
    {
        if (true === self::$registered) {
            return;
        }

        if (stream_wrapper_register(self::SCHEME, __CLASS__) === false) {
            throw new RuntimeException('A handler has already been registered for the ' . self::SCHEME . ' protocol.');
        }

        self::$registered = true;
    }

    /**
     * open the stream
     *
     * @param   string  $path         the path to open
     * @param   string  $mode         mode for opening
     * @param   string  $options      options for opening
     * @param   string  $opened_path  full path that was actually opened
     * @return  bool
     */
    public function stream_open($path, $mode, $options, $opened_path)
    {
        $this->key = self::parsePath($path);
        switch (str_replace('t', '', str_replace('b', '', $mode))) {
            case 'r':
                // break omitted

            case 'r+':
                if (isset(self::$buffer[$this->key]) === false) {
                    return false;
                }

                $this->position = 0;
                break;

            case 'w':
                // break omitted

            case 'w+':
                self::$buffer[$this->key] = '';
                $this->position           = 0;
                break;

            case 'a':
                // break omitted

            case 'a+':
                if (isset(self::$buffer[$this->key]) === false) {
                    self::$buffer[$this->key] = '';
                }

                $this->position = strlen(self::$buffer[$this->key]);
                break;

            case 'x':
                // break omitted

            case 'x+':
                if (isset(self::$buffer[$this->key]) === true) {
                    return false;
                }

                self::$buffer[$this->key] = '';
                $this->position           = 0;
                break;

            default:
                if (isset(self::$buffer[$this->key]) === false) {
                    self::$buffer[$this->key] = '';
                }

                $this->position = 0;
        }

        return true;
    }

    /**
     * closes the stream
     */
    public function stream_close()
    {
        // nothing to do
    }

    /**
     * read the stream up to $count bytes
     *
     * @param   int  $count  amount of bytes to read
     * @return  string
     */
    public function stream_read($count)
    {
        $bytes           = substr(self::$buffer[$this->key], $this->position, $count);
        $this->position += strlen($bytes);
        return $bytes;
    }

    /**
     * checks whether stream is at end of stream
     *
     * @return  bool
     */
    public function stream_eof()
    {
        return (strlen(self::$buffer[$this->key]) === $this->position);
    }

    /**
     * writes data into the stream
     *
     * @param   string  $data
     * @return  int     amount of bytes written
     */
    public function stream_write($data)
    {
        $dataLen                  = strlen($data);
        self::$buffer[$this->key] = substr(self::$buffer[$this->key], 0, $this->position) . $data . substr(self::$buffer[$this->key], $this->position + $dataLen);
        $this->position          += $dataLen;
        return $dataLen;
    }

    /**
     * returns the current position of the stream
     *
     * @return  int
     */
    public function stream_tell()
    {
        return $this->position;
    }

    /**
     * seeks to the given offset
     *
     * @param   int  $offset
     * @param   int  $whence
     * @return  bool
     */
    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                $this->position = $offset;
                return true;

            case SEEK_CUR:
                $this->position += $offset;
                return true;

            case SEEK_END:
                $this->position = strlen(self::$buffer[$this->key]) + $offset;
                return true;

            default:
                // intentionally empty
        }

        return false;
    }

    /**
     * remove the data under the given path
     *
     * @param   string  $path
     * @return  bool
     */
    public function unlink($path)
    {
        $key = self::parsePath($path);
        if (isset(self::$buffer[$key]) === true) {
            unset(self::$buffer[$key]);
            clearstatcache();
            return true;
        }

        return false;
    }

    /**
     * returns status of stream
     *
     * @return  array
     */
    public function stream_stat()
    {
        return array(2      => 0100000 + octdec(0777),
                     4      => 0,
                     5      => 0,
                     7      => strlen(self::$buffer[$this->key]),
                     'mode' => 0100000 + octdec(0777),
                     'uid'  => 0,
                     'gid'  => 0,
                     'size' => strlen(self::$buffer[$this->key])
               );
    }

    /**
     * returns status of url
     *
     * @param   string      $path  path of url to return status for
     * @return  array|bool  false if $path does not exist, else
     */
    public function url_stat($path)
    {

        $key = self::parsePath($path);
        if (isset(self::$buffer[$key]) === true) {
            return array(2      => 0100000,
                         4      => 0,
                         5      => 0,
                         7      => strlen(self::$buffer[$key]),
                         'mode' => 0100000,
                         'uid'  => 0,
                         'gid'  => 0,
                         'size' => strlen(self::$buffer[$key])
                   );
        }

        return false;
    }

    /**
     * parses buffer key out of path
     *
     * @param   string  $path
     * @return  string
     */
    protected static function parsePath($path)
    {
        list($key) = sscanf($path, self::SCHEME . '://%s');
        return $key;
    }
}
?>