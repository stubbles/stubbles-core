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
use stubbles\lang\errorhandler\ErrorHandler;
/**
 * Mock class to be used as error handler.
 */
class ModeErrorHandler implements ErrorHandler
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
     * checks whether this error handler is responsible for the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error handler is responsible, else false
     */
    public function isResponsible($level, $message, $file = null, $line = null, array $context = array()) {}

    /**
     * checks whether this error is supressable
     *
     * This method is called in case the level is 0. It decides whether the
     * error has to be handled or if it can be omitted.
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error is supressable, else false
     */
    public function isSupressable($level, $message, $file = null, $line = null, array $context = array()) {}

    /**
     * handles the given error
     *
     * @param   int     $level    level of the raised error
     * @param   string  $message  error message
     * @param   string  $file     optional  filename that the error was raised in
     * @param   int     $line     optional  line number the error was raised at
     * @param   array   $context  optional  array of every variable that existed in the scope the error was triggered in
     * @return  bool    true if error message should populate $php_errormsg, else false
     * @throws  stubException  error handlers are allowed to throw every exception they want to
     */
    public function handle($level, $message, $file = null, $line = null, array $context = array()) {}
}
/**
 * Tests for stubbles\lang\DefaultMode.
 *
 * Contains all tests which require restoring the previous error handler.
 *
 * @group  lang
 * @group  lang_core
 */
class ModeErrorHandlerTestCase extends \PHPUnit_Framework_TestCase
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
        restore_error_handler();
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function registerErrorHandlerWithInvalidClassThrowsIllegalArgumentException()
    {
        $this->defaultMode->setErrorHandler(404);
    }

    /**
     * @test
     */
    public function registerErrorHandlerWithClassNameReturnsCreatedInstance()
    {
        $this->assertEquals('/tmp',
                            $this->defaultMode->setErrorHandler('stubbles\lang\ModeErrorHandler')
                                              ->registerErrorHandler('/tmp')
                                              ->getProjectPath()
        );
    }

    /**
     * @test
     */
    public function registerErrorHandlerWithInstanceReturnsGivenInstance()
    {
        $errorHandler = new ModeErrorHandler('/tmp');
        $this->assertSame($errorHandler,
                          $this->defaultMode->setErrorHandler($errorHandler)
                                            ->registerErrorHandler('/tmp')
        );
    }
}
