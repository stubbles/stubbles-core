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
 * Parser is inside an annotation param name.
 *
 * @internal
 */
class AnnotationParamNameState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * name of the param
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
        if ("'" === $token || '"' === $token) {
            if (strlen($this->name) > 0) {
                throw new \ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains ' . $token . '. Probably an equal sign is missing.');
            }

            $this->parser->registerAnnotationParam('__value');
            $this->parser->changeState(AnnotationState::PARAM_VALUE, $token);
            return;
        }

        if ('=' === $token) {
            if (strlen($this->name) == 0) {
                throw new \ReflectionException('Annotation parameter name has to start with a letter or underscore, but starts with =');
            } elseif (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $this->name) == false) {
                throw new \ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains an invalid character.');
            }

            $this->parser->registerAnnotationParam($this->name);
            $this->parser->changeState(AnnotationState::PARAM_VALUE);
            return;
        }

        if (')' === $token) {
            if (strlen($this->name) > 0) {
                $this->parser->registerSingleAnnotationParam($this->name, false);
            }

            $this->parser->changeState(AnnotationState::DOCBLOCK);
            return;
        }

        $this->name .= $token;
    }
}
?>