<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\errorhandler;
/**
 * Exception handler for production mode: fills the response with an error document.
 *
 * @internal
 */
class ProdModeExceptionHandler extends AbstractExceptionHandler
{
    /**
     * creates response body with useful data for display
     *
     * @param   Exception  $exception  the uncatched exception
     * @return  string
     */
    protected function createResponseBody(\Exception $exception)
    {
        if (file_exists($this->projectPath . '/docroot/500.html')) {
            return file_get_contents($this->projectPath . '/docroot/500.html');
        }

        return "I'm sorry but I can not fulfill your request. Somewhere someone messed something up.";
    }
}