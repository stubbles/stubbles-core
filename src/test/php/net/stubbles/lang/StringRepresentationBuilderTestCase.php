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
/**
 * Helper class for the test.
 *
 * @since  3.0.0
 */
class SomeObject1 extends BaseObject
{
    /**
     * a property
     *
     * @type  int
     */
    protected $bar = 5;
}
/**
 * Helper class for the test.
 *
 * @since  3.0.0
 */
class SomeObject2 extends BaseObject
{
    /**
     * a property
     *
     * @type  stubObject
     */
    public $baseObject;
    /**
     * a property
     *
     * @type  string
     */
    private $foo = 'bar';

    /**
     * constructor
     *
     * @since  2.0.0
     */
    public function __construct()
    {
        // intentionally empty
    }
}
/**
 * Helper class for the test.
 *
 * @since  3.0.0
 */
class SomeObject3 extends BaseObject
{
    /**
     * a property
     *
     * @type  stub1BaseObject
     */
    protected $foo;
    /**
     * a property
     *
     * @type  stub2BaseObject
     */
    protected $bar;
    /**
     * a property
     *
     * @type  mixed
     */
    protected $baz;

    /**
     *
     * @param  SomeObject1  $foo
     * @param  SomeObject2  $bar
     * @param  mixed                $baz
     */
    public function __construct(SomeObject1 $foo, SomeObject2 $bar, $baz)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
    }

    /**
     * returns foo
     *
     * @return  SomeObject1
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * returns bar
     *
     * @return  SomeObject2
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * returns baz
     *
     * @return  mixed
     */
    public function getBaz()
    {
        return $this->baz;
    }
}
/**
 * Helper class for the test.
 *
 * @since  3.0.0
 */
class SomeObject4 implements Clonable
{
    // intentionally empty
}
/**
 * Tests for net\stubbles\lang\StringRepresentationBuilder.
 *
 * @since  3.0.0
 * @group  lang
 * @group  lang_core
 */
class StringRepresentationBuilderTestCase extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals('net\\stubbles\\lang\\SomeObject3 {
    foo(net\\stubbles\\lang\\SomeObject1): net\\stubbles\\lang\\SomeObject1 {
        bar(integer): 5
    }
    bar(net\\stubbles\\lang\\SomeObject2): net\\stubbles\\lang\\SomeObject2 {
        baseObject(net\\stubbles\\lang\\SomeObject1): net\\stubbles\\lang\\SomeObject1 {
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
        $this->assertEquals('net\\stubbles\\lang\\SomeObject4 {
}
',
                            StringRepresentationBuilder::buildFrom(new SomeObject4())
        );
    }

    /**
     * @test
     */
    public function toStringWithArrayProperty()
    {
        $object = new SomeObject1();
        $object->foo = array('bar' => 'baz');
        $this->assertEquals('net\\stubbles\\lang\\SomeObject1 {
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
                            StringRepresentationBuilder::buildFrom(5)
        );
    }
}

