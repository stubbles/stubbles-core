<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments;
use stubbles\Environment;
use stubbles\lang\errorhandler\DisplayExceptionHandler;
/**
 * Represents development environment.
 *
 * Cache is disabled, and erors as well as exceptions will be displayed.
 *
 * @api
 * @since  7.0.0
 */
class Development implements Environment
{
    use Handler;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setExceptionHandler(DisplayExceptionHandler::class);
    }

    /**
     * returns the name of the mode
     *
     * @return  string
     */
    public function name()
    {
        return 'DEV';
    }

    /**
     * checks whether cache is enabled or not
     *
     * @return  bool
     */
    public function isCacheEnabled()
    {
        return Environment::CACHE_DISABLED;
    }
}
