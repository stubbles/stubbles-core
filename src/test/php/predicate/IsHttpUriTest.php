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
 * Tests for stubbles\predicate\IsHttpUri.
 *
 * @group  predicate
 * @since  4.0.0
 */
class IsHttpUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IsHttpUri
     */
    protected $isHttpUri;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->isHttpUri = new IsHttpUri();
    }

    /**
     * @return  array
     */
    public function invalidValues()
    {
        return [[null],
                [303],
                [true],
                [false],
                [''],
                ['invalid'],
                ['ftp://example.net']
        ];
    }

    /**
     * @test
     * @dataProvider  invalidValues
     */
    public function invalidValueEvaluatesToFalse($invalid)
    {
        $this->assertFalse($this->isHttpUri->test($invalid));
    }

    /**
     * @test
     */
    public function validHttpUrlEvaluatesToTrue()
    {
        $this->assertTrue($this->isHttpUri->test('http://example.net/'));
    }

    /**
     * @test
     */
    public function validHttpUrlWithDnsEntryEvaluatesToTrue()
    {
        $this->assertTrue(
                $this->isHttpUri->enableDnsCheck()->test('http://localhost/')
        );
    }

    /**
     * @test
     */
    public function validHttpUrlWithoutDnsEntryEvaluatesToFalse()
    {
        $this->assertFalse(
                $this->isHttpUri->enableDnsCheck()->test('http://stubbles.doesNotExist/')
        );
    }
}
