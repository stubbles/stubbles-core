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
 * Parser is inside the annotation argument.
 *
 * @internal
 */
class AnnotationArgumentState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * returns list of tokens that signal state change
     *
     * @return  string[]
     */
    public function signalTokens()
    {
        return ['}'];
    }

    /**
     * processes a token
     *
     * @param   string  $word          parsed word to be processed
     * @param   string  $currentToken  current token that signaled end of word
     * @param   string  $nextToken     next token after current token
     * @return  bool
     * @throws  \ReflectionException
     */
    public function process($word, $currentToken, $nextToken)
    {
        if (strlen($word) > 0) {
            if (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $word) == false) {
                throw new \ReflectionException('Annotation argument may contain letters, underscores and numbers, but contains an invalid character.');
            }

            $this->parser->markAsParameterAnnotation($word);
        }

        $this->parser->changeState(AnnotationState::ANNOTATION);
        return true;
    }
}
