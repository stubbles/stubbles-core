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
        assertNull(SecureString::forNull()->unveil());
    }

    /**
     * @test
     */
    public function canContainNull()
    {
        assertTrue(SecureString::forNull()->isContained());
    }

    /**
     * @test
     */
    public function forNullIdentifiesAsNull()
    {
        assertTrue(SecureString::forNull()->isNull());
    }

    /**
     * @test
     */
    public function lengthOfNullStringIsZero()
    {
        assertEquals(0, SecureString::forNull()->length());
    }

    /**
     * @test
     */
    public function substringNullStringIsNullString()
    {
        assertTrue(SecureString::forNull()->substring(2, 33)->isNull());
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
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Given string was null or empty, if you explicitly want to create a Secret with value null use Secret::forNull()
     */
    public function createWithEmptyValueThrowsIllegalArgumentException($value)
    {
        SecureString::create($value);
    }

    /**
     * @test
     * @expectedException  LogicException
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
        assertNotContains(
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

        assertNotContains(
                'payload',
                $output
        );
    }

    /**
     * @test
     * @since  4.1.2
     */
    public function varDumpNotRevealingLength()
    {
        ob_start();
        var_dump(SecureString::create('payload'));
        $output = ob_get_contents();
        ob_end_clean();

        assertNotContains(
                'length',
                $output
        );
    }

    /**
     * @test
     */
    public function stringCastNotRevealingPayload() {
        assertNotContains(
                'payload',
                (string) SecureString::create('payload')
        );
    }

    /**
     * @test
     */
    public function arrayCastNotRevealingPayload()
    {
        assertNotContains(
                'payload',
                var_export((array)SecureString::create('payload'), true)
        );
    }

    /**
     * @test
     */
    public function isContainedReturnsTrueWhenEncryptionDoesNotFail()
    {
        assertTrue(
                SecureString::create('payload')->isContained()
        );
    }

    /**
     * @test
     */
    public function unveilRevealsOriginalData()
    {
        assertEquals(
                'payload',
                SecureString::create('payload')->unveil()
        );
    }

    /**
     * @test
     */
    public function lengthReturnsStringLengthOfOriginalData()
    {
        assertEquals(
                7,
                SecureString::create('payload')->length()
        );
    }

    /**
     * @test
     */
    public function nonNullSecureStringDoesNotIdentifyAsNull()
    {
        assertFalse(SecureString::create('payload')->isNull());
    }

    /**
     * @test
     */
    public function substringWithValidStartReturnsNewInstance()
    {
        assertEquals(
                'lo',
                SecureString::create('payload')->substring(3, 2)->unveil()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function substringWithStartOutOfRangeThrowsIllegalArgumentException()
    {
        SecureString::create('payload')->substring(50);
    }

    /**
     * @test
     */
    public function bigData()
    {
        $data = str_repeat('*', 1024000);
        assertEquals(
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
        assertSame(
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

        assertFalse($secureString->isContained());
    }

    /**
     * @test
     * @expectedException  LogicException
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
     * @expectedException  InvalidArgumentException
     */
    public function switchToInvalidBackingTypeThrowsIllegalArgumentException()
    {
        SecureString::switchBacking(404);
    }

    /**
     * @test
     * @expectedException  LogicException
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
        assertTrue(SecureString::switchBacking(SecureString::BACKING_PLAINTEXT));
    }
}
