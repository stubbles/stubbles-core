<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\types;
/**
 * Tests for net\stubbles\lang\types\LocalizedString.
 *
 * @group  lang
 * @group  lang_types
 */
class LocalizedStringTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LocalizedString
     */
    protected $localizedString;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->localizedString = new LocalizedString('en_EN', 'This is a localized string.');
    }

    /**
     * creates reflection instance for class under test
     *
     * @return  \net\stubbles\lang\reflect\ReflectionClass
     */
    private function getReflectionClass()
    {
        return new \net\stubbles\lang\reflect\ReflectionObject($this->localizedString);
    }

    /**
     * @test
     */
    public function annotationPresentOnClass()
    {
        $this->assertTrue($this->getReflectionClass()->hasAnnotation('XmlTag'));
    }

    /**
     * @test
     */
    public function annotationPresentOnGetLocaleMethod()
    {
        $this->assertTrue($this->getReflectionClass()
                               ->getMethod('getLocale')
                               ->hasAnnotation('XmlAttribute')
        );
    }

    /**
     * @test
     */
    public function annotationPresentOngetMessageMethod()
    {
        $this->assertTrue($this->getReflectionClass()
                               ->getMethod('getMessage')
                               ->hasAnnotation('XmlTag')
        );
    }

    /**
     * @test
     */
    public function localeAttributeEqualsGivenLocale()
    {
        $this->assertEquals('en_EN', $this->localizedString->getLocale());
    }

    /**
     * @test
     */
    public function contentOfStringEqualsGivenString()
    {
        $this->assertEquals('This is a localized string.',
                            $this->localizedString->getMessage()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function conversionToStringYieldsMessage()
    {
        $this->assertEquals('This is a localized string.',
                            (string) $this->localizedString
        );
    }
}
?>