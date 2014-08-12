<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation\parser;
/**
 * Interface for parsers to parse Java-Style annotations.
 *
 * @internal
 */
interface AnnotationParser
{
    /**
     * change the current state
     *
     * @param  int     $state
     * @param  string  $token  token that should be processed by the state
     */
    public function changeState($state, $token = null);

    /**
     * parse a docblock and return all annotations found
     *
     * @param   string  $docComment
     * @param   string  $target
     * @return  \stubbles\lang\reflect\annotation\Annotations[]
     */
    public function parse($docComment, $target);

    /**
     * register a new annotation
     *
     * @param  string  $name
     */
    public function registerAnnotation($name);

    /**
     * register a new annotation param
     *
     * @param  string  $name
     */
    public function registerAnnotationParam($name);

    /**
     * register single annotation param
     *
     * @param   string  $value  the value of the param
     * @throws  \ReflectionException
     */
    public function registerSingleAnnotationParam($value);

    /**
     * set the annoation param value for the current annotation
     *
     * @param   string  $value  the value of the param
     * @throws  \ReflectionException
     */
    public function setAnnotationParamValue($value);

    /**
     * set the type of the current annotation
     *
     * @param  string  $type  type of the annotation
     */
    public function setAnnotationType($type);

    /**
     * marks the current annotation as being an annotation for a function/method parameter
     *
     * @param  string  $parameterName  name of the parameter
     */
    public function markAsParameterAnnotation($parameterName);
}
