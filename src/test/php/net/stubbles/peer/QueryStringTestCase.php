<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer;
/**
 * Test for net\stubbles\peer\QueryString.
 *
 * @group  peer
 */
class QueryStringTestCase extends \PHPUnit_Framework_TestCase
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
        $this->prefilledQueryString = new QueryString('foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
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
        $this->assertFalse($this->emptyQueryString->hasParams());
    }

    /**
     * @test
     */
    public function prefilledHasParametersFromInitialQueryString()
    {
        $this->assertTrue($this->prefilledQueryString->hasParams());
        $this->assertEquals('bar',
                            $this->prefilledQueryString->getParam('foo.hm')
        );
        $this->assertEquals(array('dummy' => 'blubb', 'more'),
                            $this->prefilledQueryString->getParam('baz')
        );
        $this->assertEquals('',
                            $this->prefilledQueryString->getParam('empty')
        );
        $this->assertNull($this->prefilledQueryString->getParam('set'));
    }

    /**
     * @test
     */
    public function buildEmptQueryStringReturnsEmptyString()
    {
        $this->assertEquals('',
                            $this->emptyQueryString->build()
        );
    }

    /**
     * @test
     */
    public function buildNonEmptQueryStringReturnsString()
    {
        $this->assertEquals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set',
                            $this->prefilledQueryString->build()
        );
    }

    /**
     * @test
     */
    public function checkForNonExistingParamReturnsFalse()
    {
        $this->assertFalse($this->emptyQueryString->containsParam('doesNotExist'));
    }

    /**
     * @test
     */
    public function checkForExistingParamReturnsTrue()
    {
        $this->assertTrue($this->prefilledQueryString->containsParam('foo.hm'));
    }

    /**
     * @test
     */
    public function checkForExistingEmptyParamReturnsTrue()
    {
        $this->assertTrue($this->prefilledQueryString->containsParam('empty'));
    }

    /**
     * @test
     */
    public function checkForExistingNullValueParamReturnsTrue()
    {
        $this->assertTrue($this->prefilledQueryString->containsParam('set'));
    }

    /**
     * @test
     */
    public function getNonExistingParamReturnsNullByDefault()
    {
        $this->assertNull($this->emptyQueryString->getParam('doesNotExist'));
    }

    /**
     * @test
     */
    public function getNonExistingParamReturnsDefaultValue()
    {
        $this->assertEquals('example',
                            $this->emptyQueryString->getParam('doesNotExist', 'example')
        );
    }

    /**
     * @test
     */
    public function getExistingParamReturnsValue()
    {
        $this->assertEquals('bar',
                            $this->prefilledQueryString->getParam('foo.hm')
        );
    }

    /**
     * @test
     */
    public function removeNonExistingParamDoesNothing()
    {
        $this->assertEquals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=&set',
                            $this->prefilledQueryString->removeParam('doesNotExist')
                                                       ->build()
        );
    }

    /**
     * @test
     */
    public function removeExistingEmptyParam()
    {
        $this->assertEquals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&set',
                            $this->prefilledQueryString->removeParam('empty')
                                                       ->build()
        );
    }

    /**
     * @test
     */
    public function removeExistingNullValueParam()
    {
        $this->assertEquals('foo.hm=bar&baz[dummy]=blubb&baz[]=more&empty=',
                            $this->prefilledQueryString->removeParam('set')
                                                       ->build()
        );
    }

    /**
     * @test
     */
    public function removeExistingArrayParam()
    {
        $this->assertEquals('foo.hm=bar&empty=&set',
                            $this->prefilledQueryString->removeParam('baz')
                                                       ->build()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function addIllegalParamThrowsIllegalArgumentException()
    {
        $this->emptyQueryString->addParam('some', new \stdClass());
    }

    /**
     * @test
     */
    public function addNullValueAddsParamNameOnly()
    {
        $this->assertEquals('some',
                            $this->emptyQueryString->addParam('some', null)
                                                   ->build()
        );
    }

    /**
     * @test
     */
    public function addEmptyValueAddsParamNameAndEqualsign()
    {
        $this->assertEquals('some=',
                            $this->emptyQueryString->addParam('some', '')
                                                   ->build()
        );
    }

    /**
     * @test
     */
    public function addValueAddsParamNameWithValue()
    {
        $this->assertEquals('some=bar',
                            $this->emptyQueryString->addParam('some', 'bar')
                                                   ->build()
        );
    }

    /**
     * @test
     */
    public function addArrayAddsParam()
    {
        $this->assertEquals('some[foo]=bar&some[]=baz',
                            $this->emptyQueryString->addParam('some', array('foo' => 'bar', 'baz'))
                                                   ->build()
        );
    }

    /**
     * @test
     */
    public function addFalseValueTranslatesFalseTo0()
    {
        $this->assertEquals('some=0',
                            $this->emptyQueryString->addParam('some', false)
                                                   ->build()
        );
    }

    /**
     * @test
     */
    public function addTrueValueTranslatesFalseTo1()
    {
        $this->assertEquals('some=1',
                            $this->emptyQueryString->addParam('some', true)
                                                   ->build()
        );
    }

}
?>