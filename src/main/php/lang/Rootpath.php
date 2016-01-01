<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\Rootpath as NewRootpath;
/**
 * Represents the root path within a project.
 *
 * The root path is defined as the path in which the whole application resides.
 * In case the application is inside a phar, it's the directory where the phar
 * is stored.
 *
 * @since  4.0.0
 * @Singleton
 * @deprecated  since 7.0.0, use stubbles\Rootpath instead, will be removed with 8.0.0
 */
class Rootpath extends NewRootpath
{
    // intentionally empty
}

