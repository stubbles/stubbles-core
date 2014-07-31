<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect;
/**
 * Extended Reflection class for extensions.
 *
 * @api
 */
class ReflectionExtension extends \ReflectionExtension
{
    /**
     * name of reflected extension
     *
     * @type  string
     */
    protected $extensionName;

    /**
     * constructor
     *
     * @param  string  $extensionName  name of extension to reflect
     */
    public function __construct($extensionName)
    {
        parent::__construct($extensionName);
        $this->extensionName = $extensionName;
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($compare->extensionName == $this->extensionName);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'stubbles\lang\reflect\ReflectionExtension['[name-of-reflected-extension]']  {}'
     * <code>
     * stubbles\lang\reflect\ReflectionExtension[spl] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return __CLASS__ . '[' . $this->extensionName . "] {\n}\n";
    }

    /**
     * returns a list of all functions
     *
     * @return  \stubbles\lang\reflect\ReflectionFunction[]
     */
    public function getFunctions()
    {
        $functions        = parent::getFunctions();
        $stubRefFunctions = [];
        foreach ($functions as $function) {
            $stubRefFunctions[] = new ReflectionFunction($function->getName());
        }

        return $stubRefFunctions;
    }

    /**
     * returns a list of all classes
     *
     * @return  \stubbles\lang\reflect\ReflectionClass[]
     */
    public function getClasses()
    {
        $classes        = parent::getClasses();
        $stubRefClasses = [];
        foreach ($classes as $class) {
            $stubRefClasses[] = new ReflectionClass($class->getName());
        }

        return $stubRefClasses;
    }
}
