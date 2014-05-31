<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\exception;
/**
 * Tests for stubbles\lang\exception\IllegalArgumentException.
 *
 * @group  lang
 * @group  lang_exception
 */
class IllegalArgumentExceptionTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function instanceCanBeThrown()
    {
        throw new IllegalArgumentException('error');
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasNoDetailsIfNotGiven()
    {
        $iae = new IllegalArgumentException('error');
        $this->assertFalse($iae->hasDetails());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasDetailsIfAffectedParamNameGiven()
    {
        $iae = new IllegalArgumentException('error', 'param');
        $this->assertTrue($iae->hasDetails());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function containsNameOfAffectedParameter()
    {
        $iae = new IllegalArgumentException('error', 'param', 'invalid');
        $this->assertEquals('param', $iae->getAffectedParamName());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function containsIllegalValue()
    {
        $iae = new IllegalArgumentException('error', 'param', 'invalid');
        $this->assertEquals('invalid', $iae->getIllegalParamValue());
    }
}
