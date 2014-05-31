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
 * Test for stubbles\lang\reflect\annotation\parser\state\AnnotationAnnotationState.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 * @group  lang_reflect_annotation_parser_state
 */
class AnnotationAnnotationStateTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AnnotationAnnotationState
     */
    protected $annotationState;
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
        $this->annotationState      = new AnnotationAnnotationState($this->mockAnnotationParser);
    }

    /**
     * @test
     */
    public function processLinebreakChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationState->process("\n");
    }

    /**
     * @test
     */
    public function processArgumentParenthesisChangesStateToArgument()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ARGUMENT));
        $this->annotationState->process('{');
    }

    /**
     * @test
     */
    public function processTypeParenthesisChangesStateToAnnotationType()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ANNOTATION_TYPE));
        $this->annotationState->process('[');
    }

    /**
     * @test
     */
    public function processValueParenthesisChangesStateToParams()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAMS));
        $this->annotationState->process('(');
    }

    /**
     * @test
     */
    public function processOtherCharactersNeverChangesState()
    {
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->annotationState->process('a');
        $this->annotationState->process('1');
        $this->annotationState->process(']');
        $this->annotationState->process(')');
        $this->annotationState->process('_');
        $this->annotationState->process('.');
    }
}
