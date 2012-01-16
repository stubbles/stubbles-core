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
 * Test for net\stubbles\lang\reflect\annotation\parser\state\AnnotationTextState.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 * @group  lang_reflect_annotation_parser_state
 */
class AnnotationTextStateTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AnnotationTextState
     */
    protected $textState;
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
        $this->textState            = new AnnotationTextState($this->mockAnnotationParser);
    }

    /**
     * @test
     */
    public function processLignBreakChangesStateToDocblock()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::DOCBLOCK));
        $this->textState->process("\n");
    }

    /**
     * @test
     */
    public function processNoneStateChangingCharacters()
    {
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->textState->process('a');
        $this->textState->process('1');
        $this->textState->process('(');
        $this->textState->process('[');
        $this->textState->process('_');
        $this->textState->process('.');
        $this->textState->process('*');
        $this->textState->process(' ');
    }
}
?>