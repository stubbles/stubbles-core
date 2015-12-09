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
class HeaderListTest extends \PHPUnit_Framework_TestCase
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
        assertEquals(0, $this->headerList->size());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function containsGivenHeader()
    {
        $headerList = headers(['Binford' => 6100]);
        assertTrue($headerList->containsKey('Binford'));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function initialSizeEqualsAmountOfGivenHeaders()
    {
        $headerList = headers(['Binford' => 6100]);
        assertEquals(1, $headerList->size());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function returnsValeOfGivenHeader()
    {
        $headerList = headers(['Binford' => 6100]);
        assertEquals('6100', $headerList->get('Binford'));
    }

    /**
     * @test
     */
    public function addingHeaderIncreasesSize()
    {
        assertEquals(
                1,
                $this->headerList->put('Binford', 6100)->size()
        );
    }

    /**
     * @test
     */
    public function containsAddedHeader()
    {
        assertTrue(
                $this->headerList->put('Binford', 6100)
                        ->containsKey('Binford')
        );
    }

    /**
     * @test
     */
    public function returnsValueOfAddedHeader()
    {
        assertEquals(
                '6100',
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
        assertTrue($headerList->containsKey('Binford'));
        assertTrue($headerList->containsKey('X-Power'));
        assertEquals(2, $headerList->size());
        assertEquals('6100', $headerList->get('Binford'));
        assertEquals('More power!', $headerList->get('X-Power'));
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
        assertTrue($headerList->containsKey('Binford'));
        assertEquals('6100', $headerList->get('Binford'));
        assertTrue($headerList->containsKey('X-Powered-By'));
        assertEquals(
                'Servlet 2.4; JBoss-4.2.2.GA (build: SVNTag=JBoss_4_2_2_GA date=200710231031)/Tomcat-5.5',
                $headerList->get('X-Powered-By')
        );
        assertTrue($headerList->containsKey('Content-Type'));
        assertEquals('text/html', $headerList->get('Content-Type'));
    }

    /**
     * @since  2.0.0
     * @test
     * @expectedException  InvalidArgumentException
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
        $this->assertBinford(
                $this->headerList->put('Binford', '6000')
                        ->append("Binford: 6100\r\nX-Power: More power!")
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function appendAddsHeadersFromArray()
    {
        $this->assertBinford(
                $this->headerList->put('Binford', '6000')
                        ->append(
                                ['Binford' => '6100',
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
        $this->assertBinford(
                $this->headerList->put('Binford', '6000')
                        ->append(
                                headers(
                                        ['Binford' => '6100',
                                         'X-Power' => 'More power!'
                                        ]
                                )
                        )

        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function putArrayThrowsIllegalArgumentException()
    {
        $this->headerList->put('Binford', [6100]);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function putObjectThrowsIllegalArgumentException()
    {
        $this->headerList->put('Binford', new \stdClass());
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
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
        assertFalse(
                $this->headerList->put('Binford', '6100')
                        ->remove('Binford')
                        ->containsKey('Binford')
        );
    }

    /**
     * @test
     */
    public function putUserAgent()
    {
        assertTrue(
                $this->headerList->putUserAgent('Binford 6100')
                        ->containsKey('User-Agent')
        );
        assertEquals('Binford 6100', $this->headerList->get('User-Agent'));
    }


    /**
     * @test
     */
    public function putReferer()
    {
        assertTrue(
                $this->headerList->putReferer('http://example.com/')
                        ->containsKey('Referer')
        );
        assertEquals('http://example.com/', $this->headerList->get('Referer'));
    }


    /**
     * @test
     */
    public function putCookie()
    {
        assertTrue(
                $this->headerList->putCookie(['testcookie1' => 'testvalue1 %&'])
                        ->containsKey('Cookie')
        );
        assertEquals('testcookie1=' . urlencode('testvalue1 %&') . ';',
                            $this->headerList->get('Cookie')
        );
    }


    /**
     * @test
     */
    public function putAuthorization()
    {
        assertTrue(
                $this->headerList->putAuthorization('user', 'pass')
                        ->containsKey('Authorization')
        );
        assertEquals(
                'BASIC ' . base64_encode('user:pass'),
                $this->headerList->get('Authorization')
        );
    }

    /**
     * @test
     */
    public function hasNoDateByDefault()
    {
        assertFalse($this->headerList->containsKey('Date'));
    }

    /**
     * @test
     */
    public function putDateWithoutValueGiven()
    {
        assertTrue($this->headerList->putDate()->containsKey('Date'));
    }

    /**
     * @test
     */
    public function putDateWithGivenValue()
    {
        $time = time();
        assertTrue($this->headerList->putDate($time)->containsKey('Date'));
        assertEquals(
                gmdate('D, d M Y H:i:s', $time) . ' GMT',
                $this->headerList->get('Date')
        );
    }

    /**
     * @test
     */
    public function enablePower()
    {
        assertTrue($this->headerList->enablePower()->containsKey('X-Binford'));
        assertEquals('More power!', $this->headerList->get('X-Binford'));
    }

    /**
     * @test
     */
    public function returnsFalseOnCheckForNonExistingHeader()
    {
        assertFalse($this->headerList->containsKey('foo'));
    }

    /**
     * @test
     */
    public function returnsNullForNonExistingHeader()
    {
        assertNull($this->headerList->get('foo'));
    }

    /**
     * @test
     */
    public function returnsDefaultValueForNonExistingHeader()
    {
        assertEquals('bar', $this->headerList->get('foo', 'bar'));
    }

    /**
     * @test
     */
    public function returnsAddedValueForExistingHeader()
    {
        assertEquals(
                'baz',
                $this->headerList->put('foo', 'baz')->get('foo')
        );
    }

    /**
     * @test
     */
    public function returnsAddedValueForExistingHeaderWhenDefaultSupplied()
    {
        assertEquals(
                'baz',
                $this->headerList->put('foo', 'baz')->get('foo', 'bar')
        );
    }

    /**
     * @test
     */
    public function clearRemovesAllHeaders()
    {
        assertEquals(
                0,
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

        assertEquals(0, $counter);
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

        assertEquals(2, $counter);
    }
}
