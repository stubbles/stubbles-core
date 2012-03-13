<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang;
/**
 * Base interface for all stubbles classes except static ones and classes
 * extending php built-in classes.
 */
interface Object
{
    /**
     * returns class informations
     *
     * @return  net\stubbles\lang\reflect\ReflectionObject
     */
    public function getClass();

    /**
     * returns the full qualified class name
     *
     * @return  string
     */
    public function getClassName();

    /**
     * returns a unique hash code for the class
     *
     * @return  string
     */
    public function hashCode();

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare);

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * example\MyClass {
     *     foo(string): hello
     *     bar(example\AnotherClass): example\AnotherClass {
     *         baz(int): 5
     *     }
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString();
}
/**
* set internal, input and output encoding
*/
call_user_func(function() {
    iconv_set_encoding('internal_encoding', 'UTF-8');
    if (($ctype = getenv('LC_CTYPE')) || ($ctype = setlocale(LC_CTYPE, 0))) {
        sscanf($ctype, '%[^.].%s', $language, $charset);
        if (is_numeric($charset) === true) {
            $charset = 'CP' . $charset;
        } elseif (null == $charset) {
            $charset = 'iso-8859-1';
        }

        iconv_set_encoding('output_encoding', $charset);
        iconv_set_encoding('input_encoding', $charset);
    }
});
?>