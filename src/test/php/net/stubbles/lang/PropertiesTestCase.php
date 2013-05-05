<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang;
use org\bovigo\vfs\vfsStream;
/**
 * Tests for net\stubbles\lang\Properties.
 *
 * @group  lang
 * @group  lang_core
 */
class PropertiesTestCase extends \PHPUnit_Framework_TestCase
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
        $this->properties = properties(array('scalar' => array('stringValue' => 'This is a string',
                                                               'intValue1'   => '303',
                                                               'intValue2'   => 303,
                                                               'floatValue1' => '3.13',
                                                               'floatValue2' => 3.13,
                                                               'boolValue1'  => '1',
                                                               'boolValue2'  => 1,
                                                               'boolValue3'  => 'yes',
                                                               'boolValue4'  => 'true',                                                                       'boolValue5'  => 'on',
                                                               'boolValue6'  => '0',
                                                               'boolValue7'  => 0,
                                                               'boolValue8'  => 'no',
                                                               'boolValue9'  => 'false',
                                                               'boolValue10' => 'off',
                                                               'boolValue11' => 'other'
                                                         ),
                                             'array'  => array('arrayValue1' => 'foo|bar|baz',
                                                               'arrayValue2' => '',
                                                               'hashValue1'  => 'foo:bar|baz',
                                                               'hashValue2'  => ''
                                                         ),
                                             'range'  => array('rangeValue1' => '1..5',
                                                               'rangeValue2' => 'a..e',
                                                               'rangeValue3' => '1..',
                                                               'rangeValue4' => 'a..',
                                                               'rangeValue5' => '..5',
                                                               'rangeValue6' => '..e',
                                                               'rangeValue7' => '5..1',
                                                               'rangeValue8' => 'e..a'
                                                         ),
                                             'empty'  => array()
                                       )
                            );
    }

    /**
     * @test
     */
    public function getSectionsReturnsListOfSectionKeys()
    {
        $this->assertEquals(array('scalar', 'array', 'range', 'empty'),
                            $this->properties->getSections()
        );
    }

    /**
     * @test
     */
    public function hasSectionReturnsTrueForExistingSections()
    {
        $this->assertTrue($this->properties->hasSection('scalar'));
        $this->assertTrue($this->properties->hasSection('array'));
        $this->assertTrue($this->properties->hasSection('range'));
        $this->assertTrue($this->properties->hasSection('empty'));
    }

    /**
     * @test
     */
    public function hasSectionReturnsFalseForNonExistingSections()
    {
        $this->assertFalse($this->properties->hasSection('doesNotExist'));
    }

    /**
     * @test
     */
    public function getSectionWithoutDefaultValueReturnsSectionValues()
    {
        $this->assertEquals(array('stringValue' => 'This is a string',
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
                            ),
                            $this->properties->getSection('scalar')
        );
        $this->assertEquals(array('arrayValue1' => 'foo|bar|baz',
                                  'arrayValue2' => '',
                                  'hashValue1'  => 'foo:bar|baz',
                                  'hashValue2'  => ''
                            ),
                            $this->properties->getSection('array')
        );
        $this->assertEquals(array('rangeValue1' => '1..5',
                                  'rangeValue2' => 'a..e',
                                  'rangeValue3' => '1..',
                                  'rangeValue4' => 'a..',
                                  'rangeValue5' => '..5',
                                  'rangeValue6' => '..e',
                                  'rangeValue7' => '5..1',
                                  'rangeValue8' => 'e..a'
                            ),
                            $this->properties->getSection('range')
        );
        $this->assertEquals(array(),
                            $this->properties->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function getSectionWithoutDefaultValueReturnsEmptyArrayIfSectionDoesNotExist()
    {
        $this->assertEquals(array(),
                            $this->properties->getSection('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function getSectionWithDefaultValueReturnsSectionValues()
    {
        $this->assertEquals(array('stringValue' => 'This is a string',
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
                            ),
                            $this->properties->getSection('scalar', array('foo' => 'bar'))
        );
        $this->assertEquals(array('arrayValue1' => 'foo|bar|baz',
                                  'arrayValue2' => '',
                                  'hashValue1'  => 'foo:bar|baz',
                                  'hashValue2'  => ''
                            ),
                            $this->properties->getSection('array', array('foo' => 'bar'))
        );
        $this->assertEquals(array('rangeValue1' => '1..5',
                                  'rangeValue2' => 'a..e',
                                  'rangeValue3' => '1..',
                                  'rangeValue4' => 'a..',
                                  'rangeValue5' => '..5',
                                  'rangeValue6' => '..e',
                                  'rangeValue7' => '5..1',
                                  'rangeValue8' => 'e..a'
                            ),
                            $this->properties->getSection('range', array('foo' => 'bar'))
        );
        $this->assertEquals(array(),
                            $this->properties->getSection('empty', array('foo' => 'bar'))
        );
    }

    /**
     * @test
     */
    public function getSectionWithDefaultValueReturnsDefaultValueIfSectionDoesNotExist()
    {
        $this->assertEquals(array('foo' => 'bar'),
                            $this->properties->getSection('doesNotExist', array('foo' => 'bar'))
        );
    }

    /**
     * @test
     */
    public function getSectionKeysReturnsListOfKeysForGivenSection()
    {
        $this->assertEquals(array('stringValue',
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
                            ),
                            $this->properties->getSectionKeys('scalar', array('foo', 'bar'))
        );
        $this->assertEquals(array('arrayValue1',
                                  'arrayValue2',
                                  'hashValue1',
                                  'hashValue2'
                            ),
                            $this->properties->getSectionKeys('array', array('foo', 'bar'))
        );
        $this->assertEquals(array('rangeValue1',
                                  'rangeValue2',
                                  'rangeValue3',
                                  'rangeValue4',
                                  'rangeValue5',
                                  'rangeValue6',
                                  'rangeValue7',
                                  'rangeValue8'
                            ),
                            $this->properties->getSectionKeys('range', array('foo', 'bar'))
        );
        $this->assertEquals(array(),
                            $this->properties->getSectionKeys('empty', array('foo', 'bar'))
        );
    }

    /**
     * @test
     */
    public function getSectionKeysReturnsDefaultListOfSectionDoesNotExist()
    {
        $this->assertEquals(array('foo', 'bar'),
                            $this->properties->getSectionKeys('doesNotExist', array('foo', 'bar'))
        );
    }

    /**
     * @test
     */
    public function hasValueReturnsTrueIfValueExist()
    {
        $this->assertTrue($this->properties->hasValue('scalar', 'stringValue'));
        $this->assertTrue($this->properties->hasValue('scalar', 'intValue1'));
        $this->assertTrue($this->properties->hasValue('scalar', 'intValue2'));
        $this->assertTrue($this->properties->hasValue('scalar', 'floatValue1'));
        $this->assertTrue($this->properties->hasValue('scalar', 'floatValue2'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue1'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue2'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue3'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue4'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue5'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue6'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue7'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue8'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue9'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue10'));
        $this->assertTrue($this->properties->hasValue('scalar', 'boolValue11'));

        $this->assertTrue($this->properties->hasValue('array', 'arrayValue1'));
        $this->assertTrue($this->properties->hasValue('array', 'arrayValue2'));
        $this->assertTrue($this->properties->hasValue('array', 'hashValue1'));
        $this->assertTrue($this->properties->hasValue('array', 'hashValue2'));

        $this->assertTrue($this->properties->hasValue('range', 'rangeValue1'));
        $this->assertTrue($this->properties->hasValue('range', 'rangeValue2'));
        $this->assertTrue($this->properties->hasValue('range', 'rangeValue3'));
        $this->assertTrue($this->properties->hasValue('range', 'rangeValue4'));
        $this->assertTrue($this->properties->hasValue('range', 'rangeValue5'));
        $this->assertTrue($this->properties->hasValue('range', 'rangeValue6'));
        $this->assertTrue($this->properties->hasValue('range', 'rangeValue7'));
        $this->assertTrue($this->properties->hasValue('range', 'rangeValue8'));
    }

    /**
     * @test
     */
    public function hasValueReturnsFalseIfValueDoesNotExist()
    {
        $this->assertFalse($this->properties->hasValue('scalar', 'boolValue12'));
        $this->assertFalse($this->properties->hasValue('array', 'hashValue3'));
        $this->assertFalse($this->properties->hasValue('range', 'rangeValue9'));
        $this->assertFalse($this->properties->hasValue('empty', 'any'));
    }

    /**
     * @test
     */
    public function hasValueReturnsFalseIfSectionDoesNotExist()
    {
        $this->assertFalse($this->properties->hasValue('doesNotExist', 'any'));
    }

    /**
     * @test
     */
    public function getValueWithoutDefaultValueReturnsValueIfExists()
    {
        $this->assertEquals('This is a string', $this->properties->getValue('scalar', 'stringValue'));
        $this->assertEquals('303', $this->properties->getValue('scalar', 'intValue1'));
        $this->assertEquals(303, $this->properties->getValue('scalar', 'intValue2'));
        $this->assertEquals('3.13', $this->properties->getValue('scalar', 'floatValue1'));
        $this->assertEquals(3.13, $this->properties->getValue('scalar', 'floatValue2'));
        $this->assertEquals('1', $this->properties->getValue('scalar', 'boolValue1'));
        $this->assertEquals(1, $this->properties->getValue('scalar', 'boolValue2'));
        $this->assertEquals('yes', $this->properties->getValue('scalar', 'boolValue3'));
        $this->assertEquals('true', $this->properties->getValue('scalar', 'boolValue4'));
        $this->assertEquals('on', $this->properties->getValue('scalar', 'boolValue5'));
        $this->assertEquals('0', $this->properties->getValue('scalar', 'boolValue6'));
        $this->assertEquals(0, $this->properties->getValue('scalar', 'boolValue7'));
        $this->assertEquals('no', $this->properties->getValue('scalar', 'boolValue8'));
        $this->assertEquals('false', $this->properties->getValue('scalar', 'boolValue9'));
        $this->assertEquals('off', $this->properties->getValue('scalar', 'boolValue10'));
        $this->assertEquals('other', $this->properties->getValue('scalar', 'boolValue11'));

        $this->assertEquals('foo|bar|baz', $this->properties->getValue('array', 'arrayValue1'));
        $this->assertEquals('', $this->properties->getValue('array', 'arrayValue2'));
        $this->assertEquals('foo:bar|baz', $this->properties->getValue('array', 'hashValue1'));
        $this->assertEquals('', $this->properties->getValue('array', 'hashValue2'));

        $this->assertEquals('1..5', $this->properties->getValue('range', 'rangeValue1'));
        $this->assertEquals('a..e', $this->properties->getValue('range', 'rangeValue2'));
        $this->assertEquals('1..', $this->properties->getValue('range', 'rangeValue3'));
        $this->assertEquals('a..', $this->properties->getValue('range', 'rangeValue4'));
        $this->assertEquals('..5', $this->properties->getValue('range', 'rangeValue5'));
        $this->assertEquals('..e', $this->properties->getValue('range', 'rangeValue6'));
        $this->assertEquals('5..1', $this->properties->getValue('range', 'rangeValue7'));
        $this->assertEquals('e..a', $this->properties->getValue('range', 'rangeValue8'));
    }

    /**
     * @test
     */
    public function getValueWithoutDefaultValueReturnsNullIfValueDoesNotExist()
    {
        $this->assertNull($this->properties->getValue('scalar', 'boolValue12'));
        $this->assertNull($this->properties->getValue('array', 'hashValue3'));
        $this->assertNull($this->properties->getValue('range', 'rangeValue9'));
        $this->assertNull($this->properties->getValue('empty', 'any'));
    }

    /**
     * @test
     */
    public function getValueWithoutDefaultValueReturnsNullIfSectionDoesNotExist()
    {
        $this->assertNull($this->properties->getValue('doesNotExist', 'any'));
    }

    /**
     * @test
     */
    public function getValueWithDefaultValueReturnsValueIfExists()
    {
        $this->assertEquals('This is a string', $this->properties->getValue('scalar', 'stringValue', 'otherValue'));
        $this->assertEquals('303', $this->properties->getValue('scalar', 'intValue1', 'otherValue'));
        $this->assertEquals(303, $this->properties->getValue('scalar', 'intValue2', 'otherValue'));
        $this->assertEquals('3.13', $this->properties->getValue('scalar', 'floatValue1', 'otherValue'));
        $this->assertEquals(3.13, $this->properties->getValue('scalar', 'floatValue2', 'otherValue'));
        $this->assertEquals('1', $this->properties->getValue('scalar', 'boolValue1', 'otherValue'));
        $this->assertEquals(1, $this->properties->getValue('scalar', 'boolValue2', 'otherValue'));
        $this->assertEquals('yes', $this->properties->getValue('scalar', 'boolValue3', 'otherValue'));
        $this->assertEquals('true', $this->properties->getValue('scalar', 'boolValue4', 'otherValue'));
        $this->assertEquals('on', $this->properties->getValue('scalar', 'boolValue5', 'otherValue'));
        $this->assertEquals('0', $this->properties->getValue('scalar', 'boolValue6', 'otherValue'));
        $this->assertEquals(0, $this->properties->getValue('scalar', 'boolValue7', 'otherValue'));
        $this->assertEquals('no', $this->properties->getValue('scalar', 'boolValue8', 'otherValue'));
        $this->assertEquals('false', $this->properties->getValue('scalar', 'boolValue9', 'otherValue'));
        $this->assertEquals('off', $this->properties->getValue('scalar', 'boolValue10', 'otherValue'));
        $this->assertEquals('other', $this->properties->getValue('scalar', 'boolValue11', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->getValue('scalar', 'boolValue12', 'otherValue'));

        $this->assertEquals('foo|bar|baz', $this->properties->getValue('array', 'arrayValue1', 'otherValue'));
        $this->assertEquals('', $this->properties->getValue('array', 'arrayValue2', 'otherValue'));
        $this->assertEquals('foo:bar|baz', $this->properties->getValue('array', 'hashValue1', 'otherValue'));
        $this->assertEquals('', $this->properties->getValue('array', 'hashValue2', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->getValue('array', 'hashValue3', 'otherValue'));

        $this->assertEquals('1..5', $this->properties->getValue('range', 'rangeValue1', 'otherValue'));
        $this->assertEquals('a..e', $this->properties->getValue('range', 'rangeValue2', 'otherValue'));
        $this->assertEquals('1..', $this->properties->getValue('range', 'rangeValue3', 'otherValue'));
        $this->assertEquals('a..', $this->properties->getValue('range', 'rangeValue4', 'otherValue'));
        $this->assertEquals('..5', $this->properties->getValue('range', 'rangeValue5', 'otherValue'));
        $this->assertEquals('..e', $this->properties->getValue('range', 'rangeValue6', 'otherValue'));
        $this->assertEquals('5..1', $this->properties->getValue('range', 'rangeValue7', 'otherValue'));
        $this->assertEquals('e..a', $this->properties->getValue('range', 'rangeValue8', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->getValue('range', 'rangeValue9', 'otherValue'));

        $this->assertEquals('otherValue', $this->properties->getValue('empty', 'any', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->getValue('doesNotExist', 'any', 'otherValue'));
    }

    /**
     * @test
     */
    public function getValueWithDefaultValueReturnsDefaultValueIfValueDoesNotExist()
    {
        $this->assertEquals('otherValue', $this->properties->getValue('scalar', 'boolValue12', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->getValue('array', 'hashValue3', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->getValue('range', 'rangeValue9', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->getValue('empty', 'any', 'otherValue'));
    }

    /**
     * @test
     */
    public function getValueWithDefaultValueReturnsDefaultValueIfSectionDoesNotExist()
    {
        $this->assertEquals('otherValue', $this->properties->getValue('doesNotExist', 'any', 'otherValue'));
    }

    /**
     * parseString() without default value
     *
     * @test
     */
    public function parseStringWithoutDefaultValue()
    {
        $this->assertEquals('This is a string', $this->properties->parseString('scalar', 'stringValue'));
        $this->assertEquals('303', $this->properties->parseString('scalar', 'intValue1'));
        $this->assertEquals('303', $this->properties->parseString('scalar', 'intValue2'));
        $this->assertEquals('3.13', $this->properties->parseString('scalar', 'floatValue1'));
        $this->assertEquals('3.13', $this->properties->parseString('scalar', 'floatValue2'));
        $this->assertEquals('1', $this->properties->parseString('scalar', 'boolValue1'));
        $this->assertEquals('1', $this->properties->parseString('scalar', 'boolValue2'));
        $this->assertEquals('yes', $this->properties->parseString('scalar', 'boolValue3'));
        $this->assertEquals('true', $this->properties->parseString('scalar', 'boolValue4'));
        $this->assertEquals('on', $this->properties->parseString('scalar', 'boolValue5'));
        $this->assertEquals('0', $this->properties->parseString('scalar', 'boolValue6'));
        $this->assertEquals('0', $this->properties->parseString('scalar', 'boolValue7'));
        $this->assertEquals('no', $this->properties->parseString('scalar', 'boolValue8'));
        $this->assertEquals('false', $this->properties->parseString('scalar', 'boolValue9'));
        $this->assertEquals('off', $this->properties->parseString('scalar', 'boolValue10'));
        $this->assertEquals('other', $this->properties->parseString('scalar', 'boolValue11'));
        $this->assertNull($this->properties->parseString('scalar', 'boolValue12'));

        $this->assertEquals('foo|bar|baz', $this->properties->parseString('array', 'arrayValue1'));
        $this->assertEquals('', $this->properties->parseString('array', 'arrayValue2'));
        $this->assertEquals('foo:bar|baz', $this->properties->parseString('array', 'hashValue1'));
        $this->assertEquals('', $this->properties->parseString('array', 'hashValue2'));
        $this->assertNull($this->properties->parseString('array', 'hashValue3'));

        $this->assertEquals('1..5', $this->properties->parseString('range', 'rangeValue1'));
        $this->assertEquals('a..e', $this->properties->parseString('range', 'rangeValue2'));
        $this->assertEquals('1..', $this->properties->parseString('range', 'rangeValue3'));
        $this->assertEquals('a..', $this->properties->parseString('range', 'rangeValue4'));
        $this->assertEquals('..5', $this->properties->parseString('range', 'rangeValue5'));
        $this->assertEquals('..e', $this->properties->parseString('range', 'rangeValue6'));
        $this->assertEquals('5..1', $this->properties->parseString('range', 'rangeValue7'));
        $this->assertEquals('e..a', $this->properties->parseString('range', 'rangeValue8'));
        $this->assertNull($this->properties->parseString('range', 'rangeValue9'));

        $this->assertNull($this->properties->parseString('empty', 'any'));
        $this->assertNull($this->properties->parseString('doesNotExist', 'any'));
    }

    /**
     * parseString() with default value
     *
     * @test
     */
    public function parseStringWithDefaultValue()
    {
        $this->assertEquals('This is a string', $this->properties->parseString('scalar', 'stringValue', 'otherValue'));
        $this->assertEquals('303', $this->properties->parseString('scalar', 'intValue1', 'otherValue'));
        $this->assertEquals('303', $this->properties->parseString('scalar', 'intValue2', 'otherValue'));
        $this->assertEquals('3.13', $this->properties->parseString('scalar', 'floatValue1', 'otherValue'));
        $this->assertEquals('3.13', $this->properties->parseString('scalar', 'floatValue2', 'otherValue'));
        $this->assertEquals('1', $this->properties->parseString('scalar', 'boolValue1', 'otherValue'));
        $this->assertEquals('1', $this->properties->parseString('scalar', 'boolValue2', 'otherValue'));
        $this->assertEquals('yes', $this->properties->parseString('scalar', 'boolValue3', 'otherValue'));
        $this->assertEquals('true', $this->properties->parseString('scalar', 'boolValue4', 'otherValue'));
        $this->assertEquals('on', $this->properties->parseString('scalar', 'boolValue5', 'otherValue'));
        $this->assertEquals('0', $this->properties->parseString('scalar', 'boolValue6', 'otherValue'));
        $this->assertEquals('0', $this->properties->parseString('scalar', 'boolValue7', 'otherValue'));
        $this->assertEquals('no', $this->properties->parseString('scalar', 'boolValue8', 'otherValue'));
        $this->assertEquals('false', $this->properties->parseString('scalar', 'boolValue9', 'otherValue'));
        $this->assertEquals('off', $this->properties->parseString('scalar', 'boolValue10', 'otherValue'));
        $this->assertEquals('other', $this->properties->parseString('scalar', 'boolValue11', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->parseString('scalar', 'boolValue12', 'otherValue'));

        $this->assertEquals('foo|bar|baz', $this->properties->parseString('array', 'arrayValue1', 'otherValue'));
        $this->assertEquals('', $this->properties->parseString('array', 'arrayValue2', 'otherValue'));
        $this->assertEquals('foo:bar|baz', $this->properties->parseString('array', 'hashValue1', 'otherValue'));
        $this->assertEquals('', $this->properties->parseString('array', 'hashValue2', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->parseString('array', 'hashValue3', 'otherValue'));

        $this->assertEquals('1..5', $this->properties->parseString('range', 'rangeValue1', 'otherValue'));
        $this->assertEquals('a..e', $this->properties->parseString('range', 'rangeValue2', 'otherValue'));
        $this->assertEquals('1..', $this->properties->parseString('range', 'rangeValue3', 'otherValue'));
        $this->assertEquals('a..', $this->properties->parseString('range', 'rangeValue4', 'otherValue'));
        $this->assertEquals('..5', $this->properties->parseString('range', 'rangeValue5', 'otherValue'));
        $this->assertEquals('..e', $this->properties->parseString('range', 'rangeValue6', 'otherValue'));
        $this->assertEquals('5..1', $this->properties->parseString('range', 'rangeValue7', 'otherValue'));
        $this->assertEquals('e..a', $this->properties->parseString('range', 'rangeValue8', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->parseString('range', 'rangeValue9', 'otherValue'));

        $this->assertEquals('otherValue', $this->properties->parseString('empty', 'any', 'otherValue'));
        $this->assertEquals('otherValue', $this->properties->parseString('doesNotExist', 'any', 'otherValue'));
    }

    /**
     * parseInt() without default value
     *
     * @test
     */
    public function parseIntWithoutDefaultValue()
    {
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'stringValue'));
        $this->assertEquals(303, $this->properties->parseInt('scalar', 'intValue1'));
        $this->assertEquals(303, $this->properties->parseInt('scalar', 'intValue2'));
        $this->assertEquals(3, $this->properties->parseInt('scalar', 'floatValue1'));
        $this->assertEquals(3, $this->properties->parseInt('scalar', 'floatValue2'));
        $this->assertEquals(1, $this->properties->parseInt('scalar', 'boolValue1'));
        $this->assertEquals(1, $this->properties->parseInt('scalar', 'boolValue2'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue3'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue4'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue5'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue6'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue7'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue8'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue9'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue10'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue11'));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue12'));

        $this->assertEquals(0, $this->properties->parseInt('array', 'arrayValue1'));
        $this->assertEquals(0, $this->properties->parseInt('array', 'arrayValue2'));
        $this->assertEquals(0, $this->properties->parseInt('array', 'hashValue1'));
        $this->assertEquals(0, $this->properties->parseInt('array', 'hashValue2'));
        $this->assertEquals(0, $this->properties->parseInt('array', 'hashValue3'));

        $this->assertEquals(1, $this->properties->parseInt('range', 'rangeValue1'));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue2'));
        $this->assertEquals(1, $this->properties->parseInt('range', 'rangeValue3'));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue4'));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue5'));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue6'));
        $this->assertEquals(5, $this->properties->parseInt('range', 'rangeValue7'));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue8'));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue9'));

        $this->assertEquals(0, $this->properties->parseInt('empty', 'any'));
        $this->assertEquals(0, $this->properties->parseInt('doesNotExist', 'any'));
    }

    /**
     * parseInt() with default value
     *
     * @test
     */
    public function parseIntWithDefaultValue()
    {
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'stringValue', 404));
        $this->assertEquals(303, $this->properties->parseInt('scalar', 'intValue1', 404));
        $this->assertEquals(303, $this->properties->parseInt('scalar', 'intValue2', 404));
        $this->assertEquals(3, $this->properties->parseInt('scalar', 'floatValue1', 404));
        $this->assertEquals(3, $this->properties->parseInt('scalar', 'floatValue2', 404));
        $this->assertEquals(1, $this->properties->parseInt('scalar', 'boolValue1', 404));
        $this->assertEquals(1, $this->properties->parseInt('scalar', 'boolValue2', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue3', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue4', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue5', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue6', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue7', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue8', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue9', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue10', 404));
        $this->assertEquals(0, $this->properties->parseInt('scalar', 'boolValue11', 404));
        $this->assertEquals(404, $this->properties->parseInt('scalar', 'boolValue12', 404));

        $this->assertEquals(0, $this->properties->parseInt('array', 'arrayValue1', 404));
        $this->assertEquals(0, $this->properties->parseInt('array', 'arrayValue2', 404));
        $this->assertEquals(0, $this->properties->parseInt('array', 'hashValue1', 404));
        $this->assertEquals(0, $this->properties->parseInt('array', 'hashValue2', 404));
        $this->assertEquals(404, $this->properties->parseInt('array', 'hashValue3', 404));

        $this->assertEquals(1, $this->properties->parseInt('range', 'rangeValue1', 404));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue2', 404));
        $this->assertEquals(1, $this->properties->parseInt('range', 'rangeValue3', 404));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue4', 404));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue5', 404));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue6', 404));
        $this->assertEquals(5, $this->properties->parseInt('range', 'rangeValue7', 404));
        $this->assertEquals(0, $this->properties->parseInt('range', 'rangeValue8', 404));
        $this->assertEquals(404, $this->properties->parseInt('range', 'rangeValue9', 404));

        $this->assertEquals(404, $this->properties->parseInt('empty', 'any', 404));
        $this->assertEquals(404, $this->properties->parseInt('doesNotExist', 'any', 404));
    }

    /**
     * parseFloat() without default value
     *
     * @test
     */
    public function parseFloatWithoutDefaultValue()
    {
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'stringValue'));
        $this->assertEquals(303.0, $this->properties->parseFloat('scalar', 'intValue1'));
        $this->assertEquals(303.0, $this->properties->parseFloat('scalar', 'intValue2'));
        $this->assertEquals(3.13, $this->properties->parseFloat('scalar', 'floatValue1'));
        $this->assertEquals(3.13, $this->properties->parseFloat('scalar', 'floatValue2'));
        $this->assertEquals(1.0, $this->properties->parseFloat('scalar', 'boolValue1'));
        $this->assertEquals(1.0, $this->properties->parseFloat('scalar', 'boolValue2'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue3'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue4'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue5'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue6'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue7'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue8'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue9'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue10'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue11'));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue12'));

        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'arrayValue1'));
        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'arrayValue2'));
        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'hashValue1'));
        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'hashValue2'));
        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'hashValue3'));

        $this->assertEquals(1.0, $this->properties->parseFloat('range', 'rangeValue1'));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue2'));
        $this->assertEquals(1.0, $this->properties->parseFloat('range', 'rangeValue3'));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue4'));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue5'));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue6'));
        $this->assertEquals(5.0, $this->properties->parseFloat('range', 'rangeValue7'));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue8'));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue9'));

        $this->assertEquals(0.0, $this->properties->parseFloat('empty', 'any'));
        $this->assertEquals(0.0, $this->properties->parseFloat('doesNotExist', 'any'));
    }

    /**
     * parseFloat() with default value
     *
     * @test
     */
    public function parseFloatWithDefaultValue()
    {
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'stringValue', 40.4));
        $this->assertEquals(303.0, $this->properties->parseFloat('scalar', 'intValue1', 40.4));
        $this->assertEquals(303.0, $this->properties->parseFloat('scalar', 'intValue2', 40.4));
        $this->assertEquals(3.13, $this->properties->parseFloat('scalar', 'floatValue1', 40.4));
        $this->assertEquals(3.13, $this->properties->parseFloat('scalar', 'floatValue2', 40.4));
        $this->assertEquals(1.0, $this->properties->parseFloat('scalar', 'boolValue1', 40.4));
        $this->assertEquals(1.0, $this->properties->parseFloat('scalar', 'boolValue2', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue3', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue4', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue5', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue6', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue7', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue8', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue9', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue10', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('scalar', 'boolValue11', 40.4));
        $this->assertEquals(40.4, $this->properties->parseFloat('scalar', 'boolValue12', 40.4));

        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'arrayValue1', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'arrayValue2', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'hashValue1', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('array', 'hashValue2', 40.4));
        $this->assertEquals(40.4, $this->properties->parseFloat('array', 'hashValue3', 40.4));

        $this->assertEquals(1.0, $this->properties->parseFloat('range', 'rangeValue1', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue2', 40.4));
        $this->assertEquals(1.0, $this->properties->parseFloat('range', 'rangeValue3', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue4', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue5', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue6', 40.4));
        $this->assertEquals(5.0, $this->properties->parseFloat('range', 'rangeValue7', 40.4));
        $this->assertEquals(0.0, $this->properties->parseFloat('range', 'rangeValue8', 40.4));
        $this->assertEquals(40.4, $this->properties->parseFloat('range', 'rangeValue9', 40.4));

        $this->assertEquals(40.4, $this->properties->parseFloat('empty', 'any', 40.4));
        $this->assertEquals(40.4, $this->properties->parseFloat('doesNotExist', 'any', 40.4));
    }

    /**
     * parseBool() without default value
     *
     * @test
     */
    public function parseBoolWithoutDefaultValue()
    {
        $this->assertFalse($this->properties->parseBool('scalar', 'stringValue'));
        $this->assertFalse($this->properties->parseBool('scalar', 'intValue1'));
        $this->assertFalse($this->properties->parseBool('scalar', 'intValue2'));
        $this->assertFalse($this->properties->parseBool('scalar', 'floatValue1'));
        $this->assertFalse($this->properties->parseBool('scalar', 'floatValue2'));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue1'));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue2'));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue3'));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue4'));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue5'));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue6'));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue7'));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue8'));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue9'));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue10'));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue11'));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue12'));

        $this->assertFalse($this->properties->parseBool('array', 'arrayValue1'));
        $this->assertFalse($this->properties->parseBool('array', 'arrayValue2'));
        $this->assertFalse($this->properties->parseBool('array', 'hashValue1'));
        $this->assertFalse($this->properties->parseBool('array', 'hashValue2'));
        $this->assertFalse($this->properties->parseBool('array', 'hashValue3'));

        $this->assertFalse($this->properties->parseBool('range', 'rangeValue1'));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue2'));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue3'));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue4'));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue5'));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue6'));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue7'));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue8'));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue9'));

        $this->assertFalse($this->properties->parseBool('empty', 'any'));
        $this->assertFalse($this->properties->parseBool('doesNotExist', 'any'));
    }

    /**
     * parseBool() with default value
     *
     * @test
     */
    public function parseBoolWithDefaultValue()
    {
        $this->assertFalse($this->properties->parseBool('scalar', 'stringValue', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'intValue1', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'intValue2', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'floatValue1', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'floatValue2', true));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue1', true));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue2', true));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue3', true));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue4', true));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue5', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue6', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue7', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue8', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue9', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue10', true));
        $this->assertFalse($this->properties->parseBool('scalar', 'boolValue11', true));
        $this->assertTrue($this->properties->parseBool('scalar', 'boolValue12', true));

        $this->assertFalse($this->properties->parseBool('array', 'arrayValue1', true));
        $this->assertFalse($this->properties->parseBool('array', 'arrayValue2', true));
        $this->assertFalse($this->properties->parseBool('array', 'hashValue1', true));
        $this->assertFalse($this->properties->parseBool('array', 'hashValue2', true));
        $this->assertTrue($this->properties->parseBool('array', 'hashValue3', true));

        $this->assertFalse($this->properties->parseBool('range', 'rangeValue1', true));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue2', true));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue3', true));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue4', true));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue5', true));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue6', true));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue7', true));
        $this->assertFalse($this->properties->parseBool('range', 'rangeValue8', true));
        $this->assertTrue($this->properties->parseBool('range', 'rangeValue9', true));

        $this->assertTrue($this->properties->parseBool('empty', 'any', true));
        $this->assertTrue($this->properties->parseBool('doesNotExist', 'any', true));
    }

    /**
     * parseArray() without default value
     *
     * @test
     */
    public function parseArrayWithoutDefaultValue()
    {
        $this->assertEquals(array('This is a string'), $this->properties->parseArray('scalar', 'stringValue'));
        $this->assertEquals(array('303'), $this->properties->parseArray('scalar', 'intValue1'));
        $this->assertEquals(array(303), $this->properties->parseArray('scalar', 'intValue2'));
        $this->assertEquals(array('3.13'), $this->properties->parseArray('scalar', 'floatValue1'));
        $this->assertEquals(array(3.13), $this->properties->parseArray('scalar', 'floatValue2'));
        $this->assertEquals(array('1'), $this->properties->parseArray('scalar', 'boolValue1'));
        $this->assertEquals(array('1'), $this->properties->parseArray('scalar', 'boolValue2'));
        $this->assertEquals(array('yes'), $this->properties->parseArray('scalar', 'boolValue3'));
        $this->assertEquals(array('true'), $this->properties->parseArray('scalar', 'boolValue4'));
        $this->assertEquals(array('on'), $this->properties->parseArray('scalar', 'boolValue5'));
        $this->assertEquals(array(), $this->properties->parseArray('scalar', 'boolValue6'));
        $this->assertEquals(array(), $this->properties->parseArray('scalar', 'boolValue7'));
        $this->assertEquals(array('no'), $this->properties->parseArray('scalar', 'boolValue8'));
        $this->assertEquals(array('false'), $this->properties->parseArray('scalar', 'boolValue9'));
        $this->assertEquals(array('off'), $this->properties->parseArray('scalar', 'boolValue10'));
        $this->assertEquals(array('other'), $this->properties->parseArray('scalar', 'boolValue11'));
        $this->assertNull($this->properties->parseArray('scalar', 'boolValue12'));

        $this->assertEquals(array('foo', 'bar', 'baz'), $this->properties->parseArray('array', 'arrayValue1'));
        $this->assertEquals(array(), $this->properties->parseArray('array', 'arrayValue2'));
        $this->assertEquals(array('foo:bar', 'baz'), $this->properties->parseArray('array', 'hashValue1'));
        $this->assertEquals(array(), $this->properties->parseArray('array', 'hashValue2'));
        $this->assertNull($this->properties->parseArray('array', 'hashValue3'));

        $this->assertEquals(array('1..5'), $this->properties->parseArray('range', 'rangeValue1'));
        $this->assertEquals(array('a..e'), $this->properties->parseArray('range', 'rangeValue2'));
        $this->assertEquals(array('1..'), $this->properties->parseArray('range', 'rangeValue3'));
        $this->assertEquals(array('a..'), $this->properties->parseArray('range', 'rangeValue4'));
        $this->assertEquals(array('..5'), $this->properties->parseArray('range', 'rangeValue5'));
        $this->assertEquals(array('..e'), $this->properties->parseArray('range', 'rangeValue6'));
        $this->assertEquals(array('5..1'), $this->properties->parseArray('range', 'rangeValue7'));
        $this->assertEquals(array('e..a'), $this->properties->parseArray('range', 'rangeValue8'));
        $this->assertNull($this->properties->parseArray('range', 'rangeValue9'));

        $this->assertNull($this->properties->parseArray('empty', 'any'));
        $this->assertNull($this->properties->parseArray('doesNotExist', 'any'));
    }

    /**
     * parseArray() with default value
     *
     * @test
     */
    public function parseArrayWithDefaultValue()
    {
        $this->assertEquals(array('This is a string'), $this->properties->parseArray('scalar', 'stringValue', array('otherValue')));
        $this->assertEquals(array('303'), $this->properties->parseArray('scalar', 'intValue1', array('otherValue')));
        $this->assertEquals(array(303), $this->properties->parseArray('scalar', 'intValue2', array('otherValue')));
        $this->assertEquals(array('3.13'), $this->properties->parseArray('scalar', 'floatValue1', array('otherValue')));
        $this->assertEquals(array(3.13), $this->properties->parseArray('scalar', 'floatValue2', array('otherValue')));
        $this->assertEquals(array('1'), $this->properties->parseArray('scalar', 'boolValue1', array('otherValue')));
        $this->assertEquals(array('1'), $this->properties->parseArray('scalar', 'boolValue2', array('otherValue')));
        $this->assertEquals(array('yes'), $this->properties->parseArray('scalar', 'boolValue3', array('otherValue')));
        $this->assertEquals(array('true'), $this->properties->parseArray('scalar', 'boolValue4', array('otherValue')));
        $this->assertEquals(array('on'), $this->properties->parseArray('scalar', 'boolValue5', array('otherValue')));
        $this->assertEquals(array(), $this->properties->parseArray('scalar', 'boolValue6', array('otherValue')));
        $this->assertEquals(array(), $this->properties->parseArray('scalar', 'boolValue7', array('otherValue')));
        $this->assertEquals(array('no'), $this->properties->parseArray('scalar', 'boolValue8', array('otherValue')));
        $this->assertEquals(array('false'), $this->properties->parseArray('scalar', 'boolValue9', array('otherValue')));
        $this->assertEquals(array('off'), $this->properties->parseArray('scalar', 'boolValue10', array('otherValue')));
        $this->assertEquals(array('other'), $this->properties->parseArray('scalar', 'boolValue11', array('otherValue')));
        $this->assertEquals(array('otherValue'), $this->properties->parseArray('scalar', 'boolValue12', array('otherValue')));

        $this->assertEquals(array('foo', 'bar', 'baz'), $this->properties->parseArray('array', 'arrayValue1', array('otherValue')));
        $this->assertEquals(array(), $this->properties->parseArray('array', 'arrayValue2', array('otherValue')));
        $this->assertEquals(array('foo:bar', 'baz'), $this->properties->parseArray('array', 'hashValue1', array('otherValue')));
        $this->assertEquals(array(), $this->properties->parseArray('array', 'hashValue2', array('otherValue')));
        $this->assertEquals(array('otherValue'), $this->properties->parseArray('array', 'hashValue3', array('otherValue')));

        $this->assertEquals(array('1..5'), $this->properties->parseArray('range', 'rangeValue1', array('otherValue')));
        $this->assertEquals(array('a..e'), $this->properties->parseArray('range', 'rangeValue2', array('otherValue')));
        $this->assertEquals(array('1..'), $this->properties->parseArray('range', 'rangeValue3', array('otherValue')));
        $this->assertEquals(array('a..'), $this->properties->parseArray('range', 'rangeValue4', array('otherValue')));
        $this->assertEquals(array('..5'), $this->properties->parseArray('range', 'rangeValue5', array('otherValue')));
        $this->assertEquals(array('..e'), $this->properties->parseArray('range', 'rangeValue6', array('otherValue')));
        $this->assertEquals(array('5..1'), $this->properties->parseArray('range', 'rangeValue7', array('otherValue')));
        $this->assertEquals(array('e..a'), $this->properties->parseArray('range', 'rangeValue8', array('otherValue')));
        $this->assertEquals(array('otherValue'), $this->properties->parseArray('range', 'rangeValue9', array('otherValue')));

        $this->assertEquals(array('otherValue'), $this->properties->parseArray('empty', 'any', array('otherValue')));
        $this->assertEquals(array('otherValue'), $this->properties->parseArray('doesNotExist', 'any', array('otherValue')));
    }

    /**
     * parseHash() without default value
     *
     * @test
     */
    public function parseHashWithoutDefaultValue()
    {
        $this->assertEquals(array('This is a string'), $this->properties->parseHash('scalar', 'stringValue'));
        $this->assertEquals(array('303'), $this->properties->parseHash('scalar', 'intValue1'));
        $this->assertEquals(array(303), $this->properties->parseHash('scalar', 'intValue2'));
        $this->assertEquals(array('3.13'), $this->properties->parseHash('scalar', 'floatValue1'));
        $this->assertEquals(array(3.13), $this->properties->parseHash('scalar', 'floatValue2'));
        $this->assertEquals(array('1'), $this->properties->parseHash('scalar', 'boolValue1'));
        $this->assertEquals(array('1'), $this->properties->parseHash('scalar', 'boolValue2'));
        $this->assertEquals(array('yes'), $this->properties->parseHash('scalar', 'boolValue3'));
        $this->assertEquals(array('true'), $this->properties->parseHash('scalar', 'boolValue4'));
        $this->assertEquals(array('on'), $this->properties->parseHash('scalar', 'boolValue5'));
        $this->assertEquals(array(), $this->properties->parseHash('scalar', 'boolValue6'));
        $this->assertEquals(array(), $this->properties->parseHash('scalar', 'boolValue7'));
        $this->assertEquals(array('no'), $this->properties->parseHash('scalar', 'boolValue8'));
        $this->assertEquals(array('false'), $this->properties->parseHash('scalar', 'boolValue9'));
        $this->assertEquals(array('off'), $this->properties->parseHash('scalar', 'boolValue10'));
        $this->assertEquals(array('other'), $this->properties->parseHash('scalar', 'boolValue11'));
        $this->assertNull($this->properties->parseHash('scalar', 'boolValue12'));

        $this->assertEquals(array('foo', 'bar', 'baz'), $this->properties->parseHash('array', 'arrayValue1'));
        $this->assertEquals(array(), $this->properties->parseHash('array', 'arrayValue2'));
        $this->assertEquals(array('foo' => 'bar', 'baz'), $this->properties->parseHash('array', 'hashValue1'));
        $this->assertEquals(array(), $this->properties->parseHash('array', 'hashValue2'));
        $this->assertNull($this->properties->parseHash('array', 'hashValue3'));

        $this->assertEquals(array('1..5'), $this->properties->parseHash('range', 'rangeValue1'));
        $this->assertEquals(array('a..e'), $this->properties->parseHash('range', 'rangeValue2'));
        $this->assertEquals(array('1..'), $this->properties->parseHash('range', 'rangeValue3'));
        $this->assertEquals(array('a..'), $this->properties->parseHash('range', 'rangeValue4'));
        $this->assertEquals(array('..5'), $this->properties->parseHash('range', 'rangeValue5'));
        $this->assertEquals(array('..e'), $this->properties->parseHash('range', 'rangeValue6'));
        $this->assertEquals(array('5..1'), $this->properties->parseHash('range', 'rangeValue7'));
        $this->assertEquals(array('e..a'), $this->properties->parseHash('range', 'rangeValue8'));
        $this->assertNull($this->properties->parseHash('range', 'rangeValue9'));

        $this->assertNull($this->properties->parseHash('empty', 'any'));
        $this->assertNull($this->properties->parseHash('doesNotExist', 'any'));
    }

    /**
     * parseHash() with default value
     *
     * @test
     */
    public function parseHashWithDefaultValue()
    {
        $this->assertEquals(array('This is a string'), $this->properties->parseHash('scalar', 'stringValue', array('other' => 'Value')));
        $this->assertEquals(array('303'), $this->properties->parseHash('scalar', 'intValue1', array('other' => 'Value')));
        $this->assertEquals(array(303), $this->properties->parseHash('scalar', 'intValue2', array('other' => 'Value')));
        $this->assertEquals(array('3.13'), $this->properties->parseHash('scalar', 'floatValue1', array('other' => 'Value')));
        $this->assertEquals(array(3.13), $this->properties->parseHash('scalar', 'floatValue2', array('other' => 'Value')));
        $this->assertEquals(array('1'), $this->properties->parseHash('scalar', 'boolValue1', array('other' => 'Value')));
        $this->assertEquals(array('1'), $this->properties->parseHash('scalar', 'boolValue2', array('other' => 'Value')));
        $this->assertEquals(array('yes'), $this->properties->parseHash('scalar', 'boolValue3', array('other' => 'Value')));
        $this->assertEquals(array('true'), $this->properties->parseHash('scalar', 'boolValue4', array('other' => 'Value')));
        $this->assertEquals(array('on'), $this->properties->parseHash('scalar', 'boolValue5', array('other' => 'Value')));
        $this->assertEquals(array(), $this->properties->parseHash('scalar', 'boolValue6', array('other' => 'Value')));
        $this->assertEquals(array(), $this->properties->parseHash('scalar', 'boolValue7', array('other' => 'Value')));
        $this->assertEquals(array('no'), $this->properties->parseHash('scalar', 'boolValue8', array('other' => 'Value')));
        $this->assertEquals(array('false'), $this->properties->parseHash('scalar', 'boolValue9', array('other' => 'Value')));
        $this->assertEquals(array('off'), $this->properties->parseHash('scalar', 'boolValue10', array('other' => 'Value')));
        $this->assertEquals(array('other'), $this->properties->parseHash('scalar', 'boolValue11', array('other' => 'Value')));
        $this->assertEquals(array('other' => 'Value'), $this->properties->parseHash('scalar', 'boolValue12', array('other' => 'Value')));

        $this->assertEquals(array('foo', 'bar', 'baz'), $this->properties->parseHash('array', 'arrayValue1', array('other' => 'Value')));
        $this->assertEquals(array(), $this->properties->parseHash('array', 'arrayValue2', array('other' => 'Value')));
        $this->assertEquals(array('foo' => 'bar', 'baz'), $this->properties->parseHash('array', 'hashValue1', array('other' => 'Value')));
        $this->assertEquals(array(), $this->properties->parseHash('array', 'hashValue2', array('other' => 'Value')));
        $this->assertEquals(array('other' => 'Value'), $this->properties->parseHash('array', 'hashValue3', array('other' => 'Value')));

        $this->assertEquals(array('1..5'), $this->properties->parseHash('range', 'rangeValue1', array('other' => 'Value')));
        $this->assertEquals(array('a..e'), $this->properties->parseHash('range', 'rangeValue2', array('other' => 'Value')));
        $this->assertEquals(array('1..'), $this->properties->parseHash('range', 'rangeValue3', array('other' => 'Value')));
        $this->assertEquals(array('a..'), $this->properties->parseHash('range', 'rangeValue4', array('other' => 'Value')));
        $this->assertEquals(array('..5'), $this->properties->parseHash('range', 'rangeValue5', array('other' => 'Value')));
        $this->assertEquals(array('..e'), $this->properties->parseHash('range', 'rangeValue6', array('other' => 'Value')));
        $this->assertEquals(array('5..1'), $this->properties->parseHash('range', 'rangeValue7', array('other' => 'Value')));
        $this->assertEquals(array('e..a'), $this->properties->parseHash('range', 'rangeValue8', array('other' => 'Value')));
        $this->assertEquals(array('other' => 'Value'), $this->properties->parseHash('range', 'rangeValue9', array('other' => 'Value')));

        $this->assertEquals(array('other' => 'Value'), $this->properties->parseHash('empty', 'any', array('other' => 'Value')));
        $this->assertEquals(array('other' => 'Value'), $this->properties->parseHash('doesNotExist', 'any', array('other' => 'Value')));
    }

    /**
     * parseRange() without default value
     *
     * @test
     */
    public function parseRangeWithoutDefaultValue()
    {
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'stringValue'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'intValue1'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'intValue2'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'floatValue1'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'floatValue2'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue1'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue2'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue3'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue4'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue5'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue6'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue7'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue8'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue9'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue10'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue11'));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue12'));

        $this->assertEquals(array(), $this->properties->parseRange('array', 'arrayValue1'));
        $this->assertEquals(array(), $this->properties->parseRange('array', 'arrayValue2'));
        $this->assertEquals(array(), $this->properties->parseRange('array', 'hashValue1'));
        $this->assertEquals(array(), $this->properties->parseRange('array', 'hashValue2'));
        $this->assertEquals(array(), $this->properties->parseRange('array', 'hashValue3'));

        $this->assertEquals(array(1, 2, 3, 4, 5), $this->properties->parseRange('range', 'rangeValue1'));
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'), $this->properties->parseRange('range', 'rangeValue2'));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue3'));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue4'));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue5'));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue6'));
        $this->assertEquals(array(5, 4, 3, 2, 1), $this->properties->parseRange('range', 'rangeValue7'));
        $this->assertEquals(array('e', 'd', 'c', 'b', 'a'), $this->properties->parseRange('range', 'rangeValue8'));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue9'));

        $this->assertEquals(array(), $this->properties->parseRange('empty', 'any'));
        $this->assertEquals(array(), $this->properties->parseRange('doesNotExist', 'any'));
    }

    /**
     * parseRange() with default value
     *
     * @test
     */
    public function parseRangeWithDefaultValue()
    {
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'stringValue', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'intValue1', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'intValue2', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'floatValue1', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'floatValue2', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue1', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue2', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue3', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue4', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue5', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue6', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue7', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue8', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue9', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue10', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('scalar', 'boolValue11', array(303, 313)));
        $this->assertEquals(array(303, 313), $this->properties->parseRange('scalar', 'boolValue12', array(303, 313)));

        $this->assertEquals(array(), $this->properties->parseRange('array', 'arrayValue1', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('array', 'arrayValue2', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('array', 'hashValue1', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('array', 'hashValue2', array(303, 313)));
        $this->assertEquals(array(303, 313), $this->properties->parseRange('array', 'hashValue3', array(303, 313)));

        $this->assertEquals(array(1, 2, 3, 4, 5), $this->properties->parseRange('range', 'rangeValue1', array(303, 313)));
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'), $this->properties->parseRange('range', 'rangeValue2', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue3', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue4', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue5', array(303, 313)));
        $this->assertEquals(array(), $this->properties->parseRange('range', 'rangeValue6', array(303, 313)));
        $this->assertEquals(array(5, 4, 3, 2, 1), $this->properties->parseRange('range', 'rangeValue7', array(303, 313)));
        $this->assertEquals(array('e', 'd', 'c', 'b', 'a'), $this->properties->parseRange('range', 'rangeValue8', array(303, 313)));
        $this->assertEquals(array(303, 313), $this->properties->parseRange('range', 'rangeValue9', array(303, 313)));

        $this->assertEquals(array(303, 313), $this->properties->parseRange('empty', 'any', array(303, 313)));
        $this->assertEquals(array(303, 313), $this->properties->parseRange('doesNotExist', 'any', array(303, 313)));
    }

    /**
     * @test
     * @group  bug249
     */
    public function iteratingOverInstanceIteratesOverSections()
    {
        foreach ($this->properties as $section => $sectionData) {
            $this->assertTrue($this->properties->hasSection($section));
            $this->assertEquals($sectionData,
                                $this->properties->getSection($section)
            );
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
            $this->assertTrue($this->properties->hasSection($section));
            $this->assertEquals($sectionData,
                                $this->properties->getSection($section)
            );
            $firstIterationEntries++;
        }

        $secondIterationEntries = 0;
        foreach ($this->properties as $section => $sectionData) {
            $this->assertTrue($this->properties->hasSection($section));
            $this->assertEquals($sectionData,
                                $this->properties->getSection($section)
            );
            $secondIterationEntries++;
        }

        $this->assertEquals($firstIterationEntries, $secondIterationEntries);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\FileNotFoundException
     */
    public function fromNonExistantFileThrowsFileNotFoundException()
    {
        readPropertyFile(__DIR__ . '/doesNotExist.ini');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IOException
     */
    public function invalidIniFileThrowsIOException()
    {
        $root = vfsStream::setup('config');
        vfsStream::newFile('invalid.ini')
                 ->at($root)
                 ->withContent("[invalid{");
        readPropertyFile(vfsStream::url('config/invalid.ini'));
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
        $properties = readPropertyFile(vfsStream::url('config/test.ini'));
        $this->assertInstanceOf('net\\stubbles\\lang\\Properties', $properties);
        $this->assertTrue($properties->hasSection('foo'));
        $this->assertEquals(array('bar' => 'baz'), $properties->getSection('foo'));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
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
        $this->assertInstanceOf('net\\stubbles\\lang\\Properties', $properties);
        $this->assertTrue($properties->hasSection('foo'));
        $this->assertEquals(array('bar' => 'baz'), $properties->getSection('foo'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function mergeMergesTwoPropertiesInstancesAndReturnsNewInstance()
    {
        $properties1 = properties(array('foo' => array('bar' => 'baz')));
        $properties2 = properties(array('bar' => array('bar' => 'baz')));
        $resultProperties = $properties1->merge($properties2);
        $this->assertNotSame($resultProperties, $properties1);
        $this->assertNotSame($resultProperties, $properties2);
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function mergeMergesProperties()
    {
        $properties1 = properties(array('foo' => array('bar' => 'baz')));
        $properties2 = properties(array('bar' => array('bar' => 'baz')));
        $resultProperties = $properties1->merge($properties2);
        $this->assertTrue($resultProperties->hasSection('foo'));
        $this->assertEquals(array('bar' => 'baz'), $resultProperties->getSection('foo'));
        $this->assertTrue($resultProperties->hasSection('bar'));
        $this->assertEquals(array('bar' => 'baz'), $resultProperties->getSection('bar'));
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function mergeOverwritesSectionsOfMergingInstanceWithThoseFromMergedInstance()
    {
        $properties1 = properties(array('foo' => array('bar' => 'baz'),
                                        'bar' => array('baz' => 'foo')
                                      )
                       );
        $properties2 = properties(array('bar' => array('bar' => 'baz')));
        $resultProperties = $properties1->merge($properties2);
        $this->assertTrue($resultProperties->hasSection('foo'));
        $this->assertEquals(array('bar' => 'baz'), $resultProperties->getSection('foo'));
        $this->assertTrue($resultProperties->hasSection('bar'));
        $this->assertEquals(array('bar' => 'baz'), $resultProperties->getSection('bar'));
    }
}
?>