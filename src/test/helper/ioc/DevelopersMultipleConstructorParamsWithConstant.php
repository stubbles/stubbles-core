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
class DevelopersMultipleConstructorParamsWithConstant
{
    public $role;
    public $schst;

    /**
     * constructor method with Named() annotation on a specific param
     *
     * @param  Employee  $schst
     * @param  string                            $role
     * @Inject
     * @Named{role}('boss')
     */
    public function __construct(Employee $schst, $role)
    {
        $this->schst = $schst;
        $this->role  = $role;
    }
}
