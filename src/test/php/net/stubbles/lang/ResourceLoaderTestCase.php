<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang;
use net\stubbles\Bootstrap;
/**
 * Tests for net\stubbles\lang\ResourceLoader.
 *
 * @since  1.6.0
 * @group  lang
 * @group  lang_core
 */
class ResourceLoaderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ResourceLoader
     */
    protected $resourceLoader;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->resourceLoader = new ResourceLoader();
    }

    /**
     * @test
     */
    public function isAnnotatedAsSingleton()
    {
        $this->assertTrue($this->resourceLoader->getClass()
                                               ->hasAnnotation('Singleton')
        );
    }

    /**
     * @test
     */
    public function returnsListOfAllResourceUrisForExistingFile()
    {
        $this->assertEquals(array(Bootstrap::getRootPath() . DIRECTORY_SEPARATOR
                                    . 'src' . DIRECTORY_SEPARATOR
                                    . 'main' . DIRECTORY_SEPARATOR
                                    . 'resources' . DIRECTORY_SEPARATOR
                                    . 'lang' . DIRECTORY_SEPARATOR . 'stubbles.ini'
                            ),
                            $this->resourceLoader->getResourceUris('lang/stubbles.ini')
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfAllResourceUrisForNonExistingFile()
    {
        $this->assertEquals(array(),
                            $this->resourceLoader->getResourceUris('doesnot.exist')
        );
    }
}
?>