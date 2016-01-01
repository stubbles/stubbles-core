<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\environments;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
/**
 * Tests for stubbles\environments.
 *
 * All tests that do not require restoring the error or exception handler.
 *
 * @group  environments
 */
class EnvironmentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function cacheIsEnabledInProduction()
    {
        assertTrue((new Production())->isCacheEnabled());
    }

    /**
     * @test
     */
    public function cacheIsDisabledInDevelopment()
    {
        assertFalse((new Development())->isCacheEnabled());
    }

    /**
     * @test
     */
    public function developmentHasNoErrorHandlerByDefault()
    {
        assertFalse((new Development())->registerErrorHandler('/tmp'));
    }
}
