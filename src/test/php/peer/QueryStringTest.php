<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer;
use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\peer\QueryString.
 *
 * @group  peer
 */
class QueryStringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * empty instance to test
     *
     * @type  QueryString
     */
    protected $emptyQueryString;
    /**
     * prefilled instance to test
     *
     * @type  QueryString
     */
    protected $prefilledQueryString;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->emptyQueryString     = new QueryString();
        $this->prefilledQueryString = new QueryString(
                'foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set'
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function constructorThrowsIllegalArgumentExceptionIfQueryStringContainsErrors()
    {
        new QueryString('foo.hm=bar&baz[dummy]=blubb&baz[=more&empty=&set');
    }

    /**
     * @test
     */
    public function emptyHasNoParametersByDefault()
    {
        assertFalse($this->emptyQueryString->hasParams());
    }

    /**
     * @test
     */
    public function prefilledHasParametersFromInitialQueryString()
    {
        assertTrue($this->prefilledQueryString->hasParams());
    }

    /**
     * @return  array
     */
    public function parsedParameters()
    {
        return [
                ['foo.hm', 'bar'],
                ['baz', ['dummy' => 'blubb', 'more']],
                ['empty', ''],
                ['set', null]
        ];
    }

    /**
     * @test
     * @dataProvider  parsedParameters
     */
    public function parsedParametersAreCorrect($paramName, $expectedValue)
    {
        assert(
                $this->prefilledQueryString->param($paramName),
                equals($expectedValue)
        );
    }

    /**
     * @test
     */
    public function buildEmptQueryStringReturnsEmptyString()
    {
        assertEmptyString($this->emptyQueryString->build());
    }

    /**
     * @test
     */
    public function buildNonEmptQueryStringReturnsString()
    {
        assert(
                $this->prefilledQueryString->build(),
                equals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set')
        );
    }

    /**
     * @test
     */
    public function checkForNonExistingParamReturnsFalse()
    {
        assertFalse($this->emptyQueryString->containsParam('doesNotExist'));
    }

    /**
     * @test
     */
    public function checkForExistingParamReturnsTrue()
    {
        assertTrue($this->prefilledQueryString->containsParam('foo.hm'));
    }

    /**
     * @test
     */
    public function checkForExistingEmptyParamReturnsTrue()
    {
        assertTrue($this->prefilledQueryString->containsParam('empty'));
    }

    /**
     * @test
     */
    public function checkForExistingNullValueParamReturnsTrue()
    {
        assertTrue($this->prefilledQueryString->containsParam('set'));
    }

    /**
     * @test
     */
    public function getNonExistingParamReturnsNullByDefault()
    {
        assertNull($this->emptyQueryString->param('doesNotExist'));
    }

    /**
     * @test
     */
    public function getNonExistingParamReturnsDefaultValue()
    {
        assert(
                $this->emptyQueryString->param('doesNotExist', 'example'),
                equals('example')
        );
    }

    /**
     * @test
     */
    public function getExistingParamReturnsValue()
    {
        assert($this->prefilledQueryString->param('foo.hm'), equals('bar'));
    }

    /**
     * @test
     */
    public function removeNonExistingParamDoesNothing()
    {
        assert(
                $this->prefilledQueryString->removeParam('doesNotExist')->build(),
                equals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set')
        );
    }

    /**
     * @test
     */
    public function removeExistingEmptyParam()
    {
        assert(
                $this->prefilledQueryString->removeParam('empty')->build(),
                equals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&set')
        );
    }

    /**
     * @test
     */
    public function removeExistingNullValueParam()
    {
        assert(
                $this->prefilledQueryString->removeParam('set')->build(),
                equals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=')
        );
    }

    /**
     * @test
     */
    public function removeExistingArrayParam()
    {
        assert(
                $this->prefilledQueryString->removeParam('baz')->build(),
                equals('foo.hm=bar&empty=&set')
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function addIllegalParamThrowsIllegalArgumentException()
    {
        $this->emptyQueryString->addParam('some', new \stdClass());
    }

    /**
     * @test
     * @since  5.3.1
     */
    public function allowsToAddObjectWithToStringMethodAsParam()
    {
        assert(
                $this->emptyQueryString->addParam(
                        'some',
                        new IpAddress('127.0.0.1')
                )->build(),
                equals('some=127.0.0.1')
        );
    }

    /**
     * @test
     */
    public function addNullValueAddsParamNameOnly()
    {
        assert(
                $this->emptyQueryString->addParam('some', null)->build(),
                equals('some')
        );
    }

    /**
     * @test
     */
    public function addEmptyValueAddsParamNameAndEqualsign()
    {
        assert(
                $this->emptyQueryString->addParam('some', '')->build(),
                equals('some=')
        );
    }

    /**
     * @test
     */
    public function addValueAddsParamNameWithValue()
    {
        assert(
                $this->emptyQueryString->addParam('some', 'bar')->build(),
                equals('some=bar')
        );
    }

    /**
     * @test
     */
    public function addArrayAddsParam()
    {
        assert(
                $this->emptyQueryString->addParam(
                        'some', ['foo' => 'bar', 'baz']
                )->build(),
                equals('some[foo]=bar&some[]=baz')
        );
    }

    /**
     * @test
     */
    public function addFalseValueTranslatesFalseTo0()
    {
        assert(
                $this->emptyQueryString->addParam('some', false)->build(),
                equals('some=0')
        );
    }

    /**
     * @test
     */
    public function addTrueValueTranslatesFalseTo1()
    {
        assert(
                $this->emptyQueryString->addParam('some', true)->build(),
                equals('some=1')
        );
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function canBeCastedToString()
    {
        assert(
                (string) $this->prefilledQueryString,
                equals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set')
        );
    }
}
