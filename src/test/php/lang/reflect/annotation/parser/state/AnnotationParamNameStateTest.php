<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  nstubbles
 */
namespace stubbles\lang\reflect\annotation\parser\state;
/**
 * Test for stubbles\lang\reflect\annotation\parser\state\AnnotationParamNameState.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 * @group  lang_reflect_annotation_parser_state
 */
class AnnotationParamNameStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AnnotationParamNameState
     */
    protected $paramNameState;
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
        $this->mockAnnotationParser = $this->getMock('stubbles\lang\\reflect\annotation\parser\AnnotationParser');
        $this->paramNameState       = new AnnotationParamNameState($this->mockAnnotationParser);
    }

    /**
     * @test
     */
    public function processSimpleQuotationMarksChangesStateToParamValue()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotationParam')
                                   ->with($this->equalTo('__value'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_VALUE), $this->equalTo("'"));

        $this->paramNameState->process("'");
    }

    /**
     * @test
     */
    public function processQuotationMarksChangesStateToParamValur()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotationParam')
                                   ->with($this->equalTo('__value'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_VALUE), $this->equalTo('"'));

        $this->paramNameState->process('"');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processQuotationMarksInvalidOffsetThrowsReflectionException()
    {
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotationParam');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramNameState->process('a');
        $this->paramNameState->process("'");
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processQuotationMarksOnInvalidOffsetThrowsReflectionException()
    {
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotationParam');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramNameState->process('a');
        $this->paramNameState->process('"');
    }

    /**
     * @test
     * @expectedException  ReflectionException
     */
    public function processEqualSignOnStartThrowsReflectionException()
    {
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotationParam');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramNameState->process('=');
    }

    /**
     * @test
     */
    public function processEqualSignAfterCorrectParamNameChangesStateToParamValue()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotationParam')
                                   ->with($this->equalTo('abc_123'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_VALUE));
        $this->paramNameState->process('abc_123');
        $this->paramNameState->process('=');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processEqualSignAfterInCorrectParamNameThrowsReflectionException()
    {
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotationParam');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramNameState->process('1a');
        $this->paramNameState->process('=');
    }

    /**
     * @test
     */
    public function processClosingValueParenthesisAfterCharacterChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerSingleAnnotationParam')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->paramNameState->process('a');
        $this->paramNameState->process(')');
    }

    /**
     * @test
     */
    public function processClosingValueParenthesisOnStartChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->never())
                                   ->method('registerSingleAnnotationParam');
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->paramNameState->process(')');
    }
}
