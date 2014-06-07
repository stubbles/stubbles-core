<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * The contents of this file draw heavily from XP Framework
 * https://github.com/xp-framework/xp-framework
 *
 * Copyright (c) 2001-2014, XP-Framework Team
 * All rights reserved.
 * https://github.com/xp-framework/xp-framework/blob/master/core/src/main/php/LICENCE
 *
 * @package  stubbles
 */
namespace stubbles\lang;
/**
 * Base class tests for stubbles\lang\SecureString.
 *
 * @since  4.0.0
 */
abstract class SecureStringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function forNullReturnsNullOnUnveil()
    {
        $this->assertNull(SecureString::forNull()->unveil());
    }

    /**
     * @test
     */
    public function canContainNull()
    {
        $this->assertTrue(SecureString::forNull()->isContained());
    }

    /**
     * @test
     */
    public function lengthOfNullStringIsZero()
    {
        $this->assertEquals(0, SecureString::forNull()->length());
    }

    /**
     * @return  array
     */
    public function emptyValues()
    {
        return [[null], ['']];
    }

    /**
     * @test
     * @dataProvider  emptyValues
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     * @expectedExceptionMessage  Given string was null or empty, if you explicitly want to create a SecureString with value null use SecureString::forNull()
     */
    public function createWithEmptyValueThrowsIllegalArgumentException($value)
    {
        SecureString::create($value);
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalAccessException
     */
    public function notSerializable()
    {
        serialize(SecureString::create('payload'));
    }

    /**
     * @test
     */
    public function varExportNotRevealingPayload()
    {
        $this->assertNotContains(
                'payload',
                var_export(SecureString::create('payload'), true)
        );
    }

    /**
     * @test
     */
    public function varDumpNotRevealingPayload()
    {
        ob_start();
        var_dump(SecureString::create('payload'));
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertNotContains(
                'payload',
                $output
        );
    }

    /**
     * @test
     */
    public function stringCastNotRevealingPayload() {
        $this->assertNotContains(
                'payload',
                (string) SecureString::create('payload')
        );
    }

    /**
     * @test
     */
    public function arrayCastNotRevealingPayload()
    {
        $this->assertNotContains(
                'payload',
                var_export((array)SecureString::create('payload'), true)
        );
    }

    /**
     * @test
     */
    public function isContainedReturnsTrueWhenEncryptionDoesNotFail()
    {
        $this->assertTrue(
                SecureString::create('payload')->isContained()
        );
    }

    /**
     * @test
     */
    public function unveilRevealsOriginalData()
    {
        $this->assertEquals(
                'payload',
                SecureString::create('payload')->unveil()
        );
    }

    /**
     * @test
     */
    public function lengthReturnsStringLengthOfOriginalData()
    {
        $this->assertEquals(
                7,
                SecureString::create('payload')->length()
        );
    }

    /**
     * @test
     */
    public function bigData()
    {
        $data = str_repeat('*', 1024000);
        $this->assertEquals(
                $data,
                SecureString::create($data)->unveil()
        );
    }

    /**
     * @test
     */
    public function createFromSecureStringReturnsInstance()
    {
        $secureString = SecureString::create('payload');
        $this->assertSame(
                $secureString,
                SecureString::create($secureString)
        );
    }

    /**
     * @test
     */
    public function creationNeverThrowsException()
    {
        SecureString::switchBacking('__none');
        try {
            $secureString = SecureString::create('payload');
        } catch (\Exception $e) {
            $this->fail('Exception thrown where no exception may be thrown');
        }

        $this->assertFalse($secureString->isContained());
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function unveilThrowsIllegalStateExceptionWhenCreationHasFailed()
    {
        SecureString::switchBacking('__none');
        try {
            $secureString = SecureString::create('payload');
        } catch (\Exception $e) {
            $this->fail('Exception thrown where no exception may be thrown');
        }

        $secureString->unveil();
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function switchToInvalidBackingTypeThrowsIllegalArgumentException()
    {
        SecureString::switchBacking(404);
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalStateException
     */
    public function switchBackingWhenSecureStringInstancesExistThrowsIllegalStateException()
    {
        $secureString = SecureString::create('payload');
        SecureString::switchBacking(SecureString::BACKING_PLAINTEXT);
    }

    /**
     * @test
     */
    public function canSwitchBackingWhenAllSecureStringInstancesDestroyed()
    {
        $secureString = SecureString::create('payload');
        $secureString = null;
        $this->assertTrue(SecureString::switchBacking(SecureString::BACKING_PLAINTEXT));
    }
}
