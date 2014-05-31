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
 * Parser is inside the annotation.
 *
 * @internal
 */
class AnnotationAnnotationState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * processes a token
     *
     * @param  string  $token
     */
    public function process($token)
    {
        if ("\n" === $token) {
            $this->parser->changeState(AnnotationState::DOCBLOCK);
            return;
        }

        if ('{' === $token) {
            $this->parser->changeState(AnnotationState::ARGUMENT);
            return;
        }

        if ('[' === $token) {
            $this->parser->changeState(AnnotationState::ANNOTATION_TYPE);
            return;
        }

        if ('(' === $token) {
            $this->parser->changeState(AnnotationState::PARAMS);
            return;
        }
    }
}
