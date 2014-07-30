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
use stubbles\peer\IpAddress;
/**
 * Tests for stubbles\predicate\IsIpAddress.
 *
 * @group  predicate
 * @since  4.0.0
 */
class IsIpAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IsIpAddress
     */
    protected $isIpAddress;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->isIpAddress = new IsIpAddress();
    }

    /**
     * @test
     */
    public function stringIsNoIpAndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('foo'));
    }

    /**
     * @test
     */
    public function nullIsNoIpAndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test(null));
    }

    /**
     * @test
     */
    public function emptyStringIsNoIpAndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test(''));
    }

    /**
     * @test
     */
    public function booleansAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->isIpAddress->test(true));
        $this->assertFalse($this->isIpAddress->test(false));
    }

    /**
     * @test
     */
    public function singleNumbersAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->isIpAddress->test(4));
        $this->assertFalse($this->isIpAddress->test(6));
    }

    /**
     * @test
     */
    public function invalidIpV4WithMissingPartEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('255.55.55'));
    }

    /**
     * @test
     */
    public function invalidIpV4WithSuperflousPartEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('111.222.333.444.555'));
    }

    /**
     * @test
     */
    public function invalidIpV4WithMissingNumberEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('1..3.4'));
    }

    /**
     * @test
     */
    public function greatestIpV4EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('255.255.255.255'));
    }

    /**
     * @test
     */
    public function lowestIpV4EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('0.0.0.0'));
    }

    /**
     * @test
     */
    public function correctIpV4EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('1.2.3.4'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithMissingPartEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test(':1'));
    }

    /**
     * @test
     */
    public function invalidIpV6EvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('::ffffff:::::a'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithHexquadAtStartEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('XXXX::a574:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithHexquadAtEndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('9982::a574:382b:23c1:aa49:4592:4efe:XXXX'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithHexquadEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('a574::XXXX:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithHexDigitEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpAddress->test('a574::382X:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function correctIpV6EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('febc:a574:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function localhostIpV6EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('::1'));
    }

    /**
     * @test
     */
    public function shortenedIpV6EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('febc:a574:382b::4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function evenMoreShortenedIpV6EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('febc::23c1:aa49:0:0:9982'));
    }

    /**
     * @test
     */
    public function singleShortenedIpV6EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('febc:a574:2b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function shortenedPrefixIpV6EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('::382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function shortenedPostfixIpV6EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test('febc:a574:382b:23c1:aa49::'));
    }

    /**
     * @test
     */
    public function instanceOfIpAddressEvaluatesToTrue()
    {
        $this->assertTrue($this->isIpAddress->test(new IpAddress('127.0.0.1')));
    }
}
