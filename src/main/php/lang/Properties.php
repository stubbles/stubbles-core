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
use stubbles\Properties as NewProperties;
/**
 * Class to read and parse properties.
 *
 * Properties are iterable using foreach:
 * <code>
 * foreach (Properties::fromFile($propertyFile) as $sectionName => $section) {
 *     // $section is an array containing all section values as key-value pairs
 * }
 * </code>
 *
 * @deprecated since 7.0.0, use stubbles\Properties instead, will be removed with 8.0.0
 */
class Properties extends NewProperties
{
    // intentionally empty
}
