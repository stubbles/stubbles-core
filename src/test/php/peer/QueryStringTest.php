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
        assertEquals(
                'bar',
                $this->prefilledQueryString->param('foo.hm')
        );
        assertEquals(
                ['dummy' => 'blubb', 'more'],
                $this->prefilledQueryString->param('baz')
        );
        assertEquals(
                '',
                $this->prefilledQueryString->param('empty')
        );
        assertNull($this->prefilledQueryString->param('set'));
    }

    /**
     * @test
     */
    public function buildEmptQueryStringReturnsEmptyString()
    {
        assertEquals('', $this->emptyQueryString->build());
    }

    /**
     * @test
     */
    public function buildNonEmptQueryStringReturnsString()
    {
        assertEquals(
                'foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set',
                $this->prefilledQueryString->build()
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
        assertEquals(
                'example',
                $this->emptyQueryString->param('doesNotExist', 'example')
        );
    }

    /**
     * @test
     */
    public function getExistingParamReturnsValue()
    {
        assertEquals(
                'bar',
                $this->prefilledQueryString->param('foo.hm')
        );
    }

    /**
     * @test
     */
    public function removeNonExistingParamDoesNothing()
    {
        assertEquals(
                'foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set',
                $this->prefilledQueryString->removeParam('doesNotExist')->build()
        );
    }

    /**
     * @test
     */
    public function removeExistingEmptyParam()
    {
        assertEquals(
                'foo.hm=bar&baz[dummy]=blubb&baz[]=more&set',
                $this->prefilledQueryString->removeParam('empty')->build()
        );
    }

    /**
     * @test
     */
    public function removeExistingNullValueParam()
    {
        assertEquals(
                'foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=',
                $this->prefilledQueryString->removeParam('set')->build()
        );
    }

    /**
     * @test
     */
    public function removeExistingArrayParam()
    {
        assertEquals(
                'foo.hm=bar&empty=&set',
                $this->prefilledQueryString->removeParam('baz')->build()
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
        assertEquals(
                'some=127.0.0.1',
                $this->emptyQueryString->addParam(
                        'some',
                        new IpAddress('127.0.0.1')
                )->build()
        );
    }

    /**
     * @test
     */
    public function addNullValueAddsParamNameOnly()
    {
        assertEquals(
                'some',
                $this->emptyQueryString->addParam('some', null)->build()
        );
    }

    /**
     * @test
     */
    public function addEmptyValueAddsParamNameAndEqualsign()
    {
        assertEquals(
                'some=',
                $this->emptyQueryString->addParam('some', '')->build()
        );
    }

    /**
     * @test
     */
    public function addValueAddsParamNameWithValue()
    {
        assertEquals(
                'some=bar',
                $this->emptyQueryString->addParam('some', 'bar')->build()
        );
    }

    /**
     * @test
     */
    public function addArrayAddsParam()
    {
        assertEquals(
                'some[foo]=bar&some[]=baz',
                $this->emptyQueryString->addParam(
                        'some', ['foo' => 'bar', 'baz']
                )->build()
        );
    }

    /**
     * @test
     */
    public function addFalseValueTranslatesFalseTo0()
    {
        assertEquals(
                'some=0',
                $this->emptyQueryString->addParam('some', false)->build()
        );
    }

    /**
     * @test
     */
    public function addTrueValueTranslatesFalseTo1()
    {
        assertEquals(
                'some=1',
                $this->emptyQueryString->addParam('some', true)->build()
        );
    }

}
