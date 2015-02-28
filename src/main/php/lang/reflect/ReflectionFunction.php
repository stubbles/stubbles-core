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
use stubbles\lang\reflect\annotation\Annotated;
/**
 * Extended Reflection class for functions that allows usage of annotations.
 *
 * @api
 * @deprecated  use PHP's native reflection classes, will be removed with 6.0.0
 */
class ReflectionFunction extends \ReflectionFunction implements ReflectionRoutine
{
    use Annotated;

    /**
     * name of the reflected function
     *
     * @type  string
     */
    protected $functionName;
    /**
     * docblock comment for this class
     *
     * @type  string
     */
    protected $docComment;

    /**
     * constructor
     *
     * @param  string  $functionName  name of function to reflect
     */
    public function __construct($functionName)
    {
        parent::__construct($functionName);
        $this->functionName = $functionName;
        $this->docComment   = $this->getDocComment();
    }

    /**
     * target name of property annotations
     *
     * @return  string
     * @see     \stubbles\lang\reflect\annotation\Annotated
     */
    protected function annotationTarget()
    {
        return $this->functionName . '()';
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
            return ($compare->functionName === $this->functionName);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'stubbles\lang\reflect\ReflectionFunction['[name-of-reflected-function]'()]  {}'
     * <code>
     * stubbles\lang\reflect\ReflectionFunction[fopen()] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return __CLASS__ . '[' . $this->functionName . "()] {\n}\n";
    }

    /**
     * returns a list of all parameters
     *
     * @return  \stubbles\lang\reflect\ReflectionParameter[]
     */
    public function getParameters()
    {
        $parameters     = parent::getParameters();
        $stubParameters = [];
        foreach ($parameters as $parameter) {
            $stubParameters[] = new ReflectionParameter($this, $parameter->getName());
        }

        return $stubParameters;
    }

    /**
     * returns information about the return type of a function
     *
     * If the return type is a class the return value is an instance of
     * stubbles\lang\reflect\ReflectionClass (if the class is unknown a
     * stubbles\ClassNotFoundException will be thrown), if it is a scalar
     * type the return value is an instance of ReflectionPrimitive, for mixed
     * and object the return value is an instance of MixedType, and if the
     * method does not have a return value this method returns null.
     * Please be aware that this is guessing from the doc block with which the
     * function is documented. If the doc block is missing or incorrect the
     * return value of this method may be wrong. This is due to missing type
     * hints for return values in PHP itself.
     *
     * @return  \stubbles\lang\reflect\ReflectionType
     */
    public function getReturnType()
    {
        $returnPart = strstr($this->docComment, '@return');
        if (false === $returnPart) {
            return null;
        }

        $returnParts = explode(' ', trim(str_replace('@return', '', $returnPart)));
        $returnType  = trim($returnParts[0]);
        if ('void' === strtolower($returnType)) {
            return null;
        }

        return \stubbles\lang\typeFor($returnType);
    }

    /**
     * returns the extension to where this class belongs too
     *
     * @return  \stubbles\lang\reflect\ReflectionExtension
     * @since   2.0.0
     */
    public function getExtension()
    {
        $extensionName  = $this->getExtensionName();
        if (null === $extensionName || false === $extensionName) {
            return null;
        }

        return new ReflectionExtension($extensionName);
    }

    /**
     * Wrap functions in a dynamic wrapper instead of invoking through the
     * closure returned by ReflectionFunction::getClosure().
     *
     * The latter will pass through excess arguments, which is a problem
     * for functions such as `strlen()` which expect an *exact* amount of
     * arguments.
     *
     * @return  \Closure
     * @since   4.0.0
     */
    public function wrapInClosure()
    {
        static $wrappedFunctions = [];

        $signature = '';
        $arguments = '';
        foreach (parent::getParameters() as $position => $param) {
            $signature .= ', ' . ($param->isPassedByReference() ? '&' : '') . '$_' . $position . ($param->isOptional() ? '= null' : '');
            $arguments .= ', $_' . $position;
        }

        if (!isset($wrappedFunctions[$this->functionName])) {
            $wrappedFunctions[$this->functionName] = eval('return function(' . substr($signature, 2) . ') { return ' . $this->functionName . '(' . substr($arguments, 2) . '); };');
        }

        return $wrappedFunctions[$this->functionName];
    }
}
