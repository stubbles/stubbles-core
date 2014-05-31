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
 * Test for stubbles\lang\reflect\annotation\parser\state\AnnotationParamsState.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 * @group  lang_reflect_annotation_parser_state
 */
class AnnotationParamsStateTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AnnotationParamsState
     */
    protected $paramsState;
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
        $this->paramsState          = new AnnotationParamsState($this->mockAnnotationParser);
    }

    /**
     * @test
     */
    public function processClosingValueParenthesisChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->paramsState->process(')');
    }

    /**
     * @test
     */
    public function processNoneStateChangingCharacters()
    {
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramsState->process(',');
        $this->paramsState->process(' ');
        $this->paramsState->process("\r");
        $this->paramsState->process("\n");
        $this->paramsState->process("\t");
        $this->paramsState->process('*');
    }

    /**
     * @test
     */
    public function processCharacterChangesStateToParamName()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_NAME), $this->equalTo('a'));
        $this->paramsState->process('a');
    }

    /**
     * @test
     */
    public function processNumberChangesStateToParamName()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_NAME), $this->equalTo('1'));
        $this->paramsState->process('1');
    }

    /**
     * @test
     */
    public function processParenthesisChangesStateToParamName()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_NAME), $this->equalTo('('));
        $this->paramsState->process('(');
    }

    /**
     * @test
     */
    public function processOtherParenthesisChangesStateToParamName()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_NAME), $this->equalTo('['));
        $this->paramsState->process('[');
    }

    /**
     * @test
     */
    public function processUnderscoreChangesStateToParamName()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_NAME), $this->equalTo('_'));
        $this->paramsState->process('_');
    }

    /**
     * @test
     */
    public function processDotChangesStateToParamName()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAM_NAME), $this->equalTo('.'));
        $this->paramsState->process('.');
    }
}
