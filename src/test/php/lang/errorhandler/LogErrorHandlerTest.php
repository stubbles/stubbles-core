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
 * Tests for stubbles\lang\errorhandler\LogErrorHandler.
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class LogErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LogErrorHandler
     */
    private $logErrorHandler;
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
    public function handleErrorShouldLogTheError()
    {
        $line = __LINE__;
        assertTrue($this->logErrorHandler->handle(E_WARNING, 'message', __FILE__, $line));
        assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/php-error-' . date('Y-m-d') . '.log'));
        assertEquals('|' . E_WARNING . '|E_WARNING|message|' . __FILE__ . '|' . $line . "\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/php-error-' . date('Y-m-d') . '.log')
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
        assertTrue($this->logErrorHandler->handle(E_WARNING, 'message', __FILE__, __LINE__));
        assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        assertEquals(0700, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }

    /**
     * @test
     */
    public function handleErrorShouldLogTheErrorWhenTargetChanged()
    {
        $line = __LINE__;
        assertTrue($this->logErrorHandler->handle(313, 'message', __FILE__, $line)
        );
        assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/php-error-' . date('Y-m-d') . '.log'));
        assertEquals('|313|unknown|message|' . __FILE__ . '|' . $line . "\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/php-error-' . date('Y-m-d') . '.log')
                                       ->getContent(),
                                   19
                            )
        );
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithChangedModeIfNotExists()
    {
        assertTrue($this->logErrorHandler->setFilemode(0777)->handle(E_WARNING, 'message', __FILE__, __LINE__));
        assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        assertEquals(0777, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }
}
