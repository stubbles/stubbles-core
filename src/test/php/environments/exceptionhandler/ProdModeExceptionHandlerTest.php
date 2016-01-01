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

use function bovigo\callmap\verify;
/**
 * Tests for stubbles\environments\exceptionhandler\ProdModeExceptionHandler.
 *
 * @group  environments
 * @group  environments_exceptionhandler
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
     * @return  stubbles\environments\exceptionhandler\ProdModExceptionHandler
     */
    public function createExceptionHandler($sapi)
    {
        $prodModeExceptionHandler = NewInstance::of(
                ProdModeExceptionHandler::class,
                [vfsStream::url('root'), $sapi]
        )->mapCalls(['header' => false, 'writeBody' => false]);
        return $prodModeExceptionHandler->disableLogging();
    }

    /**
     * @test
     */
    public function createsFallbackErrorMessageIfNoError500FilePresent()
    {
        $exception                = new \Exception('message');
        $prodModeExceptionHandler = $this->createExceptionHandler('cgi');
        $prodModeExceptionHandler->handleException($exception);
        verify($prodModeExceptionHandler, 'header')
                ->received('Status: 500 Internal Server Error');
        verify($prodModeExceptionHandler, 'writeBody')
                ->received('I\'m sorry but I can not fulfill your request. Somewhere someone messed something up.');
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
        $prodModeExceptionHandler->handleException($exception);
        verify($prodModeExceptionHandler, 'header')
                ->received('HTTP/1.1 500 Internal Server Error');
        verify($prodModeExceptionHandler, 'writeBody')
                ->received('An error occurred');
    }
}
