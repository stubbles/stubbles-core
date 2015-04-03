<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\lang\errorhandler\ExceptionHandler;
/**
 * Mock class to be used as exception handler.
 */
class ModeExceptionHandler implements ExceptionHandler
{
    /**
     * path to project
     *
     * @type  string
     */
    protected $projectPath;

    /**
     * constructor
     *
     * @param  string  $projectPath  path to project
     */
    public function __construct($projectPath)
    {
        $this->projectPath = $projectPath;
    }

    /**
     * returns path to project
     *
     * @return  string
     */
    public function getProjectPath()
    {
        return $this->projectPath;
    }

    /**
     * handles the exception
     *
     * @param  \Exception  $exception  the uncatched exception
     */
    public function handleException(\Exception $exception) { }
}
/**
 * Tests for stubbles\lang\DefaultMode.
 *
 * Contains all tests which require restoring the previous exception handler.
 *
 * @group       lang
 */
class DefaultModeExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DefaultMode
     */
    protected $defaultMode;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultMode = new DefaultMode('FOO', Mode::CACHE_DISABLED);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        restore_exception_handler();
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function registerExceptionHandlerWithInvalidClassThrowsIllegalArgumentException()
    {
        $this->defaultMode->setExceptionHandler(404);
    }

    /**
     * @test
     */
    public function registerExceptionHandlerWithClassNameReturnsCreatedInstance()
    {
        $this->assertEquals('/tmp',
                            $this->defaultMode->setExceptionHandler('stubbles\lang\ModeExceptionHandler')
                                              ->registerExceptionHandler('/tmp')
                                              ->getProjectPath()
        );
    }

    /**
     * @test
     */
    public function registerExceptionHandlerWithInstanceReturnsGivenInstance()
    {
        $exceptionHandler = new ModeExceptionHandler('/tmp');
        $this->assertSame($exceptionHandler,
                          $this->defaultMode->setExceptionHandler($exceptionHandler)
                                            ->registerExceptionHandler('/tmp')
        );
    }
}
