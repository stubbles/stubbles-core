<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\iterator;
use stubbles\lang\Properties;
/**
 * Helper class for the test.
 */
class PropertyIterator implements \Iterator
{
    use PropertyBasedIterator;

    private $properties;

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    protected function properties()
    {
        return $this->properties;
    }
}
/**
 * Tests for stubbles\lang\iterator\PropertyBasedIterator
 *
 * @since  5.0.0
 * @group  lang
 * @group  lang_iterator
 */
class PropertyBasedIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @type  \stubbles\lang\iterator\PropertyIterator
     */
    private $propertyIterator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->propertyIterator = new PropertyIterator(
                new Properties(['foo' => ['bar' => 'baz'], 'other' => [303 => 909]])
        );
    }

    /**
     * @test
     */
    public function iterationProvidesSectionKeys()
    {
        $keys = [];
        foreach ($this->propertyIterator as $sectionKey => $section) {
            $keys[] = $sectionKey;
        }

        $this->assertEquals(['foo', 'other'], $keys);
    }

    /**
     * @test
     */
    public function iterationProvidesSections()
    {
        $sections = [];
        foreach ($this->propertyIterator as $section) {
            $sections[] = $section;
        }

        $this->assertEquals([['bar' => 'baz'], [303 => 909]], $sections);
    }
}
