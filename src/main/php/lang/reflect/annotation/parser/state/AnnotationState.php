<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation\parser\state;
/**
 * Interface for an annotation parser state.
 *
 * @internal
 */
interface AnnotationState
{
    /**
     * parser is inside the standard docblock
     */
    const DOCBLOCK             = 0;
    /**
     * parser is inside an annotation
     */
    const ANNOTATION           = 2;
    /**
     * parser is inside an annotation name
     */
    const ANNOTATION_NAME      = 3;
    /**
     * parser is inside an annotation type
     */
    const ANNOTATION_TYPE      = 4;
    /**
     * parser is inside the annotation params
     */
    const PARAMS               = 5;
    /**
     * parser is inside an annotation param name
     */
    const PARAM_NAME           = 6;
    /**
     * parser is inside an annotation param value
     */
    const PARAM_VALUE          = 7;
    /**
     * parser is inside a argument declaration
     */
    const ARGUMENT             = 8;
    /**
     * parser is inside an enclosed annotation param value
     */
    const PARAM_VALUE_ENCLOSED = 9;

    /**
     * mark this state as the currently used state
     */
    public function selected();

    /**
     * returns list of tokens that signal state change
     *
     * @return  string[]
     */
    public function signalTokens();

    /**
     * processes a token
     *
     * @param   string  $word          parsed word to be processed
     * @param   string  $currentToken  current token that signaled end of word
     * @param   string  $nextToken     next token after current token
     * @return  bool
     */
    public function process($word, $currentToken, $nextToken);
}
