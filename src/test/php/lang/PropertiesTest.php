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
 * Tests for stubbles\lang\Properties.
 *
 * @group  lang
 * @group  lang_core
 */
class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  Properties
     */
    protected $properties;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->properties = properties(
                ['scalar' => ['stringValue' => 'This is a string',
                              'intValue1'   => '303',
                              'intValue2'   => 303,
                              'floatValue1' => '3.13',
                              'floatValue2' => 3.13,
                              'boolValue1'  => '1',
                              'boolValue2'  => 1,
                              'boolValue3'  => 'yes',
                              'boolValue4'  => 'true',
                              'boolValue5'  => 'on',
                              'boolValue6'  => '0',
                              'boolValue7'  => 0,
                              'boolValue8'  => 'no',
                              'boolValue9'  => 'false',
                              'boolValue10' => 'off',
                              'boolValue11' => 'other'
                             ],
                 'array'  => ['arrayValue1' => '[foo|bar|baz]',
                              'arrayValue2' => '[]',
                              'hashValue1'  => '[foo:bar|baz]',
                              'hashValue2'  => '[]'
                             ],
                 'range'  => ['rangeValue1' => '1..5',
                              'rangeValue2' => 'a..e',
                              'rangeValue3' => '1..',
                              'rangeValue4' => 'a..',
                              'rangeValue5' => '..5',
                              'rangeValue6' => '..e',
                              'rangeValue7' => '5..1',
                              'rangeValue8' => 'e..a'
                             ],
                 'empty'  => []
                ]
       );
    }

    /**
     * @test
     */
    public function containSectionReturnsTrueForExistingSections()
    {
        assertTrue($this->properties->containSection('scalar'));
        assertTrue($this->properties->containSection('array'));
        assertTrue($this->properties->containSection('range'));
        assertTrue($this->properties->containSection('empty'));
    }

    /**
     * @test
     */
    public function containSectionReturnsFalseForNonExistingSections()
    {
        assertFalse($this->properties->containSection('doesNotExist'));
    }

    /**
     * @test
     */
    public function sectionWithoutDefaultValueReturnsSectionValues()
    {
        assertEquals(
                ['stringValue' => 'This is a string',
                 'intValue1'   => '303',
                 'intValue2'   => 303,
                 'floatValue1' => '3.13',
                 'floatValue2' => 3.13,
                 'boolValue1'  => '1',
                 'boolValue2'  => 1,
                 'boolValue3'  => 'yes',
                 'boolValue4'  => 'true',
                 'boolValue5'  => 'on',
                 'boolValue6'  => '0',
                 'boolValue7'  => 0,
                 'boolValue8'  => 'no',
                 'boolValue9'  => 'false',
                 'boolValue10' => 'off',
                 'boolValue11' => 'other'
                ],
                $this->properties->section('scalar')
        );
        assertEquals(
                ['arrayValue1' => '[foo|bar|baz]',
                 'arrayValue2' => '[]',
                 'hashValue1'  => '[foo:bar|baz]',
                 'hashValue2'  => '[]'
                ],
                $this->properties->section('array')
        );
        assertEquals(
                ['rangeValue1' => '1..5',
                 'rangeValue2' => 'a..e',
                 'rangeValue3' => '1..',
                 'rangeValue4' => 'a..',
                 'rangeValue5' => '..5',
                 'rangeValue6' => '..e',
                 'rangeValue7' => '5..1',
                 'rangeValue8' => 'e..a'
                ],
                $this->properties->section('range')
        );
        assertEquals([], $this->properties->section('empty'));
    }

    /**
     * @test
     */
    public function sectionWithoutDefaultValueReturnsEmptyArrayIfSectionDoesNotExist()
    {
        assertEquals([], $this->properties->section('doesNotExist'));
    }

    /**
     * @test
     */
    public function sectionWithDefaultValueReturnsSectionValues()
    {
        assertEquals(
                ['stringValue' => 'This is a string',
                 'intValue1'   => '303',
                 'intValue2'   => 303,
                 'floatValue1' => '3.13',
                 'floatValue2' => 3.13,
                 'boolValue1'  => '1',
                 'boolValue2'  => 1,
                 'boolValue3'  => 'yes',
                 'boolValue4'  => 'true',
                 'boolValue5'  => 'on',
                 'boolValue6'  => '0',
                 'boolValue7'  => 0,
                 'boolValue8'  => 'no',
                 'boolValue9'  => 'false',
                 'boolValue10' => 'off',
                 'boolValue11' => 'other'
                ],
                $this->properties->section('scalar', ['foo' => 'bar'])
        );
        assertEquals(
                ['arrayValue1' => '[foo|bar|baz]',
                 'arrayValue2' => '[]',
                 'hashValue1'  => '[foo:bar|baz]',
                 'hashValue2'  => '[]'
                ],
                $this->properties->section('array', ['foo' => 'bar'])
        );
        assertEquals(
                ['rangeValue1' => '1..5',
                 'rangeValue2' => 'a..e',
                 'rangeValue3' => '1..',
                 'rangeValue4' => 'a..',
                 'rangeValue5' => '..5',
                 'rangeValue6' => '..e',
                 'rangeValue7' => '5..1',
                 'rangeValue8' => 'e..a'
                ],
                $this->properties->section('range', ['foo' => 'bar'])
        );
        assertEquals([], $this->properties->section('empty', ['foo' => 'bar']));
    }

    /**
     * @test
     */
    public function sectionWithDefaultValueReturnsDefaultValueIfSectionDoesNotExist()
    {
        assertEquals(
                ['foo' => 'bar'],
                $this->properties->section('doesNotExist', ['foo' => 'bar'])
        );
    }

    /**
     * @test
     */
    public function keysForSectionReturnsListOfKeysForGivenSection()
    {
        assertEquals(
                ['stringValue',
                 'intValue1',
                 'intValue2',
                 'floatValue1',
                 'floatValue2',
                 'boolValue1',
                 'boolValue2',
                 'boolValue3',
                 'boolValue4',
                 'boolValue5',
                 'boolValue6',
                 'boolValue7',
                 'boolValue8',
                 'boolValue9',
                 'boolValue10',
                 'boolValue11'
                ],
                $this->properties->keysForSection('scalar', ['foo', 'bar'])
        );
        assertEquals(
                ['arrayValue1',
                 'arrayValue2',
                 'hashValue1',
                 'hashValue2'
                ],
                $this->properties->keysForSection('array', ['foo', 'bar'])
        );
        assertEquals(
                ['rangeValue1',
                 'rangeValue2',
                 'rangeValue3',
                 'rangeValue4',
                 'rangeValue5',
                 'rangeValue6',
                 'rangeValue7',
                 'rangeValue8'
                ],
                $this->properties->keysForSection('range', ['foo', 'bar'])
        );
        assertEquals(
                [],
                $this->properties->keysForSection('empty', ['foo', 'bar'])
        );
    }

    /**
     * @test
     */
    public function keysForSectionReturnsDefaultListOfSectionDoesNotExist()
    {
        assertEquals(
                ['foo', 'bar'],
                $this->properties->keysForSection('doesNotExist', ['foo', 'bar'])
        );
    }

    /**
     * @test
     */
    public function containValueReturnsTrueIfValueExist()
    {
        assertTrue($this->properties->containValue('scalar', 'stringValue'));
        assertTrue($this->properties->containValue('scalar', 'intValue1'));
        assertTrue($this->properties->containValue('scalar', 'intValue2'));
        assertTrue($this->properties->containValue('scalar', 'floatValue1'));
        assertTrue($this->properties->containValue('scalar', 'floatValue2'));
        assertTrue($this->properties->containValue('scalar', 'boolValue1'));
        assertTrue($this->properties->containValue('scalar', 'boolValue2'));
        assertTrue($this->properties->containValue('scalar', 'boolValue3'));
        assertTrue($this->properties->containValue('scalar', 'boolValue4'));
        assertTrue($this->properties->containValue('scalar', 'boolValue5'));
        assertTrue($this->properties->containValue('scalar', 'boolValue6'));
        assertTrue($this->properties->containValue('scalar', 'boolValue7'));
        assertTrue($this->properties->containValue('scalar', 'boolValue8'));
        assertTrue($this->properties->containValue('scalar', 'boolValue9'));
        assertTrue($this->properties->containValue('scalar', 'boolValue10'));
        assertTrue($this->properties->containValue('scalar', 'boolValue11'));

        assertTrue($this->properties->containValue('array', 'arrayValue1'));
        assertTrue($this->properties->containValue('array', 'arrayValue2'));
        assertTrue($this->properties->containValue('array', 'hashValue1'));
        assertTrue($this->properties->containValue('array', 'hashValue2'));

        assertTrue($this->properties->containValue('range', 'rangeValue1'));
        assertTrue($this->properties->containValue('range', 'rangeValue2'));
        assertTrue($this->properties->containValue('range', 'rangeValue3'));
        assertTrue($this->properties->containValue('range', 'rangeValue4'));
        assertTrue($this->properties->containValue('range', 'rangeValue5'));
        assertTrue($this->properties->containValue('range', 'rangeValue6'));
        assertTrue($this->properties->containValue('range', 'rangeValue7'));
        assertTrue($this->properties->containValue('range', 'rangeValue8'));
    }

    /**
     * @test
     */
    public function containValueReturnsFalseIfValueDoesNotExist()
    {
        assertFalse($this->properties->containValue('scalar', 'boolValue12'));
        assertFalse($this->properties->containValue('array', 'hashValue3'));
        assertFalse($this->properties->containValue('range', 'rangeValue9'));
        assertFalse($this->properties->containValue('empty', 'any'));
    }

    /**
     * @test
     */
    public function containValueReturnsFalseIfSectionDoesNotExist()
    {
        assertFalse($this->properties->containValue('doesNotExist', 'any'));
    }

    /**
     * @test
     */
    public function valueWithoutDefaultValueReturnsValueIfExists()
    {
        assertEquals('This is a string', $this->properties->value('scalar', 'stringValue'));
        assertEquals('303', $this->properties->value('scalar', 'intValue1'));
        assertEquals(303, $this->properties->value('scalar', 'intValue2'));
        assertEquals('3.13', $this->properties->value('scalar', 'floatValue1'));
        assertEquals(3.13, $this->properties->value('scalar', 'floatValue2'));
        assertEquals('1', $this->properties->value('scalar', 'boolValue1'));
        assertEquals(1, $this->properties->value('scalar', 'boolValue2'));
        assertEquals('yes', $this->properties->value('scalar', 'boolValue3'));
        assertEquals('true', $this->properties->value('scalar', 'boolValue4'));
        assertEquals('on', $this->properties->value('scalar', 'boolValue5'));
        assertEquals('0', $this->properties->value('scalar', 'boolValue6'));
        assertEquals(0, $this->properties->value('scalar', 'boolValue7'));
        assertEquals('no', $this->properties->value('scalar', 'boolValue8'));
        assertEquals('false', $this->properties->value('scalar', 'boolValue9'));
        assertEquals('off', $this->properties->value('scalar', 'boolValue10'));
        assertEquals('other', $this->properties->value('scalar', 'boolValue11'));

        assertEquals('[foo|bar|baz]', $this->properties->value('array', 'arrayValue1'));
        assertEquals('[]', $this->properties->value('array', 'arrayValue2'));
        assertEquals('[foo:bar|baz]', $this->properties->value('array', 'hashValue1'));
        assertEquals('[]', $this->properties->value('array', 'hashValue2'));

        assertEquals('1..5', $this->properties->value('range', 'rangeValue1'));
        assertEquals('a..e', $this->properties->value('range', 'rangeValue2'));
        assertEquals('1..', $this->properties->value('range', 'rangeValue3'));
        assertEquals('a..', $this->properties->value('range', 'rangeValue4'));
        assertEquals('..5', $this->properties->value('range', 'rangeValue5'));
        assertEquals('..e', $this->properties->value('range', 'rangeValue6'));
        assertEquals('5..1', $this->properties->value('range', 'rangeValue7'));
        assertEquals('e..a', $this->properties->value('range', 'rangeValue8'));
    }

    /**
     * @test
     */
    public function valueWithoutDefaultValueReturnsNullIfValueDoesNotExist()
    {
        assertNull($this->properties->value('scalar', 'boolValue12'));
        assertNull($this->properties->value('array', 'hashValue3'));
        assertNull($this->properties->value('range', 'rangeValue9'));
        assertNull($this->properties->value('empty', 'any'));
    }

    /**
     * @test
     */
    public function valueWithoutDefaultValueReturnsNullIfSectionDoesNotExist()
    {
        assertNull($this->properties->value('doesNotExist', 'any'));
    }

    /**
     * @test
     */
    public function valueWithDefaultValueReturnsValueIfExists()
    {
        assertEquals('This is a string', $this->properties->value('scalar', 'stringValue', 'otherValue'));
        assertEquals('303', $this->properties->value('scalar', 'intValue1', 'otherValue'));
        assertEquals(303, $this->properties->value('scalar', 'intValue2', 'otherValue'));
        assertEquals('3.13', $this->properties->value('scalar', 'floatValue1', 'otherValue'));
        assertEquals(3.13, $this->properties->value('scalar', 'floatValue2', 'otherValue'));
        assertEquals('1', $this->properties->value('scalar', 'boolValue1', 'otherValue'));
        assertEquals(1, $this->properties->value('scalar', 'boolValue2', 'otherValue'));
        assertEquals('yes', $this->properties->value('scalar', 'boolValue3', 'otherValue'));
        assertEquals('true', $this->properties->value('scalar', 'boolValue4', 'otherValue'));
        assertEquals('on', $this->properties->value('scalar', 'boolValue5', 'otherValue'));
        assertEquals('0', $this->properties->value('scalar', 'boolValue6', 'otherValue'));
        assertEquals(0, $this->properties->value('scalar', 'boolValue7', 'otherValue'));
        assertEquals('no', $this->properties->value('scalar', 'boolValue8', 'otherValue'));
        assertEquals('false', $this->properties->value('scalar', 'boolValue9', 'otherValue'));
        assertEquals('off', $this->properties->value('scalar', 'boolValue10', 'otherValue'));
        assertEquals('other', $this->properties->value('scalar', 'boolValue11', 'otherValue'));
        assertEquals('otherValue', $this->properties->value('scalar', 'boolValue12', 'otherValue'));

        assertEquals('[foo|bar|baz]', $this->properties->value('array', 'arrayValue1', 'otherValue'));
        assertEquals('[]', $this->properties->value('array', 'arrayValue2', 'otherValue'));
        assertEquals('[foo:bar|baz]', $this->properties->value('array', 'hashValue1', 'otherValue'));
        assertEquals('[]', $this->properties->value('array', 'hashValue2', 'otherValue'));
        assertEquals('otherValue', $this->properties->value('array', 'hashValue3', 'otherValue'));

        assertEquals('1..5', $this->properties->value('range', 'rangeValue1', 'otherValue'));
        assertEquals('a..e', $this->properties->value('range', 'rangeValue2', 'otherValue'));
        assertEquals('1..', $this->properties->value('range', 'rangeValue3', 'otherValue'));
        assertEquals('a..', $this->properties->value('range', 'rangeValue4', 'otherValue'));
        assertEquals('..5', $this->properties->value('range', 'rangeValue5', 'otherValue'));
        assertEquals('..e', $this->properties->value('range', 'rangeValue6', 'otherValue'));
        assertEquals('5..1', $this->properties->value('range', 'rangeValue7', 'otherValue'));
        assertEquals('e..a', $this->properties->value('range', 'rangeValue8', 'otherValue'));
        assertEquals('otherValue', $this->properties->value('range', 'rangeValue9', 'otherValue'));

        assertEquals('otherValue', $this->properties->value('empty', 'any', 'otherValue'));
        assertEquals('otherValue', $this->properties->value('doesNotExist', 'any', 'otherValue'));
    }

    /**
     * @test
     */
    public function valueWithDefaultValueReturnsDefaultValueIfValueDoesNotExist()
    {
        assertEquals('otherValue', $this->properties->value('scalar', 'boolValue12', 'otherValue'));
        assertEquals('otherValue', $this->properties->value('array', 'hashValue3', 'otherValue'));
        assertEquals('otherValue', $this->properties->value('range', 'rangeValue9', 'otherValue'));
        assertEquals('otherValue', $this->properties->value('empty', 'any', 'otherValue'));
    }

    /**
     * @test
     */
    public function valueWithDefaultValueReturnsDefaultValueIfSectionDoesNotExist()
    {
        assertEquals(
                'otherValue',
                $this->properties->value('doesNotExist', 'any', 'otherValue')
        );
    }

    /**
     * @test
     * @group  bug249
     */
    public function iteratingOverInstanceIteratesOverSections()
    {
        foreach ($this->properties as $section => $sectionData) {
            assertTrue($this->properties->containSection($section));
            assertEquals($sectionData, $this->properties->section($section));
        }
    }

    /**
     * @test
     * @group  bug249
     * @since  1.3.2
     */
    public function iteratingAfterIterationShouldRestartIteration()
    {
        $firstIterationEntries = 0;
        foreach ($this->properties as $section => $sectionData) {
            assertTrue($this->properties->containSection($section));
            assertEquals($sectionData, $this->properties->section($section));
            $firstIterationEntries++;
        }

        $secondIterationEntries = 0;
        foreach ($this->properties as $section => $sectionData) {
            assertTrue($this->properties->containSection($section));
            assertEquals($sectionData, $this->properties->section($section));
            $secondIterationEntries++;
        }

        assertEquals($firstIterationEntries, $secondIterationEntries);
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\FileNotFoundException
     */
    public function fromNonExistantFileThrowsFileNotFoundException()
    {
        parsePropertiesFile(__DIR__ . '/doesNotExist.ini');
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function invalidIniFileThrowsIOException()
    {
        $root = vfsStream::setup('config');
        vfsStream::newFile('invalid.ini')
                 ->at($root)
                 ->withContent("[invalid{");
        parsePropertiesFile(vfsStream::url('config/invalid.ini'));
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
        $properties = parsePropertiesFile(vfsStream::url('config/test.ini'));
        assertInstanceOf('stubbles\lang\Properties', $properties);
        assertTrue($properties->containSection('foo'));
        assertEquals(['bar' => 'baz'], $properties->section('foo'));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  2.0.0
     * @group  bug213
     */
    public function invalidIniStringThrowsException()
    {
        parseProperties("[invalid{");
    }

    /**
     * @test
     * @since  2.0.0
     * @group  bug213
     */
    public function validIniStringReturnsInstance()
    {
        $properties = parseProperties("[foo]\nbar=baz");
        assertInstanceOf('stubbles\lang\Properties', $properties);
        assertTrue($properties->containSection('foo'));
        assertEquals(['bar' => 'baz'], $properties->section('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function mergeMergesTwoPropertiesInstancesAndReturnsNewInstance()
    {
        $properties1 = properties(['foo' => ['bar' => 'baz']]);
        $properties2 = properties(['bar' => ['bar' => 'baz']]);
        $resultProperties = $properties1->merge($properties2);
        assertNotSame($resultProperties, $properties1);
        assertNotSame($resultProperties, $properties2);
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function mergeMergesProperties()
    {
        $properties1 = properties(['foo' => ['bar' => 'baz']]);
        $properties2 = properties(['bar' => ['bar' => 'baz']]);
        $resultProperties = $properties1->merge($properties2);
        assertTrue($resultProperties->containSection('foo'));
        assertEquals(['bar' => 'baz'], $resultProperties->section('foo'));
        assertTrue($resultProperties->containSection('bar'));
        assertEquals(['bar' => 'baz'], $resultProperties->section('bar'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function mergeOverwritesSectionsOfMergingInstanceWithThoseFromMergedInstance()
    {
        $properties1 = properties(['foo' => ['bar' => 'baz'],
                                   'bar' => ['baz' => 'foo']
                                  ]
                       );
        $properties2 = properties(['bar' => ['bar' => 'baz']]);
        $resultProperties = $properties1->merge($properties2);
        assertTrue($resultProperties->containSection('foo'));
        assertEquals(['bar' => 'baz'], $resultProperties->section('foo'));
        assertTrue($resultProperties->containSection('bar'));
        assertEquals(['bar' => 'baz'], $resultProperties->section('bar'));
    }

    /**
     * @test
     * @group  secure_string
     * @since  4.0.0
     */
    public function propertiesWithKeyPasswordBecomeInstancesOfSecureString()
    {
        assertInstanceOf(
                'stubbles\lang\SecureString',
                properties(['foo' => ['password' => 'baz']])->value('foo', 'password')
        );
    }

    /**
     * @test
     * @group  secure_string
     * @since  4.1.2
     */
    public function propertiesWhereKeyEndsWithPasswordBecomeInstancesOfSecureString()
    {
        assertInstanceOf(
                'stubbles\lang\SecureString',
                properties(['foo' => ['example.another.password' => 'baz']])
                        ->value('foo', 'example.another.password')
        );
    }

    /**
     * @test
     * @group  secure_string
     * @since  4.1.0
     */
    public function parseSecureStringValueReturnsSecureStringInstance()
    {
        assertInstanceOf(
                'stubbles\lang\SecureString',
                properties(['foo' => ['password' => 'baz']])
                        ->parseValue('foo', 'password')
        );
    }

    /**
     * @test
     * @group  secure_string
     * @since  5.0.0
     * @expectedException   LogicException
     */
    public function parseSecureStringThrowsIllegalAccessException()
    {
        assertInstanceOf(
                'stubbles\lang\SecureString',
                properties(['foo' => ['password' => 'baz']])
                        ->parse('foo', 'password')
        );
    }

    /**
     * @return  array
     */
    public function parseValueList()
    {
        return [
            ['This is a string', 'scalar', 'stringValue'],
            [303, 'scalar', 'intValue1'],
            [303, 'scalar', 'intValue2'],
            [3.13, 'scalar', 'floatValue1'],
            [3.13, 'scalar', 'floatValue2'],
            [1, 'scalar', 'boolValue1'],
            [1, 'scalar', 'boolValue2'],
            [true, 'scalar', 'boolValue3'],
            [true, 'scalar', 'boolValue4'],
            [true, 'scalar', 'boolValue5'],
            [0, 'scalar', 'boolValue6'],
            [0, 'scalar', 'boolValue7'],
            [false, 'scalar', 'boolValue8'],
            [false, 'scalar', 'boolValue9'],
            [false, 'scalar', 'boolValue10'],
            [['foo', 'bar', 'baz'], 'array', 'arrayValue1'],
            [[], 'array', 'arrayValue2'],
            [['foo' => 'bar', 'baz'], 'array', 'hashValue1'],
            [[], 'array', 'hashValue2'],
            [[1, 2, 3, 4, 5], 'range', 'rangeValue1'],
            [['a', 'b', 'c', 'd', 'e'], 'range', 'rangeValue2'],
            [[], 'range', 'rangeValue3'],
            [[], 'range', 'rangeValue4'],
            [[], 'range', 'rangeValue5'],
            [[], 'range', 'rangeValue6'],
            [[5, 4, 3, 2, 1], 'range', 'rangeValue7'],
            [['e', 'd', 'c', 'b', 'a'], 'range', 'rangeValue8']
        ];
    }

    /**
     * @param  mixed   $expected
     * @param  string  $section
     * @param  string  $key
     * @test
     * @dataProvider  parseValueList
     * @since  4.1.0
     */
    public function parseValueReturnsValueCastedToRecognizedType($expected, $section, $key)
    {
        assertTrue($expected === $this->properties->parseValue($section, $key));
    }

    /**
     * @test
     * @since  4.1.0
     */
    public function parseValueWithNonExistingKeyReturnsDefault()
    {
        assertEquals(
                6100,
                $this->properties->parseValue('empty', 'doesNotExist', 6100)
        );
    }

    /**
     * @test
     * @since  4.1.0
     */
    public function parseValueFromNonExistingSectionReturnsDefault()
    {
        assertEquals(
                6100,
                $this->properties->parseValue('doesNotExist', 'rangeValue8', 6100)
        );
    }

    /**
     * @return  array
     * @since  5.0.0
     */
    public function parseList()
    {
        return [
            ['This is a string', 'scalar', 'stringValue', 'asString'],
            [303, 'scalar', 'intValue1', 'asInt'],
            [303, 'scalar', 'intValue2', 'asInt'],
            [3.13, 'scalar', 'floatValue1', 'asFloat'],
            [3.13, 'scalar', 'floatValue2', 'asFloat'],
            [false, 'scalar', 'boolValue1', 'asBool'],
            [false, 'scalar', 'boolValue2', 'asBool'],
            [true, 'scalar', 'boolValue3', 'asBool'],
            [true, 'scalar', 'boolValue4', 'asBool'],
            [true, 'scalar', 'boolValue5', 'asBool'],
            [false, 'scalar', 'boolValue6', 'asBool'],
            [false, 'scalar', 'boolValue7', 'asBool'],
            [false, 'scalar', 'boolValue8', 'asBool'],
            [false, 'scalar', 'boolValue9', 'asBool'],
            [false, 'scalar', 'boolValue10', 'asBool'],
            [['foo', 'bar', 'baz'], 'array', 'arrayValue1', 'asList'],
            [[], 'array', 'arrayValue2', 'asList'],
            [['foo' => 'bar', 'baz'], 'array', 'hashValue1', 'asMap'],
            [[], 'array', 'hashValue2', 'asMap'],
            [[1, 2, 3, 4, 5], 'range', 'rangeValue1', 'asRange'],
            [['a', 'b', 'c', 'd', 'e'], 'range', 'rangeValue2', 'asRange'],
            [[], 'range', 'rangeValue3', 'asRange'],
            [[], 'range', 'rangeValue4', 'asRange'],
            [[], 'range', 'rangeValue5', 'asRange'],
            [[], 'range', 'rangeValue6', 'asRange'],
            [[5, 4, 3, 2, 1], 'range', 'rangeValue7', 'asRange'],
            [['e', 'd', 'c', 'b', 'a'], 'range', 'rangeValue8', 'asRange']
        ];
    }

    /**
     * @param  mixed   $expected
     * @param  string  $section
     * @param  string  $key
     * @param  string  $type
     * @test
     * @dataProvider  parseList
     * @since  5.0.0
     */
    public function parseReturnsValueCastedToRecognizedType($expected, $section, $key, $type)
    {
        assertEquals(
                $expected,
                $this->properties->parse($section, $key)->$type()
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function parseNonExistingReturnsNullInstance()
    {
        assertEquals(
                new Parse(null),
                $this->properties->parse('empty', 'doesNotExist')
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function parseFromNonExistingSectionReturnsDefault()
    {
        assertEquals(
                new Parse(null),
                $this->properties->parse('doesNotExist', 'rangeValue8')
        );
    }
}
