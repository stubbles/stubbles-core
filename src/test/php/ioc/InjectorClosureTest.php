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
        $answer = new \stubbles\test\ioc\Answer();
        $binder->bind('stubbles\test\ioc\Answer')->toClosure(function() use($answer) { return $answer; });
        $question = $binder->getInjector()->getInstance('stubbles\test\ioc\AnotherQuestion');
        $this->assertInstanceOf('stubbles\test\ioc\AnotherQuestion', $question);
        $this->assertSame($answer, $question->getAnswer());
    }
}
