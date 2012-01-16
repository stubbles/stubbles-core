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
 * Test for net\stubbles\lang\reflect\annotation\parser\state\AnnotationDocblockState.
 *
 * @group  lang
 * @group  lang_reflect
 * @group  lang_reflect_annotation
 * @group  lang_reflect_annotation_parser
 * @group  lang_reflect_annotation_parser_state
 */
class stubAnnotationDocblockStateTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AnnotationDocblockState
     */
    protected $docblockState;
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
        $this->docblockState        = new AnnotationDocblockState($this->mockAnnotationParser);
    }

    /**
     * @test
     */
    public function processAtSignChangesStateToAnnotationName()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::ANNOTATION_NAME));
        $this->docblockState->process('@');
    }

    /**
     * @test
     */
    public function processOtherCharacterChangesStateToText()
    {
        $this->mockAnnotationParser->expects($this->exactly(6))
                                   ->method('changeState')
                                   ->with($this->equalTo(AnnotationState::TEXT));
        $this->docblockState->process('a');
        $this->docblockState->process('1');
        $this->docblockState->process('(');
        $this->docblockState->process('[');
        $this->docblockState->process('_');
        $this->docblockState->process('.');
    }

    /**
     * @test
     */
    public function processNoneStateChangingCharacters()
    {
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->docblockState->process('*');
        $this->docblockState->process(' ');
        $this->docblockState->process("\n");
        $this->docblockState->process("\t");
    }
}
?>