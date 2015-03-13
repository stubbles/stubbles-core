<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams;
/**
 * Output stream for writing to php://output.
 *
 * @since  5.4.0
 */
class StandardOutputStream extends ResourceOutputStream
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->setHandle(fopen('php://output', 'w'));
    }

    /**
     * closes the stream
     */
    public function close()
    {
        // intentionally empty
    }
}
