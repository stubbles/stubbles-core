<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * The contents of this file draw heavily from XP Framework
 * https://github.com/xp-framework/xp-framework
 *
 * Copyright (c) 2001-2014, XP-Framework Team
 * All rights reserved.
 * https://github.com/xp-framework/xp-framework/blob/master/core/src/main/php/LICENCE
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\lang\exception\IllegalAccessException;
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\exception\IllegalStateException;
use stubbles\lang\exception\RuntimeException;
/**
 * SecureString provides a reasonable secure storage for security-sensitive
 * lists of characters, such as passwords.
 *
 * It prevents accidentially revealing them in output, by var_dump()ing,
 * echo()ing, or casting the object to array. All these cases will not
 * show the password, nor the crypt of it.
 *
 * However, it is not safe to consider this implementation secure in a crypto-
 * graphically sense, because it does not care for a very strong encryption,
 * and it does share the encryption key with all instances of it in a single
 * PHP instance.
 *
 * When using this class, you must make sure not to extract the secured string
 * and pass it to a place where an exception might occur, as it might be exposed
 * as method argument.
 *
 * @since  4.0.0
 */
final class SecureString
{
    /**
     * backing: mcrypt
     */
    const BACKING_MCRYPT    = 'mcrypt';
    /**
     * backing: openssl
     */
    const BACKING_OPENSSL   = 'openssl';
    /**
     * backing: base64
     */
    const BACKING_PLAINTEXT = 'base64';
    /**
     * actual storage of encrypted strings
     *
     * @type  array
     */
    private static $store   = [];
    /**
     * callable to encrypt data with before storing it
     *
     * @type  \Closure
     */
    private static $encrypt;
    /**
     * callable to decrypt data with before returning it from store
     *
     * @type  \Closure
     */
    private static $decrypt;
    /**
     * id of instance
     *
     * @type  string
     */
    private $id;
    /**
     * length of secured string
     *
     * @type  int
     */
    private $length;

    /**
     * static initializer
     */
    static function __static()
    {
        if (extension_loaded('mcrypt')) {
            self::useMcryptBacking();
        } elseif (extension_loaded('openssl')) {
            self::useOpenSslBacking();
        } else {
            self::usePlaintextBacking();
        }
    }

    /**
     * select en-/decryption mechanism
     *
     * @param   string  $type
     * @throws  \stubbles\lang\exception\IllegalArgumentException  when given backing is unknown
     * @throws  \stubbles\lang\exception\IllegalStateException     when trying to change the backing while there are still secure strings in the store
     */
    public static function switchBacking($type)
    {
        if (count(self::$store) > 0) {
            throw new IllegalStateException('Can not switch backing while secured strings are stored');
        }

        switch ($type) {
            case self::BACKING_MCRYPT:
                self::useMcryptBacking();
                break;

            case self::BACKING_OPENSSL:
                self::useOpenSslBacking();
                break;

            case self::BACKING_PLAINTEXT:
                self::usePlaintextBacking();
                break;

            case '__none':
                self::$encrypt = function() { throw new \Exception('No backing set'); };
                self::$decrypt = function() { return null; };
                break;

            default:
                throw new IllegalArgumentException('Unknown backing ' . $type);
        }

        return true;
    }

    /**
     * switches backing to mcrypt
     *
     * @throws  \stubbles\lang\exception\RuntimeException  when mcrypt extension not available
     */
    private static function useMcryptBacking()
    {
        if (!extension_loaded('mcrypt')) {
            throw new RuntimeException('Can not use mcrypt backing, extension mcrypt not available');
        }

        $engine   = mcrypt_module_open(MCRYPT_DES, '', 'ecb', '');
        $engineiv = mcrypt_create_iv(mcrypt_enc_get_iv_size($engine), MCRYPT_RAND);
        $key      = substr(md5(uniqid()), 0, mcrypt_enc_get_key_size($engine));
        mcrypt_generic_init($engine, $key, $engineiv);
        self::$encrypt = function($value) use($engine) { return mcrypt_generic($engine, $value); };
        self::$decrypt = function($value) use($engine) { return rtrim(mdecrypt_generic($engine, $value), "\0"); };
    }

