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
use org\bovigo\vfs\vfsStream;
/**
 * Tests for stubbles\lang\errorhandler\AbstractExceptionHandler.
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class AbstractExceptionHandlerTest extends \PHPUnit_Framework_TestCase
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
        $this->abstractExceptionHandler = $this->getMock(
                'stubbles\lang\errorhandler\AbstractExceptionHandler',
                ['header', 'createResponseBody', 'writeBody'],
                [vfsStream::url('root')]
        );
    }

    /**
     * @test
     */
    public function loggingDisabledFillsResponseOnly()
    {
        $abstractExceptionHandler = $this->getMock(
                'stubbles\lang\errorhandler\AbstractExceptionHandler',
                ['log', 'header', 'createResponseBody', 'writeBody'],
                [vfsStream::url('root')]
        );
        $abstractExceptionHandler->expects(never())->method('log');
        $abstractExceptionHandler->expects(once())->method('header');
        $abstractExceptionHandler->expects(once())->method('createResponseBody');
        $abstractExceptionHandler->expects(once())->method('writeBody');
        $abstractExceptionHandler->disableLogging()->handleException(new \Exception());
    }

    /**
     * @test
     */
    public function handleExceptionLogsExceptionData()
    {
        $this->abstractExceptionHandler->handleException(new \Exception('exception message'));
        $line = __LINE__ - 1;

        assertTrue(
                $this->root->hasChild(
                        'log/errors/' . date('Y') . '/' . date('m')
                        . '/exceptions-' . date('Y-m-d') . '.log'
                )
        );
        assertEquals(
                '|Exception|exception message|' . __FILE__ . '|' . $line . "||||\n",
                substr(
                        $this->root->getChild(
                                'log/errors/' . date('Y') . '/' . date('m')
                                . '/exceptions-' . date('Y-m-d') . '.log'
                        )->getContent(),
                        19
                )
        );

    }

    /**
     * @test
     */
    public function handleChainedExceptionLogsExceptionDataOfChainedAndCause()
    {
        $exception = new \stubbles\lang\exception\Exception('chained exception', new \Exception('exception message'), 303);
        $line      = __LINE__ - 1;

        $this->abstractExceptionHandler->handleException($exception);
        assertTrue(
                $this->root->hasChild(
                        'log/errors/' . date('Y') . '/' . date('m')
                        . '/exceptions-' . date('Y-m-d') . '.log'
                )
        );
        assertEquals(
                '|stubbles\lang\exception\Exception|chained exception|'
                . __FILE__ . '|' . $line . '|Exception|exception message|'
                . __FILE__ . '|' . $line . "\n",
                substr(
                        $this->root->getChild(
                                'log/errors/' . date('Y') . '/' . date('m')
                                . '/exceptions-' . date('Y-m-d') . '.log'
                        )->getContent(),
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
        $this->abstractExceptionHandler->handleException($exception);
        assertTrue(
                $this->root->hasChild('log/errors/' . date('Y') . '/' . date('m'))
        );
        assertEquals(
                0700,
                $this->root->getChild(
                        'log/errors/' . date('Y') . '/' . date('m')
                )->getPermissions()
        );
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithChangedModeIfNotExists()
    {
        $exception = new \Exception('exception message');
        $this->abstractExceptionHandler->setFilemode(0777)->handleException($exception);
        assertTrue(
                $this->root->hasChild('log/errors/' . date('Y') . '/' . date('m'))
        );
        assertEquals(
                0777,
                $this->root->getChild(
                        'log/errors/' . date('Y') . '/' . date('m')
                )->getPermissions()
        );
    }
}
