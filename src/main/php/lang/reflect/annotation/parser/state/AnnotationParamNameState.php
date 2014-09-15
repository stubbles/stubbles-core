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
 * Parser is inside an annotation param name.
 *
 * @internal
 */
class AnnotationParamNameState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * returns list of tokens that signal state change
     *
     * @return  string[]
     */
    public function signalTokens()
    {
        return ["'", '"', '=', ')'];
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
        $paramName = trim($word);
        if ("'" === $currentToken || '"' === $currentToken) {
            if (strlen($paramName) > 0) {
                throw new \ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains ' . $currentToken . '. Probably an equal sign is missing: ' . $paramName);
            }

            $this->parser->registerAnnotationParam('__value');
            $this->parser->changeState(AnnotationState::PARAM_VALUE_ENCLOSED, $currentToken, $nextToken);
        } elseif ('=' === $currentToken) {
            if (strlen($paramName) == 0) {
                throw new \ReflectionException('Annotation parameter name has to start with a letter or underscore, but starts with =: ' . $paramName);
            } elseif (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $paramName) == false) {
                throw new \ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains an invalid character: ' . $paramName);
            }

            $this->parser->registerAnnotationParam($paramName);
            $this->parser->changeState(AnnotationState::PARAM_VALUE);
        } elseif (')' === $currentToken) {
            if (strlen($paramName) > 0) {
                $this->parser->registerSingleAnnotationParam($paramName);
            }

            $this->parser->changeState(AnnotationState::DOCBLOCK);
        }

        return true;
    }
}
