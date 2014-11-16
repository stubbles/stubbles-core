<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams {
    use stubbles\lang\Sequence;
    use stubbles\streams\file\FileInputStream;

    /**
     * returns a sequence of lines from given input source
     *
     * @api
     * @param   \stubbles\streams\InputStream|string  $input
     * @return  \stubbles\lang\Sequence
     * @since   5.2.0
     */
    function linesOf($input)
    {
        return Sequence::of(new InputStreamIterator(FileInputStream::castFrom($input)));
    }
}
