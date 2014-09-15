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
 * Parser is inside the annotation params.
 *
 * @internal
 */
class AnnotationParamsState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * list of tokens that lead to no actions in this state
     *
     * @type  string[]
     */
    private $doNothingTokens = ["\r", "\n", "\t", '*', ' '];

    /**
     * returns list of tokens that signal state change
     *
     * @return  string[]
     */
    public function signalTokens()
    {
        return [' ', ')'];
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
        if (')' === $currentToken) {
            $this->parser->changeState(AnnotationState::DOCBLOCK);
        } elseif (in_array($nextToken, $this->doNothingTokens)) {
            // do nothing
        } else {
            $this->parser->changeState(AnnotationState::PARAM_NAME);
        }

        return true;
    }
}
