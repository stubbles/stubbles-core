<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\stubbles\scripts
 */
namespace org\stubbles\test\scripts;
/**
 * Helper class for the test. Mocks Composer\IO\IOInterface.
 */
class IOInterface
{
    /**
     * list of output messages
     *
     * @type  string[]
     */
    private $output = array();

    /**
     * writes a line to output
     *
     * @param  string  $text
     */
    public function write($text)
    {
        $this->output[] = $text;
    }

    /**
     * returns collected output
     *
     * @return  string[]
     */
    public function getOutput()
    {
        return $this->output;
    }
}
?>