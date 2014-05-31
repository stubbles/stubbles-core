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
 * Parser is inside the annotation type.
 *
 * @internal
 */
class AnnotationTypeState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * type of the annotation
     *
     * @type  string
     */
    private $type = '';

    /**
     * mark this state as the currently used state
     */
    public function selected()
    {
        parent::selected();
        $this->type = '';
    }

    /**
     * processes a token
     *
     * @param   string  $token
     * @throws  \ReflectionException
     */
    public function process($token)
    {
        if (']' === $token) {
            if (strlen($this->type) > 0) {
                if (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $this->type) == false) {
                    throw new \ReflectionException('Annotation type may contain letters, underscores and numbers, but contains an invalid character.');
                }

                $this->parser->setAnnotationType($this->type);
            }

            $this->parser->changeState(AnnotationState::ANNOTATION);
            return;
        }

        $this->type .= $token;
    }
}
