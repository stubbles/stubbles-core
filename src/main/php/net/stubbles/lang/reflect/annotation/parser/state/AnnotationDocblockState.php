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
 * Parser is in docblock, but not in any annotation.
 *
 * @internal
 */
class AnnotationDocblockState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * processes a token
     *
     * @param   string  $token
     */
    public function process($token)
    {
        if ('@' === $token) {
            $this->parser->changeState(AnnotationState::ANNOTATION_NAME);
            return;
        }

        // all character except * and space and line breaks
        if (' ' !== $token && '*' !== $token && "\n" !== $token && "\t" !== $token) {
            $this->parser->changeState(AnnotationState::TEXT);
        }
    }
}
?>