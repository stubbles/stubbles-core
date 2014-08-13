<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation;
use stubbles\lang;
class SomethingAnnotated
{
    use Annotated;

    /**
     * some decription
     *
     * @param  mixed  $param
     * @Yo
     */
    public function foo($param)
    {
        // intentionally empty
    }

    protected function getDocComment()
    {
        return lang\reflect($this, 'foo')->getDocComment();
    }

    protected function annotationTarget()
    {
        return 'SomethingAnnotated::foo()#param';
    }
}
/**
 * Special tests for stubbles\lang\reflect\annotation\Annotated.
 *
 * @since  5.0.0
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 */
class AnnotatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This test ensures that param annotations are treated correctly regarding
     * caching, as they are part of the doc comment of the function or method
     * the parameter belongs to.
     *
     * @test
     */
    public function parsingParamAnnotationCachesEmptyListWhenNotPresent()
    {
        $something = new SomethingAnnotated();
        $something->hasAnnotation('Ahoi');

        $methodAnnotations = new Annotations('SomethingAnnotated::foo()');
        $methodAnnotations->add(new Annotation('Yo', 'SomethingAnnotated::foo()'));
        $this->assertEquals(
                $methodAnnotations,
                AnnotationCache::get('SomethingAnnotated::foo()')
        );
        $this->assertEquals(
                new Annotations('SomethingAnnotated::foo()#param'),
                AnnotationCache::get('SomethingAnnotated::foo()#param')
        );
    }
}
