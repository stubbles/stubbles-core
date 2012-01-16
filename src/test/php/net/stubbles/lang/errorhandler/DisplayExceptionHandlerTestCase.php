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
 * Tests for net\stubbles\lang\errorhandler\DisplayExceptionHandler.
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class DisplayExceptionHandlerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * creates instance to test
     *
     * @return  net\stubbles\lang\errorhandler\DisplayExceptionHandler
     */
    public function createExceptionHandler($sapi)
    {
        $displayExceptionHandler = $this->getMock('net\stubbles\lang\errorhandler\DisplayExceptionHandler',
                                                  array('header', 'writeBody'),
                                                  array('/tmp', $sapi)
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

    /**
     * assure that stubbles exceptions are handled
     *
     * @test
     */
    public function writesStringRepresentationAndTractForThrowable()
    {
        $exception = new \net\stubbles\lang\exception\Exception('message');
        $displayExceptionHandler = $this->createExceptionHandler('apache');
        $displayExceptionHandler->expects($this->once())
                                ->method('header')
                                ->with($this->equalTo('HTTP/1.1 500 Internal Server Error'));
        $displayExceptionHandler->expects($this->once())
                                ->method('writeBody')
                                ->with($this->equalTo((string) $exception . "\nTrace:\n" . $exception->getTraceAsString()));
        $displayExceptionHandler->handleException($exception);
    }
}
?>