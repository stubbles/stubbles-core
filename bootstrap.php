<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles;
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
 */
class Bootstrap
{
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
     * run an application
     *
     * @param  string  $appClass  full qualified class name of the app to run
     * @since  1.7.0
     */
    public static function run($appClass)
    {
        net\stubbles\ioc\App::createInstance($appClass, __DIR__)
                            ->run();
    }
}
?>