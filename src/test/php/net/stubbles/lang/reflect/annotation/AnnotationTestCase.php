<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect\annotation;
use net\stubbles\lang\reflect\ReflectionClass;
/**
 * Test for net\stubbles\lang\reflect\annotation\Annotation.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  bug252
 */
class AnnotationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  Annotation
     */
    protected $annotation;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->annotation = new Annotation('annotationName');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\MethodNotSupportedException
     */
    public function callUndefinedMethodThrowsUnsupportedMethodException()
    {
        $this->annotation->invalid();
    }

    /**
     * @test
     */
    public function returnsSpecialValueForAllMethodCallsWithGet()
    {
        $this->annotation->setValue('bar');
        $this->assertEquals('bar',
                            $this->annotation->getFoo()
        );
        $this->assertEquals('bar',
                            $this->annotation->getOther()
        );
    }

    /**
     * @test
     */
    public function returnsSpecialValueForAllMethodCallsWithIs()
    {
        $this->annotation->setValue(true);
        $this->assertTrue($this->annotation->isFoo());
        $this->assertTrue($this->annotation->isOther());
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\MethodNotSupportedException
     */
    public function throwsUnsupportedMethodExceptionForMethodCallsWithoutGetOrIsOnSpecialValue()
    {
        $this->annotation->setValue('bar');
        $this->annotation->invalid();
    }

    /**
     * @test
     * @group  value_by_name
     * @since   1.7.0
     */
    public function returnsFalseOnCheckForUnsetProperty()
    {
        $this->assertFalse($this->annotation->hasValueByName('foo'));
    }

    /**
     * @test
     * @group  value_by_name
     * @since   1.7.0
     */
    public function returnsTrueOnCheckForSetProperty()
    {
        $this->annotation->foo = 'hello';
        $this->assertTrue($this->annotation->hasValueByName('foo'));
    }

    /**
     * @test
     * @group  value_by_name
     * @since   1.7.0
     */
    public function returnsNullForUnsetProperty()
    {
        $this->assertNull($this->annotation->getValueByName('foo'));
    }

    /**
     * @test
     * @group  value_by_name
     * @since   1.7.0
     */
    public function returnsValueForSetProperty()
    {
         $this->annotation->foo = 'hello';
        $this->assertEquals('hello', $this->annotation->getValueByName('foo'));
    }

    /**
     * @test
     */
    public function returnsNullForUnsetGetProperty()
    {
        $this->assertNull($this->annotation->getFoo());
    }

    /**
     * @test
     */
    public function returnsFalseForUnsetBooleanProperty()
    {
        $this->assertFalse($this->annotation->isFoo());
    }

    /**
     * @test
     */
    public function returnsValueOfGetProperty()
    {
        $this->annotation->foo = 'bar';
        $this->assertEquals('bar',
                            $this->annotation->getFoo()
        );
    }

    /**
     * @test
     */
    public function returnsFirstArgumentIfGetPropertyNotSet()
    {
        $this->assertEquals('bar',
                            $this->annotation->getFoo('bar')
        );
    }

    /**
     * @test
     */
    public function returnsValueOfBooleanProperty()
    {
        $this->annotation->foo = true;
        $this->assertTrue($this->annotation->isFoo());
    }

    /**
     * @test
     */
    public function returnTrueForValueCheckIfValueSet()
    {
        $this->annotation->setValue('bar');
        $this->assertTrue($this->annotation->hasValue());
    }

    /**
     * @test
     */
    public function returnFalseForValueCheckIfValueNotSet()
    {
        $this->assertFalse($this->annotation->hasValue());
    }

    /**
     * @test
     */
    public function returnFalseForValueCheckIfAnotherPropertySet()
    {
        $this->annotation->foo = 'bar';
        $this->assertFalse($this->annotation->hasValue());
    }

    /**
     * @test
     */
    public function returnTrueForPropertyCheckIfPropertySet()
    {
        $this->annotation->foo = 'bar';
        $this->annotation->baz = true;
        $this->assertTrue($this->annotation->hasFoo());
        $this->assertTrue($this->annotation->hasBaz());
    }

    /**
     * @test
     */
    public function returnFalseForPropertyCheckIfPropertyNotSet()
    {
        $this->assertFalse($this->annotation->hasFoo());
        $this->assertFalse($this->annotation->hasBaz());
    }

    /**
     * @test
     */
    public function canAccessPropertyAsMethod()
    {
        $this->annotation->foo = 'bar';
        $this->assertEquals('bar',
                            $this->annotation->foo()
        );
        $this->annotation->baz = true;
    }

    /**
     * @test
     */
    public function canAccessBooleanPropertyAsMethod()
    {
        $this->annotation->foo = true;
        $this->assertTrue($this->annotation->foo());
    }

    /**
     * @link  http://stubbles.net/ticket/63
     * @test
     */
    public function reflectionClassInstancesAreRestoredAfterUnserialize()
    {
        $this->annotation->foo = new ReflectionClass($this);
        $this->assertStringStartsWith('/**', unserialize(serialize($this->annotation))->getFoo()->getDocComment());
    }
}
?>