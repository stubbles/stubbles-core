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
use org\bovigo\vfs\vfsStream;
/**
 * Tests for net\stubbles\lang\errorhandler\AbstractExceptionHandler.
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class AbstractExceptionHandlerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AbstractExceptionHandler
     */
    private $abstractExceptionHandler;
    /**
     * root path for log files
     *
     * @type  org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;


    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root                     = vfsStream::setup();
        $this->abstractExceptionHandler = $this->getMock('net\stubbles\lang\errorhandler\AbstractExceptionHandler',
                                                         array('header', 'createResponseBody', 'writeBody'),
                                                         array(vfsStream::url('root'))
                                          );
    }

    /**
     * @test
     */
    public function loggingDisabledFillsResponseOnly()
    {
        $abstractExceptionHandler = $this->getMock('net\stubbles\lang\errorhandler\AbstractExceptionHandler',
                                                   array('log', 'header', 'createResponseBody', 'writeBody'),
                                                   array(vfsStream::url('root'))
                                          );
        $abstractExceptionHandler->expects($this->never())->method('log');
        $abstractExceptionHandler->expects($this->once())->method('header');
        $abstractExceptionHandler->expects($this->once())->method('createResponseBody');
        $abstractExceptionHandler->expects($this->once())->method('writeBody');
        $abstractExceptionHandler->disableLogging()->handleException(new \Exception());
    }

    /**
     * @test
     */
    public function handleExceptionLogsExceptionData()
    {
        $this->abstractExceptionHandler->handleException(new \Exception('exception message'));
        $line = __LINE__ - 1;

        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log'));
        $this->assertEquals('|Exception|exception message|' . __FILE__ . '|' . $line . "||||\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log')
                                              ->getContent(),
                                   19
                            )
        );

    }

    /**
     * @test
     */
    public function handleChainedExceptionLogsExceptionDataOfChainedAndCause()
    {
        $exception = new \net\stubbles\lang\exception\Exception('chained exception', new \Exception('exception message'), 303);
        $line      = __LINE__ - 1;

        $this->abstractExceptionHandler->handleException($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log'));
        $this->assertEquals('|net\\stubbles\\lang\\exception\\Exception|chained exception|' . __FILE__ . '|' . $line . '|Exception|exception message|' . __FILE__ . '|' . $line . "\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log')
                                              ->getContent(),
                                   19
                            )
        );
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithDefaultModeIfNotExists()
    {
        $exception = new \Exception('exception message');
        $line      = __LINE__ - 1;

        $this->abstractExceptionHandler->handleException($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        $this->assertEquals(0700, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithChangedModeIfNotExists()
    {
        $exception = new \Exception('exception message');
        $line      = __LINE__ - 1;

        $this->abstractExceptionHandler->setFilemode(0777)->handleException($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        $this->assertEquals(0777, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }
}
