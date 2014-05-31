<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect;
/**
 * Test for stubbles\lang\reflect\ReflectionExtension.
 *
 * @group  lang
 * @group  lang_reflect
 */
class ReflectionExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ReflectionExtension
     */
    protected $refExtension;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        // use an extension that is always available, has classes as well as
        // functions and both at the lowest possible number
        $this->refExtension = new ReflectionExtension('date');
    }

    /**
     * @test
     */
    public function equalsItsOwnInstance()
    {
        $this->assertTrue($this->refExtension->equals($this->refExtension));
    }

    /**
     * @test
     */
    public function equalsAnotherInstanceOfSameReflectedExtension()
    {
        $refExtension1 = new ReflectionExtension('date');
        $this->assertTrue($this->refExtension->equals($refExtension1));
        $this->assertTrue($refExtension1->equals($this->refExtension));
    }

    /**
     * @test
     */
    public function doesNotEqualToInstanceOfAnotherReflectedExtension()
    {
        $refExtension2 = new ReflectionExtension('standard');
        $this->assertFalse($this->refExtension->equals($refExtension2));
        $this->assertFalse($refExtension2->equals($this->refExtension));
    }

    /**
     * @test
     */
    public function doesNotEqualAnyOtherType()
    {
        $this->assertFalse($this->refExtension->equals('foo'));
    }

    /**
     * @test
     */
    public function stringRepresentationContainsInformationAboutReflectedExtension()
    {
        $this->assertEquals("stubbles\lang\\reflect\ReflectionExtension[date] {\n}\n",
                            (string) $this->refExtension
        );
    }

    /**
     * @test
     */
    public function returnsListOfReflectionFunction()
    {
        $refFunctions = $this->refExtension->getFunctions();
        foreach ($refFunctions as $refFunction) {
            $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionFunction',
                                    $refFunction
            );
        }
    }

    /**
     * @test
     */
    public function returnsListOfReflectionClass()
    {
        $refClasses = $this->refExtension->getClasses();
        foreach ($refClasses as $refClass) {
            $this->assertInstanceOf('stubbles\lang\\reflect\ReflectionClass',
                                    $refClass
            );
        }
    }
}
