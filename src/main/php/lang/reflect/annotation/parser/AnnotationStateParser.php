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
use stubbles\lang\reflect\annotation\Annotation;
use stubbles\lang\reflect\annotation\parser\state\AnnotationState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationAnnotationState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationArgumentState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationDocblockState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationNameState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationParamNameState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationParamsState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationParamValueState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationTextState;
use stubbles\lang\reflect\annotation\parser\state\AnnotationTypeState;
/**
 * Parser to parse Java-Style annotations.
 *
 * @internal
 */
class AnnotationStateParser implements AnnotationParser
{
    /**
     * possible states
     *
     * @type  \stubbles\lang\reflect\annotation\parser\state\AnnotationState[]
     */
    private $states             = [];
    /**
     * the current state
     *
     * @type  \stubbles\lang\reflect\annotation\parser\state\AnnotationParserState
     */
    private $currentState       = null;
    /**
     * the name of the current annotation
     *
     * @type  string
     */
    private $currentAnnotation  = null;
    /**
     * the name of the current annotation parameter
     *
     * @type  string
     */
    private $currentParam       = null;
    /**
     * all parsed annotations
     *
     * @type  array
     */
    private $annotations        = [];

    /**
     * constructor
     */
    public function __construct()
    {
        $this->states[AnnotationState::DOCBLOCK]        = new AnnotationDocblockState($this);
        $this->states[AnnotationState::TEXT]            = new AnnotationTextState($this);
        $this->states[AnnotationState::ANNOTATION]      = new AnnotationAnnotationState($this);
        $this->states[AnnotationState::ANNOTATION_NAME] = new AnnotationNameState($this);
        $this->states[AnnotationState::ANNOTATION_TYPE] = new AnnotationTypeState($this);
        $this->states[AnnotationState::ARGUMENT]        = new AnnotationArgumentState($this);
        $this->states[AnnotationState::PARAMS]          = new AnnotationParamsState($this);
        $this->states[AnnotationState::PARAM_NAME]      = new AnnotationParamNameState($this);
        $this->states[AnnotationState::PARAM_VALUE]     = new AnnotationParamValueState($this);
    }

    /**
     * change the current state
     *
     * @param   int     $state
     * @param   string  $token  token that should be processed by the state
     * @throws  \ReflectionException
     */
    public function changeState($state, $token = null)
    {
        if (!isset($this->states[$state])) {
            throw new \ReflectionException('Unknown state ' . $state);
        }

        $this->currentState = $this->states[$state];
        $this->currentState->selected();
        if (null != $token) {
            $this->currentState->process($token);
        }
    }

    /**
     * parse a docblock and return all annotations found
     *
     * @param   string  $docComment
     * @param   string  $targetName
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     * @throws  \ReflectionException
     */
    public static function parseFrom($docComment, $targetName)
    {
        static $self = null;
        if (null === $self) {
            $self = new self();
        }

        return $self->parse($docComment, $targetName);
    }

    /**
     * parse a docblock and return all annotations found
     *
     * @param   string  $docComment
     * @param   string  $targetName
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     * @throws  \ReflectionException
     */
    public function parse($docComment, $targetName)
    {
        $this->annotations = [];
        $this->changeState(AnnotationState::DOCBLOCK);
        $len = strlen($docComment);
        for ($i = 0; $i < $len; $i++) {
            $this->currentState->process($docComment{$i});
        }

        if (!($this->currentState instanceof AnnotationDocblockState)
          && !($this->currentState instanceof AnnotationTextState)) {
            throw new \ReflectionException('Annotation parser finished in wrong state, last annotation probably closed incorrectly, last state was ' . get_class($this->currentState));
        }

        $annotations = [];
        foreach ($this->annotations as $name => $annotation) {
            $annotations[$name] = new Annotation(
                    $annotation['type'],
                    $targetName,
                    $annotation['params']
            );
        }

        return $annotations;
    }

    /**
     * register a new annotation
     *
     * @param  string  $name
     */
    public function registerAnnotation($name)
    {
        $this->annotations[$name] = ['type'     => $name,
                                     'params'   => []
                                    ];
        $this->currentAnnotation  = $name;
    }

    /**
     * register a new annotation param
     *
     * @param  string  $name
     */
    public function registerAnnotationParam($name)
    {
        $this->currentParam = trim($name);
    }

    /**
     * register single annotation param
     *
     * @param   string  $value  the value of the param
     * @throws  \ReflectionException
     */
    public function registerSingleAnnotationParam($value)
    {
        if (count($this->annotations[$this->currentAnnotation]['params']) > 0) {
            throw new \ReflectionException('Error parsing annotation ' . $this->currentAnnotation);
        }

        $this->annotations[$this->currentAnnotation]['params']['__value'] = $value;
    }

    /**
     * set the annoation param value for the current annotation
     *
     * @param  string  $value  the value of the param
     */
    public function setAnnotationParamValue($value)
    {
        $this->annotations[$this->currentAnnotation]['params'][$this->currentParam] = $value;
    }

    /**
     * set the type of the current annotation
     *
     * @param  string  $type  type of the annotation
     */
    public function setAnnotationType($type)
    {
        $this->annotations[$this->currentAnnotation]['type'] = $type;
    }

    /**
     * sets the argument for which the annotation is declared
     *
     * @param  string  $argument  name of the argument
     */
    public function setAnnotationForArgument($argument)
    {
        $this->annotations[$this->currentAnnotation . '#' . $argument] = $this->annotations[$this->currentAnnotation];
        unset($this->annotations[$this->currentAnnotation]);
        $this->currentAnnotation .= '#' . $argument;
    }
}
