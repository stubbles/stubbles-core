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
/**
 * Test for stubbles\lang\reflect\annotation\Annotations.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @since  5.0.0
 */
class AnnotationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\lang\reflect\annotation\Annotations
     */
    private $annotations;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->annotations = new Annotations('someTarget');
    }

    /**
     * @test
     */
    public function doNotContainNonAddedAnnotation()
    {
        assertFalse($this->annotations->contain('foo'));
    }

    /**
     * @test
     */
    public function containsAddedAnnotation()
    {
        assertTrue(
                $this->annotations->add(new Annotation('foo'))
                                  ->contain('foo')
        );
    }

    /**
     * @test
     */
    public function containsMoreThanOneAnnotation()
    {
        assertTrue(
                $this->annotations->add(new Annotation('foo'))
                                  ->add(new Annotation('foo'))
                                  ->contain('foo')
        );
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function firstNamedReturnsFirstAddedAnnotationWithThisName()
    {
        $first = new Annotation('foo');
        assertSame(
                $first,
                $this->annotations->add($first)
                                  ->add(new Annotation('foo'))
                                  ->firstNamed('foo')
        );
    }

    /**
     * @test
     * @expectedException  ReflectionException
     * @since  5.3.0
     */
    public function firstNamedThrowsReflectionExceptionIfNoSuchAnnotationExists()
    {
        $this->annotations->firstNamed('foo');
    }

    /**
     * @test
     */
    public function returnsEmptyListIfNoneOfThisTypeAdded()
    {
        assertEquals(
                [],
                $this->annotations->named('foo')
        );
    }

    /**
     * @test
     */
    public function returnsAllAnnotationsOfThisType()
    {
        assertEquals(
                [new Annotation('foo'),
                 new Annotation('foo')
                ],
                $this->annotations->add(new Annotation('foo'))
                                  ->add(new Annotation('bar'))
                                  ->add(new Annotation('foo'))
                                  ->named('foo')
        );
    }

    /**
     * @test
     */
    public function returnsAllAnnotations()
    {
        assertEquals(
                [new Annotation('foo'),
                 new Annotation('foo'),
                 new Annotation('bar')
                ],
                $this->annotations->add(new Annotation('foo'))
                                  ->add(new Annotation('bar'))
                                  ->add(new Annotation('foo'))
                                  ->all()
        );
    }

    /**
     * @test
     */
    public function canIteratorOverAllAnnotations()
    {
        $this->annotations->add(new Annotation('foo'))
                          ->add(new Annotation('bar'))
                          ->add(new Annotation('foo'));
        $types = [];
        foreach ($this->annotations as $annotation) {
            $types[] = $annotation->getAnnotationName();
        }

        assertEquals(['foo', 'foo', 'bar'], $types);
    }

}
