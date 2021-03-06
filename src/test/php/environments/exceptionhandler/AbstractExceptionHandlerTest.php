<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments\exceptionhandler;
use bovigo\callmap\NewInstance;
use org\bovigo\vfs\vfsStream;
use stubbles\lang\exception\Exception;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Tests for stubbles\environments\exceptionhandler\AbstractExceptionHandler.
 *
 * @group  environments
 * @group  environments_exceptionhandler
 */
class AbstractExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\environments\exceptionhandler\AbstractExceptionHandler
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
                AbstractExceptionHandler::class,
                [vfsStream::url('root')]
        )->mapCalls([
                'header'             => null,
                'createResponseBody' => null,
                'writeBody'          => null
        ]);
    }

    /**
     * @test
     */
    public function loggingDisabledDoesNotCreateLogfile()
    {
        $this->exceptionHandler->disableLogging()
                ->handleException(new \Exception());
        assertFalse(
                $this->root->hasChild(
                        'log/errors/' . date('Y') . '/' . date('m')
                        . '/exceptions-' . date('Y-m-d') . '.log'
                )
        );
    }

    /**
     * @test
     */
    public function loggingNotDisabledCreatesLogfile()
    {
        $this->exceptionHandler->handleException(new \Exception());
        assertTrue(
                $this->root->hasChild(
                        'log/errors/' . date('Y') . '/' . date('m')
                        . '/exceptions-' . date('Y-m-d') . '.log'
                )
        );
    }

    /**
     * @test
     */
    public function loggingDisabledFillsResponseOnly()
    {
        $this->exceptionHandler->disableLogging()
                ->handleException(new \Exception());
        verify($this->exceptionHandler, 'header')->wasCalledOnce();
        verify($this->exceptionHandler, 'createResponseBody')->wasCalledOnce();
        verify($this->exceptionHandler, 'writeBody')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function handleExceptionLogsExceptionData()
    {
        $this->exceptionHandler->handleException(new \Exception('exception message'));
        $line = __LINE__ - 1;
        assert(
                substr(
                        $this->root->getChild(
                                'log/errors/' . date('Y') . '/' . date('m')
                                . '/exceptions-' . date('Y-m-d') . '.log'
                        )->getContent(),
                        19
                ),
                equals('|Exception|exception message|' . __FILE__ . '|' . $line . "||||\n")
        );

    }

    /**
     * @test
     */
    public function handleChainedExceptionLogsExceptionDataOfChainedAndCause()
    {
        $exception = new Exception('chained exception', new \Exception('exception message'), 303);
        $line      = __LINE__ - 1;
        $this->exceptionHandler->handleException($exception);
        assert(
                substr(
                        $this->root->getChild(
                                'log/errors/' . date('Y') . '/' . date('m')
                                . '/exceptions-' . date('Y-m-d') . '.log'
                        )->getContent(),
                        19
                ),
                equals(
                        '|stubbles\lang\exception\Exception|chained exception|'
                        . __FILE__ . '|' . $line . '|Exception|exception message|'
                        . __FILE__ . '|' . $line . "\n"
                )
        );
    }

    /**
     * @test
     */
    public function createsLogDirectoryWithDefaultPermissionsIfNotExists()
    {
        $exception = new \Exception('exception message');
        $this->exceptionHandler->handleException($exception);
        assert(
                $this->root->getChild(
                        'log/errors/' . date('Y') . '/' . date('m')
                )->getPermissions(),
                equals(0700)
        );
    }

    /**
     * @test
     */
    public function createLogDirectoryWithChangedPermissionsIfNotExists()
    {
        $exception = new \Exception('exception message');
        $this->exceptionHandler->setFilemode(0777)->handleException($exception);
        assert(
                $this->root->getChild(
                        'log/errors/' . date('Y') . '/' . date('m')
                )->getPermissions(),
                equals(0777)
        );
    }
}
