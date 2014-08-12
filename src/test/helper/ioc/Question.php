<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\ioc;
/**
 * Helper class for ioc tests.
 */
class Question
{
    /**
     * the answer
     *
     * @type  mixed
     */
    private $answer;

    /**
     * sets the answer
     *
     * @param  mixed  $answer
     * @Inject
     * @Named('answer')
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * returns the answer
     *
     * @return  mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
