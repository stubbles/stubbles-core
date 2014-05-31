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
 * Tests for stubbles\lang\exception\MethodInvocationException.
 *
 * @group  lang
 * @group  lang_exception
 */
class MethodInvocationExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException  stubbles\lang\exception\MethodInvocationException
     */
    public function instanceCanBeThrown()
    {
        throw new MethodInvocationException('error');
    }
}
