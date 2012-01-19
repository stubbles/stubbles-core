<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams;
/**
 * Interface for decorated input streams.
 */
interface DecoratedInputStream extends InputStream
{
    /**
     * replace current enclosed input stream
     *
     * @param  InputStream  $inputStream
     */
    public function setEnclosedInputStream(InputStream $inputStream);

    /**
     * returns enclosed input stream
     *
     * @return  InputStream
     */
    public function getEnclosedInputStream();
}
?>