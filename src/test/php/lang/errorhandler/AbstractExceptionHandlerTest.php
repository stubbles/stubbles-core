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
     * @type  \stubbles\lang\errorhandler\AbstractExceptionHandler
     */
    private $exceptionHandler;
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
        $this->root             = vfsStream::setup();
        $this->exceptionHandler = NewInstance::of(
                'stubbles\lang\errorhandler\AbstractExceptionHandler',
                [vfsStream::url('root')]
        )->mapCalls(['header' => false, 'createResponseBody' => false, 'writeBody' => false]);
    }

    /**
     * @test
     */
    public function loggingDisabledFillsResponseOnly()
    {
        $this->exceptionHandler->disableLogging()
                ->handleException(new \Exception());
        assertEquals(0, $this->exceptionHandler->callsReceivedFor('log'));
        assertEquals(1, $this->exceptionHandler->callsReceivedFor('header'));
        assertEquals(1, $this->exceptionHandler->callsReceivedFor('createResponseBody'));
        assertEquals(1, $this->exceptionHandler->callsReceivedFor('writeBody'));
    }

    /**
     * @test
     */
    public function handleExceptionLogsExceptionData()
    {
        $this->exceptionHandler->handleException(new \Exception('exception message'));
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

        $this->exceptionHandler->handleException($exception);
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
        $this->exceptionHandler->handleException($exception);
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
        $this->exceptionHandler->setFilemode(0777)->handleException($exception);
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
