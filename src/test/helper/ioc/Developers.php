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
     * @param  Employee  $mikey
     * @Named{schst}('schst')
     */
    public function __construct(Employee $schst, Employee $mikey)
    {
        $this->schst = $schst;
        $this->mikey = $mikey;
    }
}
