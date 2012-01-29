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
use net\stubbles\lang\BaseObject;
/**
 * Helper class for ioc tests.
 */
class AppClassWithArgument extends BaseObject
{
    /**
     * given project path
     *
     * @type  string
     */
    protected $arg;

    /**
     * returns set project path
     *
     * @return  string
     * @Inject
     * @Named('argv.0')
     */
    public function setArgument($arg)
    {
        $this->arg = $arg;
    }

    /**
     * returns the argument
     *
     * @return  string
     */
    public function getArgument()
    {
        return $this->arg;
    }
}
?>