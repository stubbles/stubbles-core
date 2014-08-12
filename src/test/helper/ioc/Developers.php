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
 * Helper class for the test.
 */
class Developers
{
    public $mikey;
    public $schst;

    /**
     * Setter method with Named() annotation
     *
     * @param  Employee  $schst
     * @Inject
     * @Named('schst')
     */
    public function setSchst(Employee $schst)
    {
        $this->schst = $schst;
    }

    /**
     * Setter method without Named() annotation
     *
     * @param  Employee  $schst
     * @Inject
     */
    public function setMikey(Employee $mikey)
    {
        $this->mikey = $mikey;
    }
}
