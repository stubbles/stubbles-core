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
 * Parser is inside the annotation params.
 *
 * @internal
 */
class AnnotationParamsState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * list of tokens that lead to no actions in this state
     *
     * @type  string[]
     */
    protected $doNothingTokens = array(',', ' ', "\r", "\n", "\t", '*');

    /**
     * processes a token
     *
     * @param  string  $token
     */
    public function process($token)
    {
        if (')' === $token) {
            $this->parser->changeState(AnnotationState::DOCBLOCK);
            return;
        }

        if (in_array($token, $this->doNothingTokens) == true) {
            return;
        }

        $this->parser->changeState(AnnotationState::PARAM_NAME, $token);
    }
}
