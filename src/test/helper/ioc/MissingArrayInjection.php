<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\ioc;
/**
 * Test class for missing array injection.
 *
 * @since  2.0.0
 */
class MissingArrayInjection
{
    /**
     * constructor
     *
     * @param  array  $data
     * @Inject
     * @Named('foo')
     */
    public function setData(array $data)
    {
        // intentionally empty
    }
}
