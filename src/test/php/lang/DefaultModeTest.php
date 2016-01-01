<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
/**
 * Tests for stubbles\lang\DefaultMode.
 *
 * All tests that do not require restoring the error or exception handler.
 *
 * @group  lang
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class DefaultModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function registerWithoutErrorHandlerReturnsFalse()
    {
        $mode = new DefaultMode('FOO', Mode::CACHE_ENABLED);
        assertFalse($mode->registerErrorHandler('/tmp'));
    }

    /**
     * @test
     */
    public function registerWithoutExceptionHandlerReturnsFalse()
    {
        $mode = new DefaultMode('FOO', Mode::CACHE_DISABLED);
        assertFalse($mode->registerExceptionHandler('/tmp'));
    }

    /**
     * @test
     */
    public function cacheIsEnabledInProdMode()
    {
        assertTrue(DefaultMode::prod()->isCacheEnabled());
    }

    /**
     * @test
     */
    public function cacheIsEnabledInTestMode()
    {
        assertTrue(DefaultMode::test()->isCacheEnabled());
    }

    /**
     * @test
     */
    public function cacheIsDisabledInStageMode()
    {
        assertFalse(DefaultMode::stage()->isCacheEnabled());
    }

    /**
     * @test
     */
    public function cacheIsDisabledInDevMode()
    {
        assertFalse(DefaultMode::dev()->isCacheEnabled());
    }

    /**
     * @test
     */
    public function stageModeHasNoErrorHandlerByDefault()
    {
        assertFalse(DefaultMode::stage()->registerErrorHandler('/tmp'));
    }

    /**
     * @test
     */
    public function devModeHasNoErrorHandlerByDefault()
    {
        assertFalse(DefaultMode::dev()->registerErrorHandler('/tmp'));
    }
}
