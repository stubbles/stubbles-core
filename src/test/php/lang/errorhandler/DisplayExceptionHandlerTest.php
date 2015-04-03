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
        $displayExceptionHandler = $this->getMock(
                'stubbles\lang\errorhandler\DisplayExceptionHandler',
                ['header', 'writeBody'],
                ['/tmp', $sapi]
        );
        return $displayExceptionHandler->disableLogging();
    }

    /**
     * @test
     */
    public function writesMessageAndTraceForInternalException()
    {
        $exception = new \Exception('message');
        $displayExceptionHandler = $this->createExceptionHandler('cgi');
        $displayExceptionHandler->expects($this->once())
                ->method('header')
                ->with($this->equalTo('Status: 500 Internal Server Error'));
        $displayExceptionHandler->expects($this->once())
                ->method('writeBody')
                ->with($this->equalTo("message\nTrace:\n" . $exception->getTraceAsString()));
        $displayExceptionHandler->handleException($exception);
    }
}
