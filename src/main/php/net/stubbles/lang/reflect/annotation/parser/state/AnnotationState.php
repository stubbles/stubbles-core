<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect\annotation\parser\state;
/**
 * Interface for an annotation parser state.
 */
interface AnnotationState
{
    /**
     * parser is inside the standard docblock
     */
    const DOCBLOCK        = 0;
    /**
     * parser is inside a text within the docblock
     */
    const TEXT            = 1;
    /**
     * parser is inside an annotation
     */
    const ANNOTATION      = 2;
    /**
     * parser is inside an annotation name
     */
    const ANNOTATION_NAME = 3;
    /**
     * parser is inside an annotation type
     */
    const ANNOTATION_TYPE = 4;
    /**
     * parser is inside the annotation params
     */
    const PARAMS          = 5;
    /**
     * parser is inside an annotation param name
     */
    const PARAM_NAME      = 6;
    /**
     * parser is inside an annotation param value
     */
    const PARAM_VALUE     = 7;
    /**
     * parser is inside a argument declaration
     */
    const ARGUMENT        = 8;

    /**
     * mark this state as the currently used state
     */
    public function selected();

    /**
     * processes a token
     *
     * @param   string  $token
     */
    public function process($token);
}
?>