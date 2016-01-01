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
use stubbles\environments\errorhandler\DefaultErrorHandler;
use stubbles\environments\exceptionhandler\ProdModeExceptionHandler;
/**
 * Represents production environment.
 *
 * Cache is enabled, and both errors and exceptions will be logged and not
 * displayed.
 *
 * @api
 * @since  7.0.0
 */
class Production implements Environment
{
    use Handler;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setExceptionHandler(ProdModeExceptionHandler::class)
                ->setErrorHandler(DefaultErrorHandler::class);
    }

    /**
     * returns the name of the mode
     *
     * @return  string
     */
    public function name()
    {
        return 'PROD';
    }

    /**
     * checks whether cache is enabled or not
     *
     * @return  bool
     */
    public function isCacheEnabled()
    {
        return Environment::CACHE_ENABLED;
    }
}
