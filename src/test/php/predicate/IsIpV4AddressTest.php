<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\predicate;
/**
 * Tests for stubbles\predicate\IsIpV4Address.
 *
 * @group  predicate
 * @since  4.0.0
 */
class IsIpV4AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IsIpV4Address
     */
    private $isIpV4Address;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->isIpV4Address = new IsIpV4Address();
    }

    /**
     * @test
     */
    public function stringIsNoIpAndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV4Address->test('foo'));
    }

    /**
     * @test
     */
    public function nullIsNoIpAndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV4Address->test(null));
    }

    /**
     * @test
     */
    public function booleansAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->isIpV4Address->test(true));
        $this->assertFalse($this->isIpV4Address->test(false));
    }

    /**
     * @test
     */
    public function singleNumbersAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->isIpV4Address->test(4));
        $this->assertFalse($this->isIpV4Address->test(6));
    }

    /**
     * @test
     */
    public function invalidIpWithMissingPartEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV4Address->test('255.55.55'));
    }

    /**
     * @test
     */
    public function invalidIpWithSuperflousPartEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV4Address->test('111.222.333.444.555'));
    }

    /**
     * @test
     */
    public function invalidIpWithMissingNumberEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV4Address->test('1..3.4'));
    }

    /**
     * @test
     */
    public function greatestIpV4EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV4Address->test('255.255.255.255'));
    }

    /**
     * @test
     */
    public function lowestIpV4EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV4Address->test('0.0.0.0'));
    }

    /**
     * @test
     */
    public function correctIpEvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV4Address->test('1.2.3.4'));
    }
}
