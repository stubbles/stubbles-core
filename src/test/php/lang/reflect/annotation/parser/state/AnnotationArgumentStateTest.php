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
 * Test for stubbles\lang\reflect\annotation\parser\state\AnnotationArgumentState.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 * @group  lang_reflect_annotation_parser_state
 */
class AnnotationArgumentStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AnnotationArgumentState
     */
    protected $annotationArgumentState;
    /**
     * the mocked annotation parser
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockAnnotationParser;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockAnnotationParser    = $this->getMock('stubbles\lang\\reflect\annotation\parser\AnnotationParser');
        $this->annotationArgumentState = new AnnotationArgumentState($this->mockAnnotationParser);
    }

    /**
     * @test
     */
    public function processArgumentParenthesisWithoutValueDoesNothing()
    {
        $this->mockAnnotationParser->expects($this->never())
                                   ->method('setAnnotationForArgument');
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ANNOTATION));

        $this->annotationArgumentState->process('}');
    }

    /**
     * @test
     */
    public function processArgumentParenthesisWithValueStoresValue()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationForArgument')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ANNOTATION));
        $this->annotationArgumentState->process('a');
        $this->annotationArgumentState->process('}');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processIllegalCharactersThrowsReflectionException()
    {
        $this->annotationArgumentState->process(')');
        $this->annotationArgumentState->process('}');
    }
}
