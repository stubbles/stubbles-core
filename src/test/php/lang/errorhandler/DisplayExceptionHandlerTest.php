<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\errorhandler;
use bovigo\callmap\NewInstance;
/**
 * Tests for stubbles\lang\errorhandler\DisplayExceptionHandler.
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class DisplayExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * creates instance to test
     *
     * @return  \stubbles\lang\errorhandler\DisplayExceptionHandler
     */
    public function createExceptionHandler($sapi)
    {
        $displayExceptionHandler = NewInstance::of(
                'stubbles\lang\errorhandler\DisplayExceptionHandler',
                ['/tmp', $sapi]
        )->mapCalls(['header' => false, 'writeBody' => false]);
        return $displayExceptionHandler->disableLogging();
    }

    /**
     * @test
     */
    public function writesMessageAndTraceForInternalException()
    {
        $exception = new \Exception('message');
        $displayExceptionHandler = $this->createExceptionHandler('cgi');
        $displayExceptionHandler->handleException($exception);
        assertEquals(
                ['Status: 500 Internal Server Error'],
                $displayExceptionHandler->argumentsReceivedFor('header')
        );
        assertEquals(
                ["message\nTrace:\n" . $exception->getTraceAsString()],
                $displayExceptionHandler->argumentsReceivedFor('writeBody')
        );
    }
}
