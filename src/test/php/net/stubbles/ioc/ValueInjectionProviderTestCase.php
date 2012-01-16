<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc;
/**
 * Test for net\stubbles\ioc\ValueInjectionProvider.
 *
 * @group  ioc
 */
class ValueInjectionProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldProvideGivenValue()
    {
        $valueInjectorProvider = new ValueInjectionProvider('value');
        $this->assertEquals('value', $valueInjectorProvider->get());
    }
}
?>