<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\predicate;
/**
 * Tests for stubbles\predicate\Mail.
 *
 * @group  predicate
 * @since  4.0.0
 */
class MailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  Mail
     */
    protected $mail;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mail = new Mail();
    }

    /**
     * @return  array
     */
    public function validValues()
    {
        return [['example@example.org'],
                ['example.foo.bar@example.org']
        ];
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  validValues
     */
    public function validValuesValidateToTrue($value)
    {
        $this->assertTrue($this->mail->test($value));
    }

    /**
     * @return  array
     */
    public function invalidValues()
    {
        return [['space in@mailadre.ss'],
                ['fäö@mailadre.ss'],
                ['foo@bar@mailadre.ss'],
                ['foo&/4@mailadre.ss'],
                ['foo..bar@mailadre.ss'],
                [null],
                [''],
                ['xcdsfad'],
                ['foobar@thishost.willnever.exist'],
                ['.foo.bar@example.org'],
                ['example@example.org\n'],
                ['example@exa"mple.org'],
                ['example@example.org\nBcc: example@example.com']
        ];
    }

    /**
     * @param  string  $value
     * @test
     * @dataProvider  invalidValues
     */
    public function invalidValueValidatesToFalse($value)
    {
        $this->assertFalse($this->mail->test($value));
    }

    /**
     * @test
     */
    public function validatesIndependendOfLowerOrUpperCase()
    {
        $this->assertTrue($this->mail->test('Example@example.ORG'));
        $this->assertTrue($this->mail->test('Example.Foo.Bar@EXAMPLE.org'));
    }
}
