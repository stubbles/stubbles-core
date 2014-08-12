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
 * Test for stubbles\lang\reflect\annotation\parser\state\AnnotationNameState.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 * @group  lang_reflect_annotation_parser_state
 */
class AnnotationNameStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AnnotationNameState
     */
    protected $annotationNameState;
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
        $this->annotationNameState  = new AnnotationNameState($this->mockAnnotationParser);
    }

    /**
     * @test
     */
    public function processSpaceAtStartChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotation');
        $this->annotationNameState->process(' ');
    }

    /**
     * @test
     */
    public function processSpaceAfterOtherCharacterChangesStateToAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ANNOTATION));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->annotationNameState->process('a');
        $this->annotationNameState->process(' ');
    }

    /**
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processSpaceAfterForbiddenAnnotationChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->mockAnnotationParser->expects($this->never())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));

        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process(' ');
    }

    /**
     * @test
     */
    public function processLineAtStartBreakChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->never())
                                   ->method('registerAnnotation');
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationNameState->process("\n");
    }

    /**
     * @test
     */
    public function processLineBreakAfterCharacterRegisteresAnnotationAndChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationNameState->process('a');
        $this->annotationNameState->process("\n");
    }

    /**
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processLineBreakAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process("\n");
    }

    /**
     * @test
     */
    public function processCarriageatStartReturnBehavesAsLinebreak()
    {
        $this->mockAnnotationParser->expects($this->never())
                                   ->method('registerAnnotation');
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationNameState->process("\r");
    }

    /**
     * @test
     */
    public function processCarriageAfterCharacterReturnBehavesAsLinebreak()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationNameState->process('a');
        $this->annotationNameState->process("\r");
    }

    /**
     * test processing a carriage return
     *
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processCarriageReturnAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process("\r");
    }

    /**
     * @test
     */
    public function processArgumentParenthesisChangesStateToArgument()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ARGUMENT));
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('{');
    }

    /**
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processArgumentParenthesisAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process('{');
    }

    /**
     * test processing an argument parenthesis
     *
     * @test
     * @expectedException  \ReflectionException
     */
    public function processArgumentParenthesisAfterSelectedThrowsReflectionException()
    {
        $this->annotationNameState->selected();
        $this->annotationNameState->process('{');
    }

    /**
     * @test
     */
    public function processTypeParenthesisChangesStateToAnnotationType()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ANNOTATION_TYPE));
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('[');
    }

    /**
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processTypeParenthesisAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process('[');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processTypeParenthesisAfterSelectedThrowsReflectionException()
    {
        $this->annotationNameState->selected();
        $this->annotationNameState->process('[');
    }

    /**
     * @test
     */
    public function processValueParenthesisChangesStateToParams()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::PARAMS));
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('(');
    }

    /**
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processValueParenthesisAfterForbiddenAnnotationChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));

        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process('(');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processValueParenthesisAfterSelectedThrowsReflectionException()
    {
        $this->annotationNameState->selected();
        $this->annotationNameState->process('(');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processIllegalCharactersFollowedBySpaceThrowsReflectionException()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process(' ');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processIllegalCharactersFollowedByLineBreakThrowsReflectionException()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process("\n");
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processIllegalCharactersFollowedByArgumentParenthesisThrowsReflectionException()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process('{');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processIllegalCharactersFollowedByTypeParenthesisThrowsReflectionException()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process('[');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processIllegalCharactersFollowedByValueParenthesisThrowsReflectionException()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process('(');
    }
}
