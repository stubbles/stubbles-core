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
 */
class stub1BaseObject extends BaseObject
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
 */
class stub2BaseObject extends BaseObject
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
 * @since  2.0.0
 */
class stub3BaseObject extends BaseObject
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
     * @param  stub1BaseObject  $foo
     * @param  stub2BaseObject  $bar
     * @param  mixed                $baz
     */
    public function __construct(stub1BaseObject $foo, stub2BaseObject $bar, $baz)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
    }

    /**
     * returns foo
     *
     * @return  stub1BaseObject
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * returns bar
     *
     * @return  stub2BaseObject
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
 * @since  2.0.0
 */
class stub4BaseObject extends BaseObject
{
    // intentionally empty
}
/**
 * Tests for net\stubbles\lang\BaseObject.
 *
 * @group  lang
 * @group  lang_core
 */
class BaseObjectTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @type  BaseObject
     */
    protected $baseObject1;
    /**
     * instance 2 to be used for tests
     *
     * @type  BaseObject
     */
    protected $baseObject2;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->baseObject1 = new stub1BaseObject();
        $this->baseObject2 = new stub2BaseObject();
        $this->baseObject2->baseObject = $this->baseObject1;
    }

    /**
     * @test
     * @group  bug162
     * @see    http://stubbles.net/ticket/162
     * @since  2.0.0
     */
    public function staticConstructionForClassWithoutMagicConstructMethod()
    {
        $this->assertInstanceOf('net\\stubbles\\lang\\stub1BaseObject',
                                stub1BaseObject::newInstance()
        );
    }

    /**
     * @test
     * @group  bug162
     * @see    http://stubbles.net/ticket/162
     * @since  2.0.0
     */
    public function staticConstructionForClassWithMagicConstructMethodWithoutArguments()
    {
        $this->assertInstanceOf('net\\stubbles\\lang\\stub2BaseObject',
                                stub2BaseObject::newInstance()
        );
    }

    /**
     * @test
     * @group  bug162
     * @see    http://stubbles.net/ticket/162
     * @since  2.0.0
     */
    public function staticConstructionPassesParameters()
    {
        $BaseObject3 = stub3BaseObject::newInstance($this->baseObject1, $this->baseObject2, 303);
        $this->assertInstanceOf('net\\stubbles\\lang\\stub3BaseObject',
                                $BaseObject3
        );
        $this->assertSame($this->baseObject1, $BaseObject3->getFoo());
        $this->assertSame($this->baseObject2, $BaseObject3->getBar());
        $this->assertEquals(303, $BaseObject3->getBaz());
    }

    /**
     * @test
     */
    public function getClassReturnsReflectorForClass()
    {
        $refObject = $this->baseObject1->getClass();
        $this->assertInstanceOf('net\\stubbles\\lang\\reflect\\ReflectionObject',
                                $refObject
        );
        $this->assertEquals('net\\stubbles\\lang\\stub1BaseObject',
                            $refObject->getName()
       );
    }

    /**
     * @test
     */
    public function classInstanceIsEqualToItself()
    {
        $this->assertTrue($this->baseObject1->equals($this->baseObject1));
        $this->assertTrue($this->baseObject2->equals($this->baseObject2));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToInstanceOfOtherClass()
    {
        $this->assertFalse($this->baseObject1->equals($this->baseObject2));
        $this->assertFalse($this->baseObject2->equals($this->baseObject1));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToOtherInstanceOfSameClass()
    {
        $this->assertFalse($this->baseObject1->equals(new stub1BaseObject()));
        $this->assertFalse($this->baseObject2->equals(new stub2BaseObject()));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToString()
    {
        $this->assertFalse($this->baseObject1->equals('foo'));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNumber()
    {
        $this->assertFalse($this->baseObject1->equals(6));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanTrue()
    {
        $this->assertFalse($this->baseObject1->equals(true));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanFalse()
    {
        $this->assertFalse($this->baseObject1->equals(false));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNull()
    {
        $this->assertFalse($this->baseObject1->equals(null));
    }

    /**
     * @test
     */
    public function toStringResult()
    {
        $baseObject3 = new stub3BaseObject($this->baseObject1, $this->baseObject2, 'foo');
        $baseObject3->aResource = fopen(__FILE__, 'rb');
        $this->assertEquals('net\\stubbles\\lang\\stub3BaseObject {
    foo(net\\stubbles\\lang\\stub1BaseObject): net\\stubbles\\lang\\stub1BaseObject {
        bar(integer): 5
    }
    bar(net\\stubbles\\lang\\stub2BaseObject): net\\stubbles\\lang\\stub2BaseObject {
        baseObject(net\\stubbles\\lang\\stub1BaseObject): net\\stubbles\\lang\\stub1BaseObject {
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
        $this->assertEquals('net\\stubbles\\lang\\stub4BaseObject {
}
',
                            BaseObject::getStringRepresentationOf(new stub4BaseObject())
        );
    }
}
?>