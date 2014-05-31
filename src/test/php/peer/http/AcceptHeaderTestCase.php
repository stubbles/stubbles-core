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
class AcceptHeaderTestCase extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals($expectedList,
                            $acceptHeader->getList()
        );
        $this->assertEquals(count($expectedList),
                            $acceptHeader->count()
        );
        $this->assertEquals($expectedString,
                            $acceptHeader->asString()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function addAcceptableWithPriorityLowerThan0ThrowsIllegalArgumentException()
    {
        $this->acceptHeader->addAcceptable('text/html', -0.1);
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
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
        $this->assertEquals(0, $this->acceptHeader->count());
        $this->assertEquals(0, count($this->acceptHeader));
        $this->assertEquals(1.0, $this->acceptHeader->priorityFor('text/html'));
        $this->assertEquals(1.0, $this->acceptHeader->priorityFor('text/plain'));
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturns0()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain'));
        $this->assertEquals(1, $this->acceptHeader->count());
        $this->assertEquals(1, count($this->acceptHeader));
        $this->assertEquals(0, $this->acceptHeader->priorityFor('text/html'));
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturnsPriorityForGeneralAcceptableIfThisIsInList()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('*/*'));
        $this->assertEquals(1.0, $this->acceptHeader->priorityFor('text/html'));
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturnsPriorityForMainTypeAcceptableIfThisIsInList()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain'));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/*', 0.5));
        $this->assertEquals(0.5, $this->acceptHeader->priorityFor('text/html'));
        $this->assertEquals(0, $this->acceptHeader->priorityFor('application/json'));
    }

    /**
     * @test
     */
    public function priorityForExistingAcceptableReturnsItsPriority()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertEquals(1.0, $this->acceptHeader->priorityFor('text/html'));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        $this->assertEquals(0.2, $this->acceptHeader->priorityFor('text/plain'));
    }

    /**
     * @test
     */
    public function findAcceptableWithGreatestPriorityForEmptyListReturnsNull()
    {
        $this->assertNull($this->acceptHeader->findAcceptableWithGreatestPriority());
    }

    /**
     * @test
     */
    public function findAcceptableWithGreatestPriority()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        $this->assertEquals('text/plain', $this->acceptHeader->findAcceptableWithGreatestPriority());
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertEquals('text/html', $this->acceptHeader->findAcceptableWithGreatestPriority());
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/other'));
        $this->assertEquals('text/other', $this->acceptHeader->findAcceptableWithGreatestPriority());
    }

    /**
     * @test
     */
    public function sharedAcceptablesForEmptyListReturnsEmptyArray()
    {
        $this->assertFalse($this->acceptHeader->hasSharedAcceptables([]));
        $this->assertEquals([], $this->acceptHeader->getSharedAcceptables([]));
        $this->assertFalse($this->acceptHeader->hasSharedAcceptables(['text/html']));
        $this->assertEquals([], $this->acceptHeader->getSharedAcceptables(['text/html']));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertFalse($this->acceptHeader->hasSharedAcceptables([]));
        $this->assertEquals([], $this->acceptHeader->getSharedAcceptables([]));
    }

    /**
     * @test
     */
    public function sharedAcceptablesForNonEqualListsReturnsEmptyArray()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertFalse($this->acceptHeader->hasSharedAcceptables(['text/plain']));
        $this->assertEquals([], $this->acceptHeader->getSharedAcceptables(['text/plain']));
    }

    /**
     * @test
     */
    public function sharedAcceptablesForCommonListsReturnsArrayWithSharesOnes()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        $this->assertTrue($this->acceptHeader->hasSharedAcceptables(['text/plain', 'text/other']));
        $this->assertEquals(['text/plain'], $this->acceptHeader->getSharedAcceptables(['text/plain', 'text/other']));
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityFromEmptyListReturnsNull()
    {
        $this->assertNull($this->acceptHeader->findMatchWithGreatestPriority(['text/plain', 'text/other']));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertNull($this->acceptHeader->findMatchWithGreatestPriority([]));
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityForNonMatchingListsReturnsNull()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertNull($this->acceptHeader->findMatchWithGreatestPriority(['text/foo', 'text/other']));
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityForMatchingListsAcceptableWithGreatestPriority()
    {
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/plain', 0.2));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertEquals('text/html',
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
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('*/*', 0.2));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertEquals('application/json',
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
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/*', 0.2));
        $this->assertSame($this->acceptHeader, $this->acceptHeader->addAcceptable('text/html'));
        $this->assertEquals('text/other',
                            $this->acceptHeader->findMatchWithGreatestPriority(['application/json',
                                                                                'text/other'
                                                                               ]
                                                 )
        );
    }
}
