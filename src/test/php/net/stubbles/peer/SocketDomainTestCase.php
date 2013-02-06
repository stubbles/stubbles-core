<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer;
/**
 * Test for net\stubbles\peer\SocketDomain.
 *
 * @since  2.0.0
 * @group  peer
 */
class SocketDomainTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function AF_INETrequiresPort()
    {
        $this->assertTrue(SocketDomain::$AF_INET->requiresPort());
    }

    /**
     * @test
     */
    public function AF_INET6requiresPort()
    {
        $this->assertTrue(SocketDomain::$AF_INET6->requiresPort());
    }

    /**
     * @test
     */
    public function AF_UNIXdoesNotRequirePort()
    {
        $this->assertFalse(SocketDomain::$AF_UNIX->requiresPort());
    }

    /**
     * returns list of names
     *
     * @return  array
     */
    public function getNames()
    {
        return array(array('AF_INET'),
                     array('AF_INET6'),
                     array('AF_UNIX')
        );
    }

    /**
     * @param  string        $expectedName
     * @test
     * @dataProvider  getNames
     */
    public function namesAreCorrect($expectedName)
    {
        $this->assertEquals($expectedName,
                            SocketDomain::forName($expectedName)->name()
        );
    }

    /**
     * returns list of names
     *
     * @return  array
     */
    public function getValues()
    {
        return array(array(AF_INET),
                     array(AF_INET6),
                     array(AF_UNIX)
        );
    }

    /**
     * @param  int  $expectedValue
     * @test
     * @dataProvider  getValues
     */
    public function valuesAreCorrect($expectedValue)
    {
        $this->assertEquals($expectedValue,
                            SocketDomain::forValue($expectedValue)->value()
        );
    }
}
?>