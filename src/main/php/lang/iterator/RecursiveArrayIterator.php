<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\iterator;
/**
 * Recursive iterator for arrays which really allows to recurse into children.
 *
 * The default SPL implementation \RecursiveArrayIterator is not able to return
 * the leaves only. This implementation changes the behaviour so only leaves
 * will be in the final result.
 *
 * @since  5.0.0
 */
class RecursiveArrayIterator extends \RecursiveArrayIterator
{
    /**
     * checks whether the current index has any children
     *
     * @return  bool
     */
    public function hasChildren()
    {
        return is_array($this->current());
    }
}
