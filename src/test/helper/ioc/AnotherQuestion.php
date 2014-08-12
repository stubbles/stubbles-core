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
 * Helper class for the test.
 */
class AnotherQuestion
{
    /**
     * answer
     *
     * @type  Answer
     */
    private $answer;

    /**
     * @param  Answer  $answer
     * @Inject
     * @Named('answer')
     */
    public function setAnswer(Answer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * returns answer
     *
     * @return  Answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
