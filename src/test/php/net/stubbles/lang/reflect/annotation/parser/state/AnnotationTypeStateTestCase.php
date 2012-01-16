<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect\annotation\parser\state;
/**
 * Test for net\stubbles\lang\reflect\annotation\parser\state\AnnotationTypeState.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 * @group  lang_reflect_annotation_parser_state
 */
class AnnotationTypeStateTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AnnotationTypeState
     */
    protected $annotationTypeState;
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
        $this->mockAnnotationParser = $this->getMock('net\\stubbles\\lang\\reflect\\annotation\\parser\\AnnotationParser');
        $this->annotationTypeState  = new AnnotationTypeState($this->mockAnnotationParser);
    }

    /**
     * @test
     */
    public function processTypeParenthesisAfterStartChangesStateToAnnotation()
    {
        $this->mockAnnotationParser->expects($this->never())
                                   ->method('setAnnotationType');
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ANNOTATION));
        $this->annotationTypeState->process(']');
    }

    /**
     * @test
     */
    public function processTypeParenthesisAfterCharacterChangesStateToAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationType')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ANNOTATION));
        $this->annotationTypeState->process('a');
        $this->annotationTypeState->process(']');
    }

    /**
     * @test
     * @expectedException  \ReflectionException
     */
    public function processOtherIllegalCharactersThrowsReflectionException()
    {
        $this->annotationTypeState->process('a');
        $this->annotationTypeState->process('1');
        $this->annotationTypeState->process('_');
        $this->annotationTypeState->process(')');
        $this->annotationTypeState->process(']');
    }
}
?>