<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * The contents of this file draw heavily from XP Framework
 * https://github.com/xp-forge/sequence
 *
 * Copyright (c) 2001-2014, XP-Framework Team
 * All rights reserved.
 * https://github.com/xp-framework/xp-framework/blob/master/core/src/main/php/LICENCE
 *
 * @package  stubbles
 */
namespace stubbles\lang;
/**
 * Helper class for the test.
 */
class Employee
{
    private $id;
    private $name;
    private $department;
    private $years;

    /**
     * Creates a new employee
     *
     * @param  int $id
     * @param  string $name
     * @param  string $department
     * @param  int $years
     */
    public function __construct($id, $name, $department, $years)
    {
        $this->id         = $id;
        $this->name       = $name;
        $this->department = $department;
        $this->years      = $years;
    }

    /** @return int */
    public function id() { return $this->id; }

    /** @return string */
    public function name() { return $this->name; }

    /** @return string */
    public function department() { return $this->department; }

    /** @return int */
    public function years() { return $this->years; }

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString()
    {
        return $this->getClassName().'('.
          'id= '.$this->id.', name= '.$this->name.', department= '.$this->department.', years= '.$this->years.
        ')';
    }
}
/**
 * Tests for stubbles\lang\Collector.
 *
 * @since  5.2.0
 */
class CollectorTest extends \PHPUnit_Framework_TestCase
{
    private $people;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->people= [
            1549 => new Employee(1549, 'Timm', 'B', 15),
            1552 => new Employee(1552, 'Alex', 'I', 14),
            6100 => new Employee(6100, 'Dude', 'I', 4)
        ];
    }

    /**
     * @test
     */
    public function toList()
    {
        $this->assertEquals(
                ['Timm', 'Alex', 'Dude'],
                Sequence::of($this->people)
                        ->map(function($e) { return $e->name(); })
                        ->collect()
                        ->inList()
        );
    }

    /**
     * @test
     */
    public function toMapUsesGivenKeyAndValueSelector()
    {
        $this->assertEquals(
                [1549 => 'Timm', 1552 => 'Alex', 6100 => 'Dude'],
                Sequence::of($this->people)
                        ->collect()
                        ->inMap(
                            function(Employee $e) { return $e->id(); },
                            function(Employee $e) { return $e->name(); }
                )
        );
    }

    /**
     * @test
     */
    public function toMapPassesKeyAndValueWhenNoSelectorProvided()
    {
        $this->assertEquals(
                $this->people,
                Sequence::of($this->people)->collect()->inMap()
        );
    }
}
