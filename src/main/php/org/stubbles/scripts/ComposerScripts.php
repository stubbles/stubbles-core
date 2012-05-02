<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\stubbles\scripts
 */
namespace org\stubbles\scripts;
/**
 * Contains hooks for composer scripts.
 */
class ComposerScripts
{
    /**
     * root directory to be used
     *
     * @type  string
     */
    private static $rootDir;

    /**
     * script to execute after installation
     *
     * @param  Composer\Script\Event  $event
     */
    public static function postInstall($event)
    {
        $rootDir = self::getRootDir();
        $io      = $event->getIO();
        if (file_exists($rootDir . '/cache/.')) {
            self::clearProjectCache($rootDir, $io);
        } else {
            mkdir($rootDir . '/cache');
        }

        return true;
    }

    /**
     * script to execute after update
     *
     * @param  Composer\Script\Event  $event
     */
    public static function postUpdate($event)
    {
        $rootDir = self::getRootDir();
        $io      = $event->getIO();
        self::clearProjectCache($rootDir, $io);
        return true;
    }

    /**
     * returns root directory of the package
     *
     * @return  string
     */
    private static function getRootDir()
    {
        if (null == self::$rootDir) {
            return getcwd();
        }

        return self::$rootDir;
    }

    /**
     * sets root dir to be used
     *
     * @param  string  $rootDir
     */
    public static function setRootDir($rootDir)
    {
        self::$rootDir = $rootDir;
    }

    /**
     * copies bootstrap.php to project root dir
     *
     * @param  string                   $rootDir
     * @param  Composer\IO\IOInterface  $io
     */
    private static function clearProjectCache($rootDir, $io)
    {
        $removed = ClearCache::run($rootDir . '/cache');
        $filetxt = (1 === $removed) ? ('file') : ('files');
        $io->write('Cleared ' . $removed . ' ' . $filetxt . ' from cache');
    }
}
?>