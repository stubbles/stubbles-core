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
 * Tests for stubbles\predicate\IsIpV6Address.
 *
 * @group  predicate
 * @since  4.0.0
 */
class IsIpV6AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IsIpV6Address
     */
    private $isIpV6Address;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->isIpV6Address = new IsIpV6Address();
    }

    /**
     * @test
     */
    public function stringIsNoIpAndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test('foo'));
    }

    /**
     * @test
     */
    public function nullIsNoIpAndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test(null));
    }

    /**
     * @test
     */
    public function booleansAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->isIpV6Address->test(true));
        $this->assertFalse($this->isIpV6Address->test(false));
    }

    /**
     * @test
     */
    public function singleNumbersAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->isIpV6Address->test(4));
        $this->assertFalse($this->isIpV6Address->test(6));
    }

    /**
     * @test
     */
    public function ipv4EvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test('1.2.3.4'));
    }

    /**
     * @test
     */
    public function invalidIpWithMissingPartEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test(':1'));
    }

    /**
     * @test
     */
    public function invalidIpEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test('::ffffff:::::a'));
    }

    /**
     * @test
     */
    public function invalidIpWithHexquadAtStartEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test('XXXX::a574:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function invalidIpWithHexquadAtEndEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test('9982::a574:382b:23c1:aa49:4592:4efe:XXXX'));
    }

    /**
     * @test
     */
    public function invalidIpWithHexquadEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test('a574::XXXX:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function invalidIpWithHexDigitEvaluatesToFalse()
    {
        $this->assertFalse($this->isIpV6Address->test('a574::382X:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function correctIpEvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV6Address->test('febc:a574:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function localhostIpV6EvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV6Address->test('::1'));
    }

    /**
     * @test
     */
    public function shortenedIpEvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV6Address->test('febc:a574:382b::4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function evenMoreShortenedIpEvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV6Address->test('febc::23c1:aa49:0:0:9982'));
    }

    /**
     * @test
     */
    public function singleShortenedIpEvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV6Address->test('febc:a574:2b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function shortenedPrefixIpEvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV6Address->test('::382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function shortenedPostfixIpEvaluatesToTrue()
    {
        $this->assertTrue($this->isIpV6Address->test('febc:a574:382b:23c1:aa49::'));
    }
}
