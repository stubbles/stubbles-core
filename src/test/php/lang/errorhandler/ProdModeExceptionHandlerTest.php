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
 * Tests for stubbles\lang\errorhandler\ProdModeExceptionHandler.
 *
 * @group  lang
 * @group  lang_errorhandler
 */
class stubProdModeExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * root path for log files
     *
     * @type  org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root = vfsStream::setup();
    }

    /**
     * creates instance to test
     *
     * @return  stubbles\lang\errorhandler\ProdModExceptionHandler
     */
    public function createExceptionHandler($sapi)
    {
        $prodModeExceptionHandler = $this->getMock('stubbles\lang\errorhandler\ProdModeExceptionHandler',
                                                   ['header', 'writeBody'],
                                                   [vfsStream::url('root'), $sapi]
                                    );
        return $prodModeExceptionHandler->disableLogging();
    }

    /**
     * @test
     */
    public function createsFallbackErrorMessageIfNoError500FilePresent()
    {
        $exception                = new \Exception('message');
        $prodModeExceptionHandler = $this->createExceptionHandler('cgi');
        $prodModeExceptionHandler->expects(once())
                ->method('header')
                ->with(equalTo('Status: 500 Internal Server Error'));
        $prodModeExceptionHandler->expects(once())
                ->method('writeBody')
                ->with(equalTo("I'm sorry but I can not fulfill your request. Somewhere someone messed something up."));
        $prodModeExceptionHandler->handleException($exception);
    }

    /**
     * @test
     */
    public function returnsContentOfError500FileIfPresent()
    {
        vfsStream::newFile('docroot/500.html')
                 ->withContent('An error occurred')
                 ->at($this->root);
        $exception                = new \stubbles\lang\exception\Exception('message');
        $prodModeExceptionHandler = $this->createExceptionHandler('apache');
        $prodModeExceptionHandler->expects(once())
                ->method('header')
                ->with(equalTo('HTTP/1.1 500 Internal Server Error'));
        $prodModeExceptionHandler->expects(once())
                ->method('writeBody')
                ->with(equalTo('An error occurred'));
        $prodModeExceptionHandler->handleException($exception);
    }
}
