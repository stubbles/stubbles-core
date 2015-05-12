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
 * Parser is inside an enclosed annotation param value.
 *
 * @internal
 */
class AnnotationParamEnclosedValueState extends AnnotationAbstractState implements AnnotationState
{
    /**
     * character in which the value is enclosed
     *
     * @type  string
     */
    private $enclosed  = null;
    /**
     * whether next character is escaped
     *
     * @type  bool
     */
    private $escaped   = false;
    /**
     * collected value until an escaping sign occurred
     *
     * @type  string
     */
    private $collected = '';

    /**
     * mark this state as the currently used state
     */
    public function selected()
    {
        parent::selected();
        $this->enclosed  = null;
        $this->escaped   = false;
        $this->collected = '';
    }

    /**
     * returns list of tokens that signal state change
     *
     * @return  string[]
     */
    public function signalTokens()
    {
        if (null === $this->enclosed) {
            return ["'", '"', '\\'];
        } elseif ('"' === $this->enclosed) {
            return ['"', '\\'];
        }

        return ["'", '\\'];
    }

    /**
     * processes a token
     *
     * @param   string  $word          parsed word to be processed
     * @param   string  $currentToken  current token that signaled end of word
     * @param   string  $nextToken     next token after current token
     * @return  bool
     */
    public function process($word, $currentToken, $nextToken)
    {
        if (strlen($this->collected) === 0 && strlen($word) === 0 && ('"' === $currentToken || "'" === $currentToken)) {
            $this->enclosed = $currentToken;
        } elseif (!$this->escaped && $this->enclosed === $currentToken) {
            $this->parser->setAnnotationParamValue($this->collected . $word);
            $this->enclosed = null;
            $this->parser->changeState(AnnotationState::PARAMS);
        } elseif (!$this->escaped && '\\' === $currentToken && null !== $this->enclosed) {
            $this->escaped = true;
            return false;
        } elseif ($this->escaped) {
            $this->collected .= $word . $currentToken;
            $this->escaped = false;
        }

        return true;
    }
}
