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
use function bovigo\assert\assert;
use function bovigo\assert\assertEmpty;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isOfSize;
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
        assertEmpty(emptyAcceptHeader());
    }

    /**
     * @test
     */
    public function addAcceptableIncreasesCount()
    {
        assert($this->acceptHeader->addAcceptable('text/plain'), isOfSize(1));
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
    public function parseYieldsCorrectValues($parseValue, $expectedList)
    {
        $acceptHeader = AcceptHeader::parse($parseValue);
        foreach ($expectedList as $mimeType => $priority) {
            assertTrue($acceptHeader->hasSharedAcceptables([$mimeType]));
            assert($acceptHeader->priorityFor($mimeType), equals($priority));
        }
    }

    /**
     * @test
     * @dataProvider  provider
     */
    public function parsedStringCanBeRecreated($parseValue, $expectedList, $expectedString)
    {
        assert(
                (string) AcceptHeader::parse($parseValue),
                equals($expectedString)
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
    public function priorityOnEmptyListReturnsPriorityOf1ForEachAcceptable()
    {
        assert($this->acceptHeader->priorityFor('text/html'), equals(1.0));
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturns0()
    {
        assert(
                $this->acceptHeader->addAcceptable('text/plain')
                        ->priorityFor('text/html'),
                equals(0)
        );
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturnsPriorityForGeneralAcceptableIfThisIsInList()
    {
        assert(
                $this->acceptHeader->addAcceptable('*/*')
                        ->priorityFor('text/html'),
                equals(1.0)
        );
    }

    /**
     * @test
     */
    public function priorityForNonExistingAcceptableReturnsPriorityForMainTypeAcceptableIfThisIsInList()
    {
        assert(
                $this->acceptHeader->addAcceptable('text/plain')
                        ->addAcceptable('text/*', 0.5)
                        ->priorityFor('text/html'),
                equals(0.5)
        );
    }

    /**
     * @test
     */
    public function priorityForExistingAcceptableReturnsItsPriority()
    {
        assert(
                $this->acceptHeader->addAcceptable('text/html')
                        ->addAcceptable('text/plain', 0.2)
                        ->priorityFor('text/plain'),
                equals(0.2)
        );
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
        $this->acceptHeader->addAcceptable('text/plain', 0.2);
        assert(
                $this->acceptHeader->findAcceptableWithGreatestPriority(),
                equals('text/plain')
        );
        $this->acceptHeader->addAcceptable('text/html');
        assert(
                $this->acceptHeader->findAcceptableWithGreatestPriority(),
                equals('text/html')
        );
        $this->acceptHeader->addAcceptable('text/other');
        assert(
                $this->acceptHeader->findAcceptableWithGreatestPriority(),
                equals('text/other')
        );
    }

    /**
     * @return  array
     */
    public function acceptedMimetypes()
    {
        return [
                'empty list'  => [[]],
                'filled list' => [['text/plain']]
        ];
    }

    /**
     * @test
     * @dataProvider  acceptedMimetypes
     */
    public function doesNotHaveSharedAcceptablesForEmptyList(array $accepted)
    {
        assertFalse($this->acceptHeader->hasSharedAcceptables($accepted));
    }

    /**
     * @test
     * @dataProvider  acceptedMimetypes
     */
    public function sharedAcceptablesForEmptyListReturnsEmptyArray($accepted)
    {
        assertEmptyArray($this->acceptHeader->getSharedAcceptables($accepted));
    }

    /**
     * @test
     * @dataProvider  acceptedMimetypes
     */
    public function doesNotHaveSharedAcceptablesForNonEqualLists(array $accepted)
    {
        assertFalse(
                $this->acceptHeader->addAcceptable('text/html')
                        ->hasSharedAcceptables($accepted)
        );
    }

    /**
     * @test
     * @dataProvider  acceptedMimetypes
     */
    public function sharedAcceptablesForNonEqualListsReturnsEmptyArray($accepted)
    {
        assertEmptyArray(
                $this->acceptHeader->addAcceptable('text/html')
                        ->getSharedAcceptables($accepted)
        );
    }

    /**
     * @test
     */
    public function hasSharedAcceptablesForCommonLists()
    {
        assertTrue(
                $this->acceptHeader->addAcceptable('text/plain', 0.2)
                        ->hasSharedAcceptables(['text/plain', 'text/other'])
        );
    }

    /**
     * @test
     */
    public function sharedAcceptablesForCommonListsReturnsArrayWithSharedOnes()
    {
        assert(
                $this->acceptHeader->addAcceptable('text/plain', 0.2)
                        ->getSharedAcceptables(['text/plain', 'text/other']),
                equals(['text/plain'])
        );
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityFromEmptyListReturnsNull()
    {
        assertNull(
                $this->acceptHeader->findMatchWithGreatestPriority([
                        'text/plain',
                        'text/other'
                ])
        );
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityFromAcceptedEmptyListReturnsNull()
    {
        assertNull(
                $this->acceptHeader->addAcceptable('text/plain', 0.2)
                        ->addAcceptable('text/html')
                        ->findMatchWithGreatestPriority([])
        );
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityForNonMatchingListsReturnsNull()
    {
        assertNull(
                $this->acceptHeader->addAcceptable('text/plain', 0.2)
                        ->addAcceptable('text/html')
                        ->findMatchWithGreatestPriority(['text/foo', 'text/other'])
        );
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityForMatchingListsAcceptableWithGreatestPriority()
    {
        assert(
                $this->acceptHeader->addAcceptable('text/plain', 0.2)
                        ->addAcceptable('text/html')
                        ->findMatchWithGreatestPriority(['text/html', 'text/other']),
                equals('text/html')
        );
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityWithNonSharedAcceptablesButGeneralAllowedAcceptable()
    {
        assert(
                $this->acceptHeader->addAcceptable('*/*', 0.2)
                        ->addAcceptable('text/html')
                        ->findMatchWithGreatestPriority([
                                'application/json',
                                'text/other'
                        ]),
                equals('application/json')
        );
    }

    /**
     * @test
     */
    public function findMatchWithGreatestPriorityWithNonSharedAcceptablesButMainTypeAllowedAcceptable()
    {
        assert(
                $this->acceptHeader->addAcceptable('text/*', 0.2)
                        ->addAcceptable('text/html')
                        ->findMatchWithGreatestPriority([
                                'application/json',
                                'text/other'
                ]),
                equals('text/other')
        );
    }
}
