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
        $this->modifiableProperties = new ModifiableProperties(['scalar' => ['stringValue' => 'This is a string',
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
    public function setNonExistingSectionAddsSection()
    {
        $this->assertTrue($this->modifiableProperties->setSection('doesNotExist', ['foo' => 'bar'])
                                                     ->hasSection('doesNotExist')
        );
        $this->assertEquals(['foo' => 'bar'],
                            $this->modifiableProperties->section('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function setExistingSectionReplacesSection()
    {
        $this->assertEquals(['foo' => 'bar'],
                            $this->modifiableProperties->setSection('empty', ['foo' => 'bar'])
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setNonExistingValueForNonExistingSectionAddsSectionAndValue()
    {
        $this->assertTrue($this->modifiableProperties->setValue('doesNotExist', 'foo', 'bar')
                                                     ->hasSection('doesNotExist')
        );
        $this->assertEquals(['foo' => 'bar'],
                            $this->modifiableProperties->section('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function setNonExistingValueForExistingSectionAddsValueToSection()
    {
        $this->assertEquals(['stringValue' => 'bar',
                             'intValue'    => '303',
                             'floatValue'  => '3.13',
                             'boolValue'   => 'true'
                            ],
                            $this->modifiableProperties->setValue('scalar', 'stringValue', 'bar')
                                                       ->section('scalar')
        );
    }

    /**
     * @test
     */
    public function setExistingValueForExistingSectionReplacesValueInSection()
    {
        $this->assertEquals(['foo' => 'bar'],
                            $this->modifiableProperties->setValue('empty', 'foo', 'bar')
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setBooleanTrueTransformsToPropertyStorage()
    {
        $this->assertEquals(['foo' => 'true'],
                            $this->modifiableProperties->setBooleanValue('empty', 'foo', true)
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setBooleanFalseTransformsToPropertyStorage()
    {
        $this->assertEquals(['foo' => 'false'],
                            $this->modifiableProperties->setBooleanValue('empty', 'foo', false)
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setArrayValueTransformsToPropertyStorage()
    {
        $this->assertEquals(['foo' => '1|2|3'],
                            $this->modifiableProperties->setArrayValue('empty', 'foo', [1, 2, 3])
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setHashValueTransformsToPropertyStorage()
    {
        $this->assertEquals(['foo' => '1:10|2:20|3:30'],
                            $this->modifiableProperties->setHashValue('empty', 'foo', [1 => 10, 2 => 20, 3 => 30])
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setIntegerRangeValueTransformsToPropertyStorage()
    {
        $this->assertEquals(['foo' => '1..5'],
                            $this->modifiableProperties->setRangeValue('empty', 'foo', [1, 2, 3, 4, 5])
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setReverseIntegerRangeValueTransformsToPropertyStorage()
    {
        $this->assertEquals(['foo' => '5..1'],
                            $this->modifiableProperties->setRangeValue('empty', 'foo', [5, 4, 3, 2, 1])
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setCharacterRangeValueTransformsToPropertyStorage()
    {
        $this->assertEquals(['foo' => 'a..e'],
                            $this->modifiableProperties->setRangeValue('empty', 'foo', ['a', 'b', 'c', 'd', 'e'])
                                                       ->section('empty')
        );
    }

    /**
     * @test
     */
    public function setReverseCharacterRangeValueTransformsToPropertyStorage()
    {
        $this->assertEquals(['foo' => 'e..a'],
                            $this->modifiableProperties->setRangeValue('empty', 'foo', ['e', 'd', 'c', 'b', 'a'])
                                                       ->section('empty')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\FileNotFoundException
     */
    public function fromNonExistantFileThrowsException()
    {
        ModifiableProperties::fromFile(__DIR__ . '/doesNotExist.ini');
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
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
        $this->assertInstanceOf('stubbles\lang\ModifiableProperties', $properties);
        $this->assertTrue($properties->hasSection('foo'));
        $this->assertEquals(['bar' => 'baz'], $properties->section('foo'));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
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
        $this->assertInstanceOf('stubbles\lang\ModifiableProperties', $properties);
        $this->assertTrue($properties->hasSection('foo'));
        $this->assertEquals(['bar' => 'baz'], $properties->section('foo'));
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function mergeReturnsModifiableProperties()
    {
        $this->assertInstanceOf(
                'stubbles\lang\ModifiableProperties',
                $this->modifiableProperties->merge(new Properties([]))
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function unmodifiableTurnsModifiableIntoNonModifiableProperties()
    {
        $this->assertInstanceOf(
                'stubbles\lang\Properties',
                $this->modifiableProperties->unmodifiable()
        );
    }
}
