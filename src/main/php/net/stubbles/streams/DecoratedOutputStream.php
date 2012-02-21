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
/**
 * Interface for decorated output streams.
 */
interface DecoratedOutputStream extends OutputStream
{
    /**
     * returns enclosed output stream
     *
     * @return  OutputStream
     */
    public function getEnclosedOutputStream();
}
?>