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
 * Tests for stubbles\predicate\HttpUriPredicate.
 *
 * @group  predicate
 * @since  4.0.0
 */
class HttpUriPredicateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  HttpUriPredicate
     */
    protected $httpUriPredicate;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->httpUriPredicate = new HttpUriPredicate();
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
    public function invalidValueValidatesToFalse($invalid)
    {
        $this->assertFalse($this->httpUriPredicate->test($invalid));
    }

    /**
     * @test
     */
    public function validHttpUrlValidatesToTrue()
    {
        $this->assertTrue($this->httpUriPredicate->test('http://example.net/'));
    }

    /**
     * @test
     */
    public function validHttpUrlWithDnsEntryValidatesToTrue()
    {
        $this->assertTrue(
                $this->httpUriPredicate->enableDnsCheck()->test('http://localhost/')
        );
    }

    /**
     * @test
     */
    public function validHttpUrlWithoutDnsEntryValidatesToFalse()
    {
        $this->assertFalse(
                $this->httpUriPredicate->enableDnsCheck()->test('http://stubbles.doesNotExist/')
        );
    }
}
