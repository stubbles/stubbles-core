<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments\errorhandler;
use org\bovigo\vfs\vfsStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\environments\errorhandler\LogErrorHandler.
 *
 * @group  environments
 * @group  environments_errorhandler
 */
class LogErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\environments\errorhandler\LogErrorHandler
     */
    private $logErrorHandler;
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
        self::$logFile = 'php-error-' . date('Y-m-d') . '.log';
    }

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root            = vfsStream::setup();
        $this->logErrorHandler = new LogErrorHandler(vfsStream::url('root'));
    }

    /**
     * @test
     */
    public function isAlwaysResponsible()
    {
        assertTrue($this->logErrorHandler->isResponsible(E_NOTICE, 'foo'));
    }

    /**
     * @test
     */
    public function isNeverSupressable()
    {
        assertFalse($this->logErrorHandler->isSupressable(E_NOTICE, 'foo'));
    }

    /**
     * @test
     */
    public function stopsErrorHandlingWhenHandled()
    {
        assert(
                $this->logErrorHandler->handle(E_WARNING, 'message', __FILE__, __LINE__),
                equals(ErrorHandler::STOP_ERROR_HANDLING)
        );
    }

    /**
     * @test
     */
    public function handleErrorCreatesLogfile()
    {
        $this->logErrorHandler->handle(E_WARNING, 'message', __FILE__, __LINE__);
        assertTrue($this->root->hasChild(self::$logPath . '/' . self::$logFile));
    }

    /**
     * @test
     */
    public function handleErrorShouldLogTheError()
    {
        $line = __LINE__;
        $this->logErrorHandler->handle(E_WARNING, 'message', __FILE__, $line);
        assert(
                substr(
                        $this->root->getChild(self::$logPath . '/' . self::$logFile)
                                ->getContent(),
                        19
                ),
                equals('|' . E_WARNING . '|E_WARNING|message|' . __FILE__ . '|' . $line . "\n")
        );
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithDefaultPermissionsIfNotExists()
    {
        $this->logErrorHandler->handle(E_WARNING, 'message', __FILE__, __LINE__);
        assert(
                $this->root->getChild(self::$logPath)->getPermissions(),
                equals(0700)
        );
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithChangedPermissionsIfNotExists()
    {
        $this->logErrorHandler->setFilemode(0777)
                ->handle(E_WARNING, 'message', __FILE__, __LINE__);
        assert(
                $this->root->getChild(self::$logPath)->getPermissions(),
                equals(0777)
        );
    }
}
