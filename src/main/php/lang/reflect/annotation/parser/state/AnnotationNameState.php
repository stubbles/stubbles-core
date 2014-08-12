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
    protected $forbiddenAnnotationNames = ['deprecated',
                                           'example',
                                           'ignore',
                                           'internal',
                                           'link',
                                           'method',
                                           'package',
                                           'param',
                                           'property',
                                           'property-read',
                                           'property-write',
                                           'return',
                                           'see',
                                           'since',
                                           'static',
                                           'subpackage',
                                           'throws',
                                           'todo',
                                           'type',
                                           'uses',
                                           'var',
                                           'version'
                                          ];
    /**
     * name of the annotation
     *
     * @type  string
     */
    private $name = '';

    /**
     * mark this state as the currently used state
     */
    public function selected()
    {
        parent::selected();
        $this->name = '';
    }

    /**
     * processes a token
     *
     * @param   string  $token
     * @throws  \ReflectionException
     */
    public function process($token)
    {
        if (' ' === $token) {
            if (strlen($this->name) == 0) {
                $this->changeState(AnnotationState::DOCBLOCK);
                return;
            }

            $this->checkName();
            if (!in_array($this->name, $this->forbiddenAnnotationNames)) {
                $this->parser->registerAnnotation($this->name);
            }

            $this->changeState(AnnotationState::ANNOTATION);
            return;
        }

        if ("\n" === $token || "\r" === $token) {
            if (strlen($this->name) > 0) {
                $this->checkName();
                $this->parser->registerAnnotation($this->name);
            }

            $this->changeState(AnnotationState::DOCBLOCK);
            return;
        }

        if ('{' === $token) {
            if (strlen($this->name) == 0) {
                throw new \ReflectionException('Annotation name can not be empty');
            }

            $this->checkName();
            $this->parser->registerAnnotation($this->name);
            $this->changeState(AnnotationState::ARGUMENT);
            return;
        }

        if ('[' === $token) {
            if (strlen($this->name) == 0) {
                throw new \ReflectionException('Annotation name can not be empty');
            }

            $this->checkName();
            $this->parser->registerAnnotation($this->name);
            $this->changeState(AnnotationState::ANNOTATION_TYPE);
            return;
        }

        if ('(' === $token) {
            if (strlen($this->name) == 0) {
                throw new \ReflectionException('Annotation name can not be empty');
            }

            $this->checkName();
            $this->parser->registerAnnotation($this->name);
            $this->changeState(AnnotationState::PARAMS);
            return;
        }

        $this->name .= $token;
    }

    /**
     * check if the name is valid
     *
     * @throws  \ReflectionException
     */
    protected function checkName()
    {
        if (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $this->name) == false) {
            throw new \ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains an invalid character.');
        }
    }

    /**
     * helper method to change state to another parsing state only if annotation
     * name is not forbidden, if it is forbidden change back to docblock state
     *
     * @param  int  $targetState  original target state
     */
    protected function changeState($targetState)
    {
        if (in_array($this->name, $this->forbiddenAnnotationNames)) {
            $targetState = AnnotationState::DOCBLOCK;
        }

        $this->parser->changeState($targetState);
    }
}
