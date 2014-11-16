<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\sequence;
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