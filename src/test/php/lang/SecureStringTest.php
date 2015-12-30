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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\doesNotContain;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isSameAs;
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
        assert(SecureString::forNull()->length(), equals(0));
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
        assert(
                var_export(SecureString::create('payload'), true),
                doesNotContain('payload')
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

        assert($output, doesNotContain('payload'));
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

        assert($output, doesNotContain('length'));
    }

    /**
     * @test
     */
    public function stringCastNotRevealingPayload() {
        assert(
                (string) SecureString::create('payload'),
                doesNotContain('payload')
        );
    }

    /**
     * @test
     */
    public function arrayCastNotRevealingPayload()
    {
        assert(
                var_export((array)SecureString::create('payload'), true),
                doesNotContain('payload')
        );
    }

    /**
     * @test
     */
    public function isContainedReturnsTrueWhenEncryptionDoesNotFail()
    {
        assertTrue(SecureString::create('payload')->isContained());
    }

    /**
     * @test
     */
    public function unveilRevealsOriginalData()
    {
        assert(SecureString::create('payload')->unveil(), equals('payload'));
    }

    /**
     * @test
     */
    public function lengthReturnsStringLengthOfOriginalData()
    {
        assert(SecureString::create('payload')->length(), equals(7));
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
        assert(
                SecureString::create('payload')->substring(3, 2)->unveil(),
                equals('lo')
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
        assert(SecureString::create($data)->unveil(), equals($data));
    }

    /**
     * @test
     */
    public function createFromSecureStringReturnsInstance()
    {
        $secureString = SecureString::create('payload');
        assert(SecureString::create($secureString), isSameAs($secureString));
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
            fail('Exception thrown where no exception may be thrown');
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
            fail('Exception thrown where no exception may be thrown');
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
