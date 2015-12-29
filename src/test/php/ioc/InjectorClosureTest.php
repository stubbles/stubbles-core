<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc;
use stubbles\test\ioc\AnotherQuestion;
use stubbles\test\ioc\Answer;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\ioc\Injector with closure binding.
 *
 * @since  2.1.0
 * @group  ioc
 * @group  issue_31
 */
class InjectorClosureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function injectWithClosure()
    {
        $binder = new Binder();
        $answer = new Answer();
        $binder->bind(Answer::class)->toClosure(function() use($answer) { return $answer; });
        $question = $binder->getInjector()->getInstance(AnotherQuestion::class);
        assert($question, isInstanceOf(AnotherQuestion::class));
        assert($question->getAnswer(), isSameAs($answer));
    }
}
