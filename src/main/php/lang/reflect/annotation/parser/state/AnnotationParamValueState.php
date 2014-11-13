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
 * Parser is inside an annotation param value.
 *
 * @internal
 */
class AnnotationParamValueState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * returns list of tokens that signal state change
     *
     * @return  string[]
     */
    public function signalTokens()
    {
        return ["'", '"', ',', ')'];
    }

    /**
     * processes a token
     *
     * @param   string  $word          parsed word to be processed
     * @param   string  $currentToken  current token that signaled end of word
     * @param   string  $nextToken     next token after current token
     * @return  bool
     */
    public function process($word, $currentToken, $nextToken)
    {
        if (strlen($word) === 0 && ('"' === $currentToken || "'" === $currentToken)) {
            $this->parser->changeState(AnnotationState::PARAM_VALUE_ENCLOSED, $currentToken, $nextToken);
        } elseif (',' === $currentToken) {
            $this->parser->setAnnotationParamValue($word);
            $this->parser->changeState(AnnotationState::PARAMS);
        } elseif (')' === $currentToken) {
            $this->parser->setAnnotationParamValue($word);
            $this->parser->changeState(AnnotationState::DOCBLOCK);
        }

        return true;
    }
}
