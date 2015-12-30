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
use stubbles\peer\http\HttpUri;

use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
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
        assertFalse($this->isHttpUri->test($invalid));
    }

    /**
     * @return  array
     */
    public function validValues()
    {
        return [
            ['http://localhost/'],
            [HttpUri::fromString('http://localhost/')]
        ];
    }

    /**
     * @test
     * @dataProvider  validValues
     */
    public function validHttpUrlWithDnsEntryEvaluatesToTrue($value)
    {
        assertTrue($this->isHttpUri->test($value));
    }

    /**
     * @return  array
     */
    public function validValuesWithoutDnsEntry()
    {
        return [
            ['http://stubbles.doesNotExist/'],
            [HttpUri::fromString('http://stubbles.doesNotExist/')]
        ];
    }

    /**
     * @test
     * @dataProvider  validValuesWithoutDnsEntry
     */
    public function validHttpUrlWithoutDnsEntryEvaluatesToTrue($value)
    {
        assertTrue($this->isHttpUri->test($value));
    }
}
