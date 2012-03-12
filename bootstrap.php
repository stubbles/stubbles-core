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
}
?>