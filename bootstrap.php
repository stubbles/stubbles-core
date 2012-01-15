<?php
if (file_exists(__DIR__ . '/vendor/.composer/autoload.php') === false) {
    die('Please run "composer.phar install" first' . "\n");
}

require __DIR__ . '/vendor/.composer/autoload.php';

/**
 * set internal, input and output encoding
 */
iconv_set_encoding('internal_encoding', 'UTF-8');
if (($ctype = getenv('LC_CTYPE')) || ($ctype = setlocale(LC_CTYPE, 0))) {
    sscanf($ctype, '%[^.].%s', $language, $charset);
    if (is_numeric($charset) === true) {
        $charset = 'CP' . $charset;
    } elseif (null == $charset) {
        $charset = 'iso-8859-1';
    }

    iconv_set_encoding('output_encoding', $charset);
    iconv_set_encoding('input_encoding', $charset);
}
/**
 * The bootstrap class takes care of providing all necessary data required in the bootstrap process.
 *
 * @package  stubbles
 * @version  $Id: bootstrap.php 3296 2011-12-19 16:34:36Z mikey $
 */
/**
 * The bootstrap class takes care of providing all necessary data required in the bootstrap process.
 *
 * @package  stubbles
 */
class stubBootstrap
{
    /**
     * list of source pathes
     *
     * @type  string[]
     */
    private static $sourcePathes;
    /**
     * returns root path of the installation
     *
     * @return  string
     */
    public static function getRootPath()
    {
        return __DIR__;
    }

    /**
     * returns list of source pathes
     *
     * @return  string[]
     */
    public static function getSourcePathes()
    {
        if (null === self::$sourcePathes) {
            $pathes       = array();
            $vendorPathes = require __DIR__ . '/vendor/.composer/autoload_namespaces.php';
            foreach ($vendorPathes as $path) {
                if (substr($path, -13) === '/src/main/php') {
                    $path = str_replace('/src/main/php', '/src/main', $path);
                }

                if (isset($pathes[$path]) === false) {
                    $pathes[$path] = $path;
                }
            }

            self::$sourcePathes = array_values($pathes);
        }

        return self::$sourcePathes;
    }

    /**
     * run an application
     *
     * @param  string  $appClass  full qualified class name of the app to run
     * @since  1.7.0
     */
    public static function run($appClass)
    {
        self::$project = $project;
        stubClassLoader::load('net::stubbles::ioc::stubApp');
        stubApp::createInstance($appClass, __DIR__)
               ->run();
    }
}
?>