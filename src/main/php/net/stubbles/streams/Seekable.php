<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams;
use net\stubbles\lang\Object;
/**
 * A seekable stream may be altered in its position to read data.
 */
interface Seekable extends Object
{
    /**
     * set position equal to offset  bytes
     */
    const SET     = SEEK_SET;
    /**
     * set position to current location plus offset
     */
    const CURRENT = SEEK_CUR;
    /**
     * set position to end-of-file plus offset
     */
    const END     = SEEK_END;

    /**
     * seek to given offset
     *
     * @param  int  $offset
     * @param  int  $whence  one of Seekable::SET, Seekable::CURRENT or Seekable::END
     */
    public function seek($offset, $whence = Seekable::SET);

    /**
     * return current position
     *
     * @return  int
     */
    public function tell();
}
?>