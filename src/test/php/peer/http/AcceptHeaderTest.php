<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer\http;
/**
 * Test for stubbles\peer\http\AcceptHeader.
 *
 * @group  peer
 * @group  peer_http
 */
class AcceptHeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  AcceptHeader
     */
    protected $acceptHeader;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->acceptHeader = new AcceptHeader();
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function emptyAcceptHeaderReturnsInstanceWithoutAcceptables()
    {
        assertEquals(0, count(emptyAcceptHeader()));
    }

    /**
     * data provider
     *
     * @return  array
     */
    public function provider()
    {
        return [['text/plain;q=0.5',
                 ['text/plain' => 0.5],
                 'text/plain;q=0.5'
                ],
                ['text/plain;level=2;q=0.5',
                 ['text/plain;level=2' => 0.5],
                 'text/plain;level=2;q=0.5'
                ],
                ['text/plain; q=0.5',
                 ['text/plain' => 0.5],
                 'text/plain;q=0.5'
                ],
                ['text/plain;level=2; q=0.5',
                 ['text/plain;level=2' => 0.5],
                 'text/plain;level=2;q=0.5'
                ],
                ['text/plain;q=1',
                 ['text/plain' => 1.0],
                 'text/plain'
                ],
                ['text/plain; q=1',
                 ['text/plain' => 1.0],
                 'text/plain'
                ],
                ['text/plain',
                 ['text/plain' => 1.0],
                 'text/plain'
                ],
                ['text/plain;level3',
                 ['text/plain;level3' => 1.0],
                 'text/plain;level3'
                ],
                ['text/*;q=0.3, text/html;q=0.7, text/html;level=1,text/html;level=2;q=0.4, */*;q=0.5',
                 ['text/*'            => 0.3,
                  'text/html'         => 0.7,
                  'text/html;level=1' => 1.0,
                  'text/html;level=2' => 0.4,
                  '*/*'               => 0.5
                 ],
                 'text/*;q=0.3,text/html;q=0.7,text/html;level=1,text/html;level=2;q=0.4,*/*;q=0.5'
                ],
                ['text/plain; q=0.5, text/html,text/x-dvi; q=0.8, text/x-c',
                 ['text/plain'        => 0.5,
                  'text/html'         => 1.0,
                  'text/x-dvi'        => 0.8,
                  'text/x-c'          => 1.0
                 ],
                 'text/plain;q=0.5,text/html,text/x-dvi;q=0.8,text/x-c'
                ]
               ];
    }

    /**
     * @test
     * @dataProvider  provider
     */
    public function parseYieldsCorrectValues($parseValue, $expectedList, $expectedString)
    {
        $acceptHeader = AcceptHeader::parse($parseValue);
        foreach ($expectedList as $mimeType => $priority) {
            assertTrue($acceptHeader->hasSharedAcceptables([$mimeType]));
            assertEquals($priority, $acceptHeader->priorityFor($mimeType));
        }

        assertEquals($expectedString,
                            $acceptHeader->asString()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function addAcceptableWithPriorityLowerThan0ThrowsIllegalArgumentException()
    {
        $this->acceptHeader->addAcceptable('text/html', -0.1);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function addAcceptableWithPriorityGreaterThan1ThrowsIllegalArgumentException()
    {
        $this->acceptHeader->addAcceptable('text/html', 1.1);
    }

    /**
     * @test
     */
    public function priorityForOnEmptyListReturnsPriority1ForEachAcceptable()
    {
        assertEquals(0, $this->acceptHeader->count());
        assertEquals(0, count($this->acceptHeader));
        assertEquals(1.0, $this->acceptHeader->priorityFor('text/html'));
        assertEquals(1.0, $this->acceptHeader->priorityFor('text/plain'));
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturns0()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain'));
        assertEquals(1, $this->acceptHeader->count());
        assertEquals(1, count($this->acceptHeader));
        assertEquals(0, $this->acceptHeader->priorityFor('text/html'));
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturnsPriorityForGeneralAcceptableIfThisIsInList()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('*/*'));
        assertEquals(1.0, $this->acceptHeader->priorityFor('text/html'));
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturnsPriorityForMainTypeAcceptableIfThisIsInList()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain'));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/*', 0.5));
        assertEquals(0.5, $this->acceptHeader->priorityFor('text/html'));
        assertEquals(0, $this->acceptHeader->priorityFor('application/json'));
    }

    /**
     * @test
     */
    public function priorityForExistingAcceptableReturnsItsPriority()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertEquals(1.0, $this->acceptHeader->priorityFor('text/html'));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        assertEquals(0.2, $this->acceptHeader->priorityFor('text/plain'));
    }

    /**
     * @test
     */
    public function findAcceptableWithGreatestPriorityForEmptyListReturnsNull()
    {
        assertNull($this->acceptHeader->findAcceptableWithGreatestPriority());
    }

    /**
     * @test
     */
    public function findAcceptableWithGreatestPriority()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        assertEquals('text/plain', $this->acceptHeader->findAcceptableWithGreatestPriority());
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertEquals('text/html', $this->acceptHeader->findAcceptableWithGreatestPriority());
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/other'));
        assertEquals('text/other', $this->acceptHeader->findAcceptableWithGreatestPriority());
    }

    /**
     * @test
     */
    public function sharedAcceptablesForEmptyListReturnsEmptyArray()
    {
        assertFalse($this->acceptHeader->hasSharedAcceptables([]));
        assertEquals([], $this->acceptHeader->getSharedAcceptables([]));
        assertFalse($this->acceptHeader->hasSharedAcceptables(['text/html']));
        assertEquals([], $this->acceptHeader->getSharedAcceptables(['text/html']));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertFalse($this->acceptHeader->hasSharedAcceptables([]));
        assertEquals([], $this->acceptHeader->getSharedAcceptables([]));
    }

    /**
     * @test
     */
    public function sharedAcceptablesForNonEqualListsReturnsEmptyArray()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertFalse($this->acceptHeader->hasSharedAcceptables(['text/plain']));
        assertEquals([], $this->acceptHeader->getSharedAcceptables(['text/plain']));
    }

    /**
     * @test
     */
    public function sharedAcceptablesForCommonListsReturnsArrayWithSharesOnes()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        assertTrue($this->acceptHeader->hasSharedAcceptables(['text/plain', 'text/other']));
        assertEquals(['text/plain'], $this->acceptHeader->getSharedAcceptables(['text/plain', 'text/other']));
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityFromEmptyListReturnsNull()
    {
        assertNull($this->acceptHeader->findMatchWithGreatestPriority(['text/plain', 'text/other']));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertNull($this->acceptHeader->findMatchWithGreatestPriority([]));
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityForNonMatchingListsReturnsNull()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertNull($this->acceptHeader->findMatchWithGreatestPriority(['text/foo', 'text/other']));
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityForMatchingListsAcceptableWithGreatestPriority()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertEquals('text/html',
                            $this->acceptHeader->findMatchWithGreatestPriority(['text/html',
                                                                                'text/other'
                                                                               ]
                                                 )
        );
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityWithNonSharedAcceptablesButGeneralAllowedAcceptable()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('*/*', 0.2));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertEquals('application/json',
                            $this->acceptHeader->findMatchWithGreatestPriority(['application/json',
                                                                                'text/other'
                                                                               ]
                                                 )
        );
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityWithNonSharedAcceptablesButMainTypeAllowedAcceptable()
    {
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/*', 0.2));
        assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        assertEquals('text/other',
                            $this->acceptHeader->findMatchWithGreatestPriority(['application/json',
                                                                                'text/other'
                                                                               ]
                                                 )
        );
    }
}
