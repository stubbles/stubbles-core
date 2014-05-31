<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer;
/**
 * Test for stubbles\peer\SocketOptions.
 *
 * @since  2.0.0
 * @group  peer
 */
class SocketOptionsTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  SocketOptions
     */
    protected $socketOptions;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->socketOptions = new SocketOptions();
    }

    /**
     * @test
     */
    public function canBindToConnection()
    {
        $this->assertSame($this->socketOptions,
                          $this->socketOptions->bindToConnection('mockfp')
        );
    }

    /**
     * @test
     */
    public function getUnsetOptionWithoutDefaultValueReturnsNull()
    {
        $this->assertNull($this->socketOptions->get(SOL_TCP, SO_RCVTIMEO));
    }

    /**
     * @test
     */
    public function getUnsetOptionWithDefaultValueReturnsDefaultValue()
    {
        $this->assertEquals(array('sec' => 2, 'usec' => 0),
                            $this->socketOptions->get(SOL_TCP,
                                                      SO_RCVTIMEO,
                                                      array('sec' => 2, 'usec' => 0)
                            )
        );
    }

    /**
     * @test
     */
    public function getSetOptionReturnsValue()
    {
        $this->assertEquals(array('sec' => 5, 'usec' => 2),
                            $this->socketOptions->set(SOL_TCP,
                                                      SO_RCVTIMEO,
                                                      array('sec' => 5, 'usec' => 2)
                                                  )
                                                ->get(SOL_TCP,
                                                      SO_RCVTIMEO,
                                                      array('sec' => 2, 'usec' => 0)
                            )
        );
    }
}
