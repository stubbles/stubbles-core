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
require_once __DIR__ . '/SecureStringTest.php';
/**
 * Plain text backed tests for stubbles\lang\SecureString.
 *
 * @since  4.0.0
 * @group  lang
 * @group  lang_core
 */
class PlaintextSecureStringTest extends SecureStringTest
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        SecureString::switchBacking(SecureString::BACKING_PLAINTEXT);
    }
}
