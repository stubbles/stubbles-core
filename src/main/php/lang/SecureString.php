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
 * SecureString provides a reasonable secure storage for security-sensitive
 * lists of characters, such as passwords.
 *
 * It prevents accidentially revealing them in output, by var_dump()ing,
 * echo()ing, or casting the object to array. All these cases will not
 * show the password, nor the crypt of it.
 *
 * However, it is not safe to consider this implementation secure in a crypto-
 * graphically sense, because it does not care for a very strong encryption,
 * and it does share the encryption key with all instances of it in a single
 * PHP instance.
 *
 * When using this class, you must make sure not to extract the secured string
 * and pass it to a place where an exception might occur, as it might be exposed
 * as method argument.
 *
 * @since  4.0.0
 * @deprecated  since 7.0.0, use stubbles\lang\Secret instead, will be removed with 8.0.0
 */
final class SecureString extends Secret
{
    /**
     * override regular __toString() output
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this) . " {\n}\n";
    }
}
