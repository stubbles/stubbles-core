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
use net\stubbles\lang\reflect\MixedType;
use net\stubbles\lang\reflect\ReflectionType;
use net\stubbles\lang\reflect\ReflectionPrimitive;
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\lang\reflect\annotation\AnnotationCache;
use org\bovigo\vfs\vfsStream;
/**
 * Tests for net\stubbles\lang\*().
 *
 * @since  3.1.0
 * @group  lang
 * @group  lang_core
 */
class FunctionsTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * return list of type definitions to test
     *
     * @return  array
     */
    public static function getTypeDefinitions()
    {
        return array(array('string', ReflectionPrimitive::$STRING),
                     array('int', ReflectionPrimitive::$INT),
                     array('integer', ReflectionPrimitive::$INTEGER),
                     array('float', ReflectionPrimitive::$FLOAT),
                     array('double', ReflectionPrimitive::$DOUBLE),
                     array('bool', ReflectionPrimitive::$BOOL),
                     array('boolean', ReflectionPrimitive::$BOOLEAN),
                     array('array', ReflectionPrimitive::$ARRAY),
                     array('mixed', MixedType::$MIXED),
                     array('object', MixedType::$OBJECT)
        );
    }

    /**
     * @since  3.1.1
     * @param  string          $typeName
     * @param  ReflectionType  $expected
     * @dataProvider  getTypeDefinitions
     * @test
     */
    public function typeForDeliversCorrectReflectionTypeForNonClasses($typeName, ReflectionType $expected)
    {
        $this->assertSame($expected, typeFor($typeName));
    }

    /**
     * @since  3.1.1
     * @test
     */
    public function typeForDeliversCorrectReflectionClass()
    {
        $className = get_class($this);
        $refClass  = typeFor($className);
        $this->assertInstanceOf('net\stubbles\lang\reflect\ReflectionClass',
                                $refClass
        );
        $this->assertEquals($className, $refClass->getName());
    }

    /**
     * @since  3.0.0
     * @group  issue_58
     * @test
     */
    public function canEnableFileAnnotationCache()
    {
        $root = vfsStream::setup();
        $file = vfsStream::newFile('annotations.cache')
                         ->withContent($this->createdCachedAnnotation())
                         ->at($root);
        persistAnnotationsInFile($file->url());
        $this->assertTrue(AnnotationCache::has(Annotation::TARGET_CLASS, 'foo', 'bar'));
    }

    /**
     * @since  3.1.0
     * @group  issue_58
     * @test
     */
    public function canEnableOtherAnnotationCache()
    {
        $annotationData = $this->createdCachedAnnotation();
        persistAnnotations(function() use($annotationData)
                           {
                               return $annotationData;
                           },
                           function($data) {}
        );
        $this->assertTrue(AnnotationCache::has(Annotation::TARGET_CLASS, 'foo', 'bar'));
    }

    /**
     * creates a annotation cache with one annotation
     *
     * @return  string
     */
    private function createdCachedAnnotation()
    {
        return serialize(array(Annotation::TARGET_CLASS => array('foo' => array('bar' => new Annotation('bar')))));
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        AnnotationCache::stop();
    }

    /**
     * @test
     */
    public function enforceInternalEncodingSetsInternalEncodingToUtf8()
    {
        enforceInternalEncoding();
        $this->assertEquals('UTF-8', iconv_get_encoding('internal_encoding'));
    }
}
?>