    /**
     * switches backing to openssl
     *
     * @throws  \stubbles\lang\exception\RuntimeException  when openssl extension not available
     */
    private static function useOpenSslBacking()
    {
        if (!extension_loaded('openssl')) {
            throw new RuntimeException('Can not use openssl backing, extension openssl not available');
        }

        $key = md5(uniqid());
        $iv  = substr(md5(uniqid()), 0, openssl_cipher_iv_length('des'));
        self::$encrypt = function($value) use ($key, $iv) { return openssl_encrypt($value, 'DES', $key,  0, $iv); };
        self::$decrypt = function($value) use ($key, $iv) { return openssl_decrypt($value, 'DES', $key,  0, $iv); };
    }

    /**
     * switches backing to base64 encoding
     *
     * Of course this still allows to reveal the secured string, but at least
     * it allows to use SecureString transparantly.
     */
    private static function usePlaintextBacking()
    {
        self::$encrypt = function($value) { return base64_encode($value); };
        self::$decrypt = function($value) { return base64_decode($value); };
    }

    /**
     * constructor
     */
    private function __construct()
    {
        $this->id = uniqid('', true);
    }

    /**
     * creates an instance for given characters
     *
     * Please note the given characters are passed as reference and will be
     * blanked out after creation of the instance.
     *
     * @param   string|\stubbles\lang\SecureString  $string  characters to secure
     * @return  \stubbles\lang\SecureString
     */
    public static function create($string)
    {
        if ($string instanceof self) {
            return $string;
        }

        if (empty($string)) {
            throw new IllegalArgumentException('Given string was null or empty, if you explicitly want to create a SecureString with value null use SecureString::forNull()');
        }

        $self = new self();
        try {
            $encrypt = self::$encrypt;
            self::$store[$self->id] = $encrypt($string);
            $self->length           = iconv_strlen($string);
        } catch (\Exception $e) {
            $e = null;
            // This intentionally catches *ALL* exceptions, in order not to fail
            // and produce a stacktrace (containing arguments on the stack that
            // were) supposed to be protected.
            unset(self::$store[$self->id]);
        }

        $string = str_repeat('*', strlen($string));
        $string = null;
        return $self;
    }

    /**
     * explicitly create an instance where the actual string is null
     *
     * @return  \stubbles\lang\SecureString
     */
    public static function forNull()
    {
        $self = new self();
        self::$store[$self->id] = true;
        $self->length = 0;
        return $self;
    }

    /**
     * Destructor; removes references from crypted storage for this instance.
     */
    public function __destruct()
    {
        unset(self::$store[$this->id]);
    }

    /**
     * checks whether actual value is null
     *
     * @return  bool
     */
    public function isNull()
    {
        return true === self::$store[$this->id];
    }

    /**
     * checks if instance contains a string, i.e. encryption did not fail
     *
     * @return  bool
     */
    public function isContained()
    {
        return isset(self::$store[$this->id]);
    }

    /**
     * retrieve secured characters
     *
     * This should be called at the latest possible moment to avoid unneccessary
     * revealing of the value to be intended stored secure.
     *
     * @return  string
     * @throws  \stubbles\lang\exception\IllegalStateException  in case the secure string can not be found
     */
    public function unveil()
    {
        if (!$this->isContained()) {
           throw new IllegalStateException('An error occurred during string encryption.');
        }

        if ($this->isNull()) {
            return null;
        }

        $decrypt = self::$decrypt;
        return $decrypt(self::$store[$this->id]);
    }

    /**
     * returns a substring of the secured string as a new secured string instance
     *
     * @param   int  $start
     * @param   int  $length  optional
     * @return  \stubbles\lang\SecureString
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     * @link    http://php.net/manual/en/function.substr.php
     */
    public function substring($start, $length = null)
    {
        if ($this->isNull()) {
            return $this;
        }

        $substring = substr($this->unveil(), $start, $length);
        if (false === $substring) {
            throw new IllegalArgumentException('Given start offset is out of range');
        }

        return self::create($substring);
    }

    /**
     * returns length of string
     *
     * @return  int
     */
    public function length()
    {
        return $this->length;
    }

    /**
     * prevent serialization
     *
     * @throws  \stubbles\lang\exception\IllegalAccessException
     */
    public function __sleep()
    {
        throw new IllegalAccessException('Cannot serialize instances of ' . get_class($this));
    }

    /**
     * override regular __toString() output
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this) . " {\n}\n";
    }
}
SecureString::__static();
