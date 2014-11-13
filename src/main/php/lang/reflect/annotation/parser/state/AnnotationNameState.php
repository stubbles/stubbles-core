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
 * Parser is inside the annotation name.
 *
 * @internal
 */
class AnnotationNameState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * list of forbidden annotation names
     *
     * @type  string[]
     */
    protected $forbiddenAnnotationNames = ['deprecated' => 1,
                                           'example' => 1,
                                           'ignore' => 1,
                                           'internal' => 1,
                                           'link' => 1,
                                           'method' => 1,
                                           'package' => 1,
                                           'param' => 1,
                                           'property' => 1,
                                           'property-read' => 1,
                                           'property-write' => 1,
                                           'return' => 1,
                                           'see' => 1,
                                           'since' => 1,
                                           'static' => 1,
                                           'subpackage' => 1,
                                           'throws' => 1,
                                           'todo' => 1,
                                           'type' => 1,
                                           'uses' => 1,
                                           'var' => 1,
                                           'version' => 1,
                                           'api' => 1
                                          ];

    /**
     * returns list of tokens that signal state change
     *
     * @return  string[]
     */
    public function signalTokens()
    {
        return [' ', "\n", "\r", '{', '[', '('];
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
        if (' ' === $currentToken) {
            if (strlen($word) == 0) {
                $this->changeState($word, AnnotationState::DOCBLOCK);
                return true;
            }

            $this->checkName($word);
            if (!isset($this->forbiddenAnnotationNames[$word])) {
                $this->parser->registerAnnotation($word);
            }

            $this->changeState($word, AnnotationState::ANNOTATION);
        } elseif ("\n" === $currentToken || "\r" === $currentToken) {
            if (strlen($word) > 0 && !isset($this->forbiddenAnnotationNames[$word])) {
                $this->checkName($word);
                $this->parser->registerAnnotation($word);
            }

            $this->changeState($word, AnnotationState::DOCBLOCK);
        } elseif ('{' === $currentToken) {
            if (strlen($word) == 0) {
                throw new \ReflectionException('Annotation name can not be empty');
            }

            $this->checkName($word);
            $this->parser->registerAnnotation($word);
            $this->changeState($word, AnnotationState::ARGUMENT);
        } elseif ('[' === $currentToken) {
            if (strlen($word) == 0) {
                throw new \ReflectionException('Annotation name can not be empty');
            }

            $this->checkName($word);
            $this->parser->registerAnnotation($word);
            $this->changeState($word, AnnotationState::ANNOTATION_TYPE);
        } elseif ('(' === $currentToken) {
            if (strlen($word) == 0) {
                throw new \ReflectionException('Annotation name can not be empty');
            }

            $this->checkName($word);
            $this->parser->registerAnnotation($word);
            $this->changeState($word, AnnotationState::PARAM_NAME);
        }

        return true;
    }

    /**
     * check if the name is valid
     *
     * @param   string  $word
     * @throws  \ReflectionException
     */
    protected function checkName($word)
    {
        if (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $word) == false) {
            throw new \ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains an invalid character.');
        }
    }

    /**
     * helper method to change state to another parsing state only if annotation
     * name is not forbidden, if it is forbidden change back to docblock state
     *
     * @param  string  $word
     * @param  int     $targetState  original target state
     */
    protected function changeState($word, $targetState)
    {
        if (isset($this->forbiddenAnnotationNames[$word])) {
            $targetState = AnnotationState::DOCBLOCK;
        }

        $this->parser->changeState($targetState);
    }
}
