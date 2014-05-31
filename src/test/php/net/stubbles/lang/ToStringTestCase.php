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
use org\stubbles\test\lang\SomeObject1;
use org\stubbles\test\lang\SomeObject2;
use org\stubbles\test\lang\SomeObject3;
use org\stubbles\test\lang\SomeObject4;
/**
 * Tests for net\stubbles\lang\__toString().
 *
 * @since  3.1.0
 * @group  lang
 * @group  lang_core
 */
class ToStringTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @type  SomeObject1
     */
    protected $someObject1;
    /**
     * instance 2 to be used for tests
     *
     * @type  SomeObject2
     */
    protected $someObject2;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->someObject1 = new SomeObject1();
        $this->someObject2 = new SomeObject2();
        $this->someObject2->baseObject = $this->someObject1;
    }

    /**
     * @test
     */
    public function toStringResult()
    {
        $baseObject3 = new SomeObject3($this->someObject1, $this->someObject2, 'foo');
        $baseObject3->aResource = fopen(__FILE__, 'rb');
        $this->assertEquals('org\stubbles\\test\lang\SomeObject3 {
    foo(org\stubbles\\test\lang\SomeObject1): org\stubbles\\test\lang\SomeObject1 {
        bar(integer): 5
    }
    bar(org\stubbles\\test\lang\SomeObject2): org\stubbles\\test\lang\SomeObject2 {
        baseObject(org\stubbles\\test\lang\SomeObject1): org\stubbles\\test\lang\SomeObject1 {
            bar(integer): 5
        }
        foo(string): bar
    }
    baz(string): foo
    aResource(resource[stream]): resource
}
',
                            (string) $baseObject3
        );
        fclose($baseObject3->aResource);
    }

    /**
     * @test
     */
    public function stringRepresentationWithNoProperties()
    {
        $this->assertEquals('org\stubbles\\test\lang\SomeObject4 {
}
',
                            __toString(new SomeObject4())
        );
    }

    /**
     * @test
     */
    public function toStringWithArrayProperty()
    {
        $object = new SomeObject1();
        $object->foo = array('bar' => 'baz');
        $this->assertEquals('org\stubbles\\test\lang\SomeObject1 {
    bar(integer): 5
    foo(array): [..](1)
}
',
                            (string) $object
        );
    }

    /**
     * @test
     */
    public function toStringWithSimpleDataType()
    {
        $this->assertEquals('{
    (integer): 5
}
',
                            __toString(5)
        );
    }
}
