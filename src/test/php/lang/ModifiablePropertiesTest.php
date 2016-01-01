<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use org\bovigo\vfs\vfsStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Tests for stubbles\lang\ModifiableProperties.
 *
 * @since  1.7.0
 * @group  lang
 * @group  lang_core
 */
class ModifiablePropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ModifiableProperties
     */
    protected $modifiableProperties;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->modifiableProperties = new ModifiableProperties(
                ['scalar' => ['stringValue' => 'This is a string',
                              'intValue'    => '303',
                              'floatValue'  => '3.13',
                              'boolValue'   => 'true'
                             ],
                 'array'  => ['arrayValue'  => 'foo|bar|baz',
                              'hashValue'   => 'foo:bar|baz',
                             ],
                 'range'  => ['rangeValue1' => '1..5',
                              'rangeValue2' => 'a..e'
                             ],
                 'empty'  => []
                ]
        );
    }

    /**
     * @test
     */
    public function setNonExistingSectionEnsuresSectionIsContained()
    {
        assertTrue(
                $this->modifiableProperties->setSection('doesNotExist', ['foo' => 'bar'])
                        ->containSection('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function setNonExistingSectionAddsSection()
    {
        assert(
                $this->modifiableProperties->setSection('doesNotExist', ['foo' => 'bar'])
                        ->section('doesNotExist'),
                equals(['foo' => 'bar'])
        );
    }

    /**
     * @test
     */
    public function setExistingSectionReplacesSection()
    {
        assert(
                $this->modifiableProperties->setSection('empty', ['foo' => 'bar'])
                        ->section('empty'),
                equals(['foo' => 'bar'])
        );
    }

    /**
     * @test
     */
    public function setNonExistingValueForNonExistingSectionEnsuresSectionIsContained()
    {
        assertTrue(
                $this->modifiableProperties->setValue('doesNotExist', 'foo', 'bar')
                        ->containSection('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function setNonExistingValueForNonExistingSectionAddsSectionAndValue()
    {
        assert(
                $this->modifiableProperties->setValue('doesNotExist', 'foo', 'bar')
                        ->section('doesNotExist'),
                equals(['foo' => 'bar'])
        );
    }

    /**
     * @test
     */
    public function setNonExistingValueForExistingSectionAddsValueToSection()
    {
        assert(
                $this->modifiableProperties->setValue('scalar', 'stringValue', 'bar')
                        ->section('scalar'),
                equals([
                        'stringValue' => 'bar',
                        'intValue'    => '303',
                        'floatValue'  => '3.13',
                        'boolValue'   => 'true'
                ])
        );
    }

    /**
     * @test
     */
    public function setExistingValueForExistingSectionReplacesValueInSection()
    {
        assert(
                $this->modifiableProperties->setValue('empty', 'foo', 'bar')
                        ->section('empty'),
                equals(['foo' => 'bar'])
        );
    }

    /**
     * @test
     */
    public function setBooleanTrueTransformsToPropertyStorage()
    {
        assert(
                $this->modifiableProperties->setBooleanValue('empty', 'foo', true)
                        ->section('empty'),
                equals(['foo' => 'true'])
        );
    }

    /**
     * @test
     */
    public function setBooleanFalseTransformsToPropertyStorage()
    {
        assert(
                $this->modifiableProperties->setBooleanValue('empty', 'foo', false)
                        ->section('empty'),
                equals(['foo' => 'false'])
        );
    }

    /**
     * @test
     */
    public function setArrayValueTransformsToPropertyStorage()
    {
        assert(
                $this->modifiableProperties->setArrayValue('empty', 'foo', [1, 2, 3])
                        ->section('empty'),
                equals(['foo' => '1|2|3'])
        );
    }

    /**
     * @test
     */
    public function setHashValueTransformsToPropertyStorage()
    {
        assert(
                $this->modifiableProperties->setHashValue(
                        'empty',
                        'foo',
                        [1 => 10, 2 => 20, 3 => 30]
                )->section('empty'),
                equals(['foo' => '1:10|2:20|3:30'])
        );
    }

    /**
     * @test
     */
    public function setIntegerRangeValueTransformsToPropertyStorage()
    {
        assert(
                $this->modifiableProperties->setRangeValue(
                        'empty',
                        'foo',
                        [1, 2, 3, 4, 5]
                )->section('empty'),
                equals(['foo' => '1..5'])
        );
    }

    /**
     * @test
     */
    public function setReverseIntegerRangeValueTransformsToPropertyStorage()
    {
        assert(
                $this->modifiableProperties->setRangeValue(
                        'empty',
                        'foo',
                        [5, 4, 3, 2, 1]
                )->section('empty'),
                equals(['foo' => '5..1'])
        );
    }

    /**
     * @test
     */
    public function setCharacterRangeValueTransformsToPropertyStorage()
    {
        assert(
                $this->modifiableProperties->setRangeValue(
                        'empty',
                        'foo',
                        ['a', 'b', 'c', 'd', 'e']
                )->section('empty'),
                equals(['foo' => 'a..e'])
        );
    }

    /**
     * @test
     */
    public function setReverseCharacterRangeValueTransformsToPropertyStorage()
    {
        assert(
                $this->modifiableProperties->setRangeValue(
                        'empty',
                        'foo',
                        ['e', 'd', 'c', 'b', 'a']
                )->section('empty'),
                equals(['foo' => 'e..a'])
        );
    }

    /**
     * @test
     * @expectedException  stubbles\streams\file\FileNotFound
     */
    public function fromNonExistantFileThrowsException()
    {
        ModifiableProperties::fromFile(__DIR__ . '/doesNotExist.ini');
    }

    /**
     * @test
     * @expectedException  UnexpectedValueException
     */
    public function invalidIniFileThrowsException()
    {
        $root = vfsStream::setup('config');
        vfsStream::newFile('invalid.ini')
                 ->at($root)
                 ->withContent("[invalid{");
        ModifiableProperties::fromFile(vfsStream::url('config/invalid.ini'));
    }

    /**
     * @test
     */
    public function validIniFileReturnsInstance()
    {
        $root = vfsStream::setup('config');
        vfsStream::newFile('test.ini')
                 ->at($root)
                 ->withContent("[foo]\nbar=baz");
        $properties = ModifiableProperties::fromFile(vfsStream::url('config/test.ini'));
        assert($properties->section('foo'), equals(['bar' => 'baz']));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  2.0.0
     * @group  bug213
     */
    public function invalidIniStringThrowsException()
    {
        ModifiableProperties::fromString("[invalid{");
    }

    /**
     * @test
     * @since  2.0.0
     * @group  bug213
     */
    public function validIniStringReturnsInstance()
    {
        $properties = ModifiableProperties::fromString("[foo]\nbar=baz");
        assert($properties->section('foo'), equals(['bar' => 'baz']));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function mergeReturnsModifiableProperties()
    {
        assert(
                $this->modifiableProperties->merge(new Properties([])),
                isInstanceOf(ModifiableProperties::class)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function unmodifiableTurnsModifiableIntoNonModifiableProperties()
    {
        assert(
                $this->modifiableProperties->unmodifiable(),
                isInstanceOf(Properties::class)
        );
    }
}
