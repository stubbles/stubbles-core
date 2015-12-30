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
use stubbles\test\lang\SomeObject1;
use stubbles\test\lang\SomeObject2;
use stubbles\test\lang\SomeObject3;
use stubbles\test\lang\SomeObject4;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\lang\__toString().
 *
 * @since  3.1.0
 * @group  lang
 * @group  lang_core
 */
class ToStringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @type  SomeObject1
     */
    private $someObject1;
    /**
     * instance 2 to be used for tests
     *
     * @type  SomeObject2
     */
    private $someObject2;

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
        assert(
                (string) $baseObject3,
                equals('stubbles\\test\lang\SomeObject3 {
    foo(stubbles\\test\lang\SomeObject1): stubbles\\test\lang\SomeObject1 {
        bar(integer): 5
    }
    bar(stubbles\\test\lang\SomeObject2): stubbles\\test\lang\SomeObject2 {
        baseObject(stubbles\\test\lang\SomeObject1): stubbles\\test\lang\SomeObject1 {
            bar(integer): 5
        }
        foo(string): bar
    }
    baz(string): foo
    aResource(resource[stream]): resource
}
')
        );
        fclose($baseObject3->aResource);
    }

    /**
     * @test
     */
    public function stringRepresentationWithNoProperties()
    {
        assert(
                __toString(new SomeObject4()),
                equals('stubbles\\test\lang\SomeObject4 {
}
')
        );
    }

    /**
     * @test
     */
    public function toStringWithArrayProperty()
    {
        $object = new SomeObject1();
        $object->foo = ['bar' => 'baz'];
        assert(
                (string) $object,
                equals('stubbles\\test\lang\SomeObject1 {
    bar(integer): 5
    foo(array): [..](1)
}
')
        );
    }

    /**
     * @test
     */
    public function toStringWithSimpleDataType()
    {
        assert(
                __toString(5),
                equals('{
    (integer): 5
}
')
        );
    }
}
