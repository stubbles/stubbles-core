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
 * Test for stubbles\peer\HeaderList.
 *
 * @group  peer
 */
class HeaderListTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  HeaderList
     */
    protected $headerList;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->headerList = headers();
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function hasNoHeadersByDefault()
    {
        $this->assertEquals(0, $this->headerList->size());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function containsGivenHeader()
    {
        $headerList = headers(['Binford' => 6100]);
        $this->assertTrue($headerList->containsKey('Binford'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function initialSizeEqualsAmountOfGivenHeaders()
    {
        $headerList = headers(['Binford' => 6100]);
        $this->assertEquals(1, $headerList->size());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsValeOfGivenHeader()
    {
        $headerList = headers(['Binford' => 6100]);
        $this->assertEquals('6100',
                            $headerList->get('Binford')
        );
    }

    /**
     * @test
     */
    public function addingHeaderIncreasesSize()
    {
        $this->assertEquals(1,
                            $this->headerList->put('Binford', 6100)
                                             ->size()
        );
    }

    /**
     * @test
     */
    public function containsAddedHeader()
    {
        $this->assertTrue($this->headerList->put('Binford', 6100)
                                           ->containsKey('Binford')
        );
    }

    /**
     * @test
     */
    public function returnsValueOfAddedHeader()
    {
        $this->assertEquals('6100',
                            $this->headerList->put('Binford', 6100)
                                             ->get('Binford')
        );
    }

    /**
     * helper method to assert presence and content of binford headers
     *
     * @param  stubbles\peer\HeaderList  $headerList
     */
    protected function assertBinford(HeaderList $headerList)
    {
        $this->assertTrue($headerList->containsKey('Binford'));
        $this->assertTrue($headerList->containsKey('X-Power'));
        $this->assertEquals(2, $headerList->size());
        $this->assertEquals('6100', $headerList->get('Binford'));
        $this->assertEquals('More power!', $headerList->get('X-Power'));
    }

    /**
     * @test
     */
    public function containsAllHeadersFromParsedString()
    {
        $this->assertBinford(parseHeaders("Binford: 6100\r\nX-Power: More power!"));
    }

    /**
     * @since 2.1.2
     * @test
     */
    public function doubleOccurenceOfColonSplitsOnFirstColon()
    {
        $headerList = parseHeaders("Binford: 6100\r\nX-Powered-By: Servlet 2.4; JBoss-4.2.2.GA (build: SVNTag=JBoss_4_2_2_GA date=200710231031)/Tomcat-5.5\r\nContent-Type: text/html\r\n");
        $this->assertTrue($headerList->containsKey('Binford'));
        $this->assertEquals('6100', $headerList->get('Binford'));
        $this->assertTrue($headerList->containsKey('X-Powered-By'));
        $this->assertEquals('Servlet 2.4; JBoss-4.2.2.GA (build: SVNTag=JBoss_4_2_2_GA date=200710231031)/Tomcat-5.5',
                            $headerList->get('X-Powered-By')
        );
        $this->assertTrue($headerList->containsKey('Content-Type'));
        $this->assertEquals('text/html', $headerList->get('Content-Type'));
    }

    /**
     * @since  2.0.0
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function appendingInvalidHeaderStructureThrowsIllegalArgumentException()
    {
        $this->headerList->append(400);
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function appendAddsHeadersFromString()
    {
        $this->assertBinford($this->headerList->put('Binford', '6000')
                                              ->append("Binford: 6100\r\nX-Power: More power!")
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function appendAddsHeadersFromArray()
    {
        $this->assertBinford($this->headerList->put('Binford', '6000')
                                              ->append(['Binford' => '6100',
                                                        'X-Power' => 'More power!'
                                                       ]
                                                )

        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function appendAddsHeadersFromOtherInstance()
    {
        $this->assertBinford($this->headerList->put('Binford', '6000')
                                              ->append(headers(['Binford' => '6100',
                                                                'X-Power' => 'More power!'
                                                               ]
                                                       )
                                                )

        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function putArrayThrowsIllegalArgumentException()
    {
        $this->headerList->put('Binford', [6100]);
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function putObjectThrowsIllegalArgumentException()
    {
        $this->headerList->put('Binford', new \stdClass());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function putUnusualKeyThrowsIllegalArgumentException()
    {
        $this->headerList->put(6100, new \stdClass());
    }

    /**
     * @test
     */
    public function remove()
    {
        $this->assertFalse($this->headerList->put('Binford', '6100')
                                            ->remove('Binford')
                                            ->containsKey('Binford')
        );
    }

    /**
     * @test
     */
    public function putUserAgent()
    {
        $this->assertTrue($this->headerList->putUserAgent('Binford 6100')
                                           ->containsKey('User-Agent')
        );
        $this->assertEquals('Binford 6100', $this->headerList->get('User-Agent'));
    }


    /**
     * @test
     */
    public function putReferer()
    {
        $this->assertTrue($this->headerList->putReferer('http://example.com/')
                                           ->containsKey('Referer')
        );
        $this->assertEquals('http://example.com/', $this->headerList->get('Referer'));
    }


    /**
     * @test
     */
    public function putCookie()
    {
        $this->assertTrue($this->headerList->putCookie(['testcookie1' => 'testvalue1 %&'])
                                           ->containsKey('Cookie')
        );
        $this->assertEquals('testcookie1=' . urlencode('testvalue1 %&') . ';',
                            $this->headerList->get('Cookie')
        );
    }


    /**
     * @test
     */
    public function putAuthorization()
    {
        $this->assertTrue($this->headerList->putAuthorization('user', 'pass')
                                           ->containsKey('Authorization')
        );
        $this->assertEquals('BASIC ' . base64_encode('user:pass'),
                            $this->headerList->get('Authorization')
        );
    }

    /**
     * @test
     */
    public function hasNoDateByDefault()
    {
        $this->assertFalse($this->headerList->containsKey('Date'));
    }

    /**
     * @test
     */
    public function putDateWithoutValueGiven()
    {
        $this->assertTrue($this->headerList->putDate()->containsKey('Date'));
    }

    /**
     * @test
     */
    public function putDateWithGivenValue()
    {
        $time = time();
        $this->assertTrue($this->headerList->putDate($time)->containsKey('Date'));
        $this->assertEquals(gmdate('D, d M Y H:i:s', $time) . ' GMT',
                            $this->headerList->get('Date')
        );
    }

    /**
     * @test
     */
    public function enablePower()
    {
        $this->assertTrue($this->headerList->enablePower()->containsKey('X-Binford'));
        $this->assertEquals('More power!', $this->headerList->get('X-Binford'));
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingHeader()
    {
        $this->assertFalse($this->headerList->containsKey('foo'));
    }

    /**
     * @test
     */
    public function returnsNullForNonExistingHeader()
    {
        $this->assertNull($this->headerList->get('foo'));
    }

    /**
     * @test
     */
    public function returnsDefaultValueForNonExistingHeader()
    {
        $this->assertEquals('bar', $this->headerList->get('foo', 'bar'));
    }

    /**
     * @test
     */
    public function returnsAddedValueForExistingHeader()
    {
        $this->assertEquals('baz',
                            $this->headerList->put('foo', 'baz')
                                             ->get('foo')
        );
    }

    /**
     * @test
     */
    public function returnsAddedValueForExistingHeaderWhenDefaultSupplied()
    {
        $this->assertEquals('baz',
                            $this->headerList->put('foo', 'baz')
                                             ->get('foo', 'bar')
        );
    }

    /**
     * @test
     */
    public function clearRemovesAllHeaders()
    {
        $this->assertEquals(0,
                            $this->headerList->putUserAgent('Binford 6100')
                                             ->putReferer('Home Improvement')
                                             ->putCookie(['testcookie1' => 'testvalue1 %&'])
                                             ->putAuthorization('user', 'pass')
                                             ->putDate(time())
                                             ->enablePower()
                                             ->clear()
                                             ->size()
        );
    }

    /**
     * @test
     */
    public function iteratorIsEmptyForDefaultHeaderList()
    {
        $counter = 0;
        foreach ($this->headerList as $key => $value) {
            $counter++;
        }

        $this->assertEquals(0, $counter);
    }

    /**
     * @test
     */
    public function iterableOverAddedHeaders()
    {
        $counter = 0;
        $this->headerList->putUserAgent('Binford 6100')
                         ->put('X-TV', 'Home Improvement');
        foreach ($this->headerList as $key => $value) {
            $counter++;
        }

        $this->assertEquals(2, $counter);
    }
}
