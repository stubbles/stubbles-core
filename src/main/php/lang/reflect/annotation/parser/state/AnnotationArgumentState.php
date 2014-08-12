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
     * argument for which the annotation stands for
     *
     * @type  string
     */
    private $argument = '';

    /**
     * mark this state as the currently used state
     */
    public function selected()
    {
        parent::selected();
        $this->argument = '';
    }

    /**
     * processes a token
     *
     * @param   string  $token
     * @throws  \ReflectionException
     */
    public function process($token)
    {
        if ('}' === $token) {
            if (strlen($this->argument) > 0) {
                if (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $this->argument) == false) {
                    throw new \ReflectionException('Annotation argument may contain letters, underscores and numbers, but contains an invalid character.');
                }

                $this->parser->markAsParameterAnnotation($this->argument);
            }

            $this->parser->changeState(AnnotationState::ANNOTATION);
            return;
        }

        $this->argument .= $token;
    }
}
