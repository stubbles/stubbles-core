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
/**
 * Tests for stubbles\lang\DefaultMode.
 *
 * All tests that do not require restoring the error or exception handler.
 *
 * @group  lang
 */
class DefaultModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function registerWithoutErrorHandlerReturnsFalse()
    {
        $mode = new DefaultMode('FOO', Mode::CACHE_ENABLED);
        $this->assertFalse($mode->registerErrorHandler('/tmp'));
    }

    /**
     * @test
     */
    public function registerWithoutExceptionHandlerReturnsFalse()
    {
        $mode = new DefaultMode('FOO', Mode::CACHE_DISABLED);
        $this->assertFalse($mode->registerExceptionHandler('/tmp'));
    }

    /**
     * @test
     */
    public function cacheIsEnabledInProdMode()
    {
        $this->assertTrue(DefaultMode::prod()->isCacheEnabled());
    }

    /**
     * @test
     */
    public function cacheIsEnabledInTestMode()
    {
        $this->assertTrue(DefaultMode::test()->isCacheEnabled());
    }

    /**
     * @test
     */
    public function cacheIsDisabledInStageMode()
    {
        $this->assertFalse(DefaultMode::stage()->isCacheEnabled());
    }

    /**
     * @test
     */
    public function cacheIsDisabledInDevMode()
    {
        $this->assertFalse(DefaultMode::dev()->isCacheEnabled());
    }

    /**
     * @test
     */
    public function stageModeHasNoErrorHandlerByDefault()
    {
        $this->assertFalse(DefaultMode::stage()->registerErrorHandler('/tmp'));
    }

    /**
     * @test
     */
    public function devModeHasNoErrorHandlerByDefault()
    {
        $this->assertFalse(DefaultMode::dev()->registerErrorHandler('/tmp'));
    }
}
