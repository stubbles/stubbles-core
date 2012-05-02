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
 * Helper class for the test. Mocks Composer\Script\Event.
 */
class Event
{
    /**
     * interface to output
     *
     * @type  IOInterface
     */
    private $io;

    /**
     * constructor
     *
     * @param  IOInterface  $io
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * returns interface to output
     *
     * @return  IOInterface
     */
    public function getIO()
    {
        return $this->io;
    }
}
?>