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
/**
 * Tests for stubbles\predicate\IsExistingHttpUri.
 *
 * @group  predicate
 * @since  4.0.0
 */
class IsExistingHttpUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  IsExistingHttpUri
     */
    protected $isExistingHttpUri;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->isExistingHttpUri = new IsExistingHttpUri();
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
        $this->assertFalse($this->isExistingHttpUri->test($invalid));
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
        $this->assertTrue(
                $this->isExistingHttpUri->test($value)
        );
    }

    /**
     * @test
     */
    public function validHttpUrlWithoutDnsEntryEvaluatesToFalse()
    {
        $this->assertFalse(
                $this->isExistingHttpUri->test('http://stubbles.doesNotExist/')
        );
    }
}
