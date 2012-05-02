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
 * Script to remove all .cache files in a certain directory recursively.
 */
class ClearCache
{
    /**
     * clears cache folder
     *
     * Returns amount of removed cache files.
     *
     * @param   string  $cacheDir
     * @return  int
     */
    public static function run($cacheDir)
    {
        $dirIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($cacheDir),
                                                      \RecursiveIteratorIterator::CHILD_FIRST
                       );
        $removed     = 0;
        foreach ($dirIterator as $file) {
            /* @var  $file  \SplFileInfo */
            if (self::isCacheFile($file)) {
                if (unlink($file->getPathname())) {
                    $removed++;
                }
            }
        }

        return $removed;
    }

    /**
     * checks whether given file is a cache file
     *
     * @param   SplFileInfo $file
     * @return  bool
     */
    private static function isCacheFile(\SplFileInfo $file)
    {
        if (!$file->isFile()) {
            return false;
        }

        return pathinfo($file->getPathname(), PATHINFO_EXTENSION) ===  'cache';
    }
}
?>