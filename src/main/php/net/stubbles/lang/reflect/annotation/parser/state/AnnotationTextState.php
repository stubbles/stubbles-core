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
 * Text within a docblock state.
 */
class AnnotationTextState extends AnnotationAbstractState implements AnnotationState
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
        }
    }
}
?>