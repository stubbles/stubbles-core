<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace org\stubbles\test\ioc;
/**
 * Helper class for the test.
 */
class DevelopersMultipleSetterMethodParamsWithConstant
{
    public $role;
    public $schst;

    /**
     * setter method with Named() annotation on a specific param
     *
     * @param  Employee  $schst
     * @param  string                            $role
     * @Inject
     * @Named{role}('boss')
     */
    public function setDevelopers(Employee $schst, $role)
    {
        $this->schst = $schst;
        $this->role  = $role;
    }
}
?>