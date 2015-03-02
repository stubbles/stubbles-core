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
use stubbles\lang\reflect;
/**
 * Tests for stubbles\lang\errorhandler\ExceptionLogger.
 *
 * @group  lang
 * @group  lang_errorhandler
 * @since  3.3.0
 */
class ExceptionLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ExceptionLogger
     */
    private $exceptionLogger;
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
        $this->root            = vfsStream::setup();
        $this->exceptionLogger = new ExceptionLogger(vfsStream::url('root'));
    }

    /**
     * @test
     * @since  3.3.1
     */
    public function annotationsPresentOnConstructor()
    {
        $annotations = reflect\annotationsOfConstructor($this->exceptionLogger);
        $this->assertTrue($annotations->contain('Inject'));
        $this->assertTrue($annotations->contain('Named'));
        $this->assertEquals(
                'stubbles.project.path',
                $annotations->named('Named')[0]->getName()
        );
    }

    /**
     * @test
     */
    public function logsExceptionData()
    {
        $this->exceptionLogger->log(new \Exception('exception message'));
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
    public function logsExceptionDataOfChainedAndCause()
    {
        $exception = new \stubbles\lang\exception\Exception('chained exception', new \Exception('exception message'), 303);
        $line      = __LINE__ - 1;

        $this->exceptionLogger->log($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log'));
        $this->assertEquals('|stubbles\lang\exception\Exception|chained exception|' . __FILE__ . '|' . $line . '|Exception|exception message|' . __FILE__ . '|' . $line . "\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log')
                                              ->getContent(),
                                   19
                            )
        );
    }

    /**
     * @test
     */
    public function createsLogDirectoryWithDefaultModeIfNotExists()
    {
        $exception = new \Exception('exception message');
        $line      = __LINE__ - 1;

        $this->exceptionLogger->log($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        $this->assertEquals(0700, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }

    /**
     * @test
     */
    public function createsLogDirectoryWithChangedModeIfNotExists()
    {
        $exception = new \Exception('exception message');
        $line      = __LINE__ - 1;

        $this->exceptionLogger->setFilemode(0777)->log($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        $this->assertEquals(0777, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }
}
