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
use org\bovigo\vfs\vfsStream;
use stubbles\lang\exception\Exception;

use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function stubbles\lang\reflect\annotationsOf;
use function stubbles\lang\reflect\annotationsOfConstructor;
/**
 * Tests for stubbles\environments\ExceptionLogger.
 *
 * @group  environments
 * @since  3.3.0
 */
class ExceptionLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\environments\ExceptionLogger
     */
    private $exceptionLogger;
    /**
     * root path for log files
     *
     * @type  org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;
    /**
     * @type  string
     */
    private static $logPath;
    /**
     * @type  string
     */
    private static $logFile;

    /**
     * set up test environment
     */
    public static function setUpBeforeClass()
    {
        self::$logPath = 'log/errors/' . date('Y') . '/' . date('m');
        self::$logFile = 'exceptions-' . date('Y-m-d') . '.log';
    }

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root            = vfsStream::setup();
        $this->exceptionLogger = new ExceptionLogger(vfsStream::url('root'));
    }

    /**
     * @test
     * @since  5.4.0
     */
    public function annotationsPresentOnClass()
    {
        assertTrue(annotationsOf($this->exceptionLogger)->contain('Singleton'));
    }

    /**
     * @test
     * @since  3.3.1
     */
    public function annotationsPresentOnConstructor()
    {
        $annotations = annotationsOfConstructor($this->exceptionLogger);
        assertTrue($annotations->contain('Named'));
        assert(
                $annotations->named('Named')[0]->getName(),
                equals('stubbles.project.path')
        );
    }

    /**
     * @test
     */
    public function logsExceptionDataCreatesLogfile()
    {
        $this->exceptionLogger->log(new \Exception('exception message'));
        assertTrue($this->root->hasChild(self::$logPath . '/' . self::$logFile));
    }

    /**
     * @test
     */
    public function logsExceptionData()
    {
        $this->exceptionLogger->log(new \Exception('exception message'));
        $line = __LINE__ - 1;
        assert(
                substr(
                        $this->root->getChild(self::$logPath . '/' . self::$logFile)
                                ->getContent(),
                        19
                ),
                equals('|Exception|exception message|' . __FILE__ . '|' . $line . "||||\n")
        );

    }

    /**
     * @test
     */
    public function logsExceptionDataOfChainedAndCause()
    {
        $exception = new Exception('chained exception', new \Exception('exception message'), 303);
        $line      = __LINE__ - 1;
        $this->exceptionLogger->log($exception);
        assert(
                substr(
                        $this->root->getChild(self::$logPath . '/' . self::$logFile)
                                ->getContent(),
                        19
                ),
                equals('|stubbles\lang\exception\Exception|chained exception|' . __FILE__ . '|' . $line . '|Exception|exception message|' . __FILE__ . '|' . $line . "\n")
        );
    }

    /**
     * @test
     */
    public function createsLogDirectoryWithDefaultModeIfNotExists()
    {
        $exception = new \Exception('exception message');
        $this->exceptionLogger->log($exception);
        assert(
                $this->root->getChild(self::$logPath)->getPermissions(),
                equals(0700)
        );
    }

    /**
     * @test
     */
    public function createsLogDirectoryWithChangedModeIfNotExists()
    {
        $exception = new \Exception('exception message');
        $this->exceptionLogger->setFilemode(0777)->log($exception);
        assert(
                $this->root->getChild(self::$logPath)->getPermissions(),
                equals(0777)
        );
    }
}
