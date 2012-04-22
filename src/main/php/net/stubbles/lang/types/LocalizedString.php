<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\types;
use net\stubbles\lang\BaseObject;
/**
 * Class containing a localized string.
 *
 * @api
 * @XmlTag(tagName='string')
 */
class LocalizedString extends BaseObject
{
    /**
     * locale of the message
     *
     * @type  string
     */
    private $locale;
    /**
     * content of the message
     *
     * @type  string
     */
    private $message;

    /**
     * constructor
     *
     * @param  string  $locale
     * @param  string  $message
     */
    public function __construct($locale, $message)
    {
        $this->locale  = $locale;
        $this->message = $message;
    }

    /**
     * returns the locale of the message
     *
     * @XmlAttribute(attributeName='locale')
     * @return  string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * returns the content of the message
     *
     * @XmlTag(tagName='content')
     * @return  string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * returns a string representation
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getMessage();
    }
}
?>