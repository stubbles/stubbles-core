<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\exception;
/**
 * Tests for stubbles\lang\exception\ConfigurationException.
 *
 * @group  lang
 * @group  lang_exception
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class ConfigurationExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException  stubbles\lang\exception\ConfigurationException
     */
    public function instanceCanBeThrown()
    {
        throw new ConfigurationException('error');
    }
}
