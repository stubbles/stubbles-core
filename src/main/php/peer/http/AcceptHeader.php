<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer\http;
use stubbles\lang;
/**
 * Class to work with all kinds of Accept* headers.
 *
 * @api
 */
class AcceptHeader implements \Countable
{
    /**
     * list of acceptables
     *
     * @type  array
     */
    protected $acceptables = [];

    /**
     * method to create an instance from a string header value
     *
     * @param   string  $headerValue
     * @return  \stubbles\peer\http\AcceptHeader
     */
    public static function parse($headerValue)
    {
        $self = new self();
        foreach (explode(',', $headerValue) as $acceptable) {
            // seems to be impossible to parse acceptables with regular
            // expressions or even scanf(), so we do some string crunching here
            if (strstr($acceptable, 'q=') !== false) {
                list($acceptable, $priority) = explode('q=', trim($acceptable));
            } else {
                $priority = 1;
            }

            settype($priority, 'float');
            $acceptable = trim($acceptable);
            if (substr($acceptable, -1) === ';') {
                $acceptable = substr($acceptable, 0, -1);
            }

            $self->addAcceptable($acceptable, $priority);
        }

        return $self;
    }

    /**
     * amount of acceptables
     *
     * @return  int
     */
    public function count()
    {
        return count($this->acceptables);
    }

    /**
     * add an acceptable to the list
     *
     * @param   string  $acceptable
     * @param   float   $priority    defaults to 1.0
     * @return  \stubbles\peer\http\AcceptHeader
     * @throws  \InvalidArgumentException
     */
    public function addAcceptable($acceptable, $priority = 1.0)
    {
        if (0 > $priority || 1.0 < $priority) {
            throw new \InvalidArgumentException(
                    'Invalid priority, must be between 0 and 1.0'
            );
        }

        $this->acceptables[$acceptable] = $priority;
        return $this;
    }

    /**
     * returns priority for given acceptable
     *
     * If returned priority is 0 the requested acceptable is not in the list. In
     * case no acceptables were added before every requested acceptable has a
     * priority of 1.0.
     *
     * @param   string  $mimeType
     * @return  float
     */
    public function priorityFor($mimeType)
    {
        if (!isset($this->acceptables[$mimeType])) {
            if ($this->count() === 0) {
                return 1.0;
            } elseif (isset($this->acceptables['*/*'])) {
                return $this->acceptables['*/*'];
            }

            list($maintype, $subtype) = explode('/', $mimeType);
            if (isset($this->acceptables[$maintype . '/*'])) {
                return $this->acceptables[$maintype . '/*'];
            }

            return 0;
        }

        return $this->acceptables[$mimeType];
    }

    /**
     * find match with highest priority
     *
     * Checks given list of mime types if they are in the list, and returns the
     * one with the greatest priority. If return value is null none of the given
     * mime types matches any in the list.
     *
     * @param   string[]  $mimeTypes
     * @return  string
     */
    public function findMatchWithGreatestPriority(array $mimeTypes)
    {
        $sharedAcceptables = array_intersect_key($this->acceptables,
                                                 array_flip($this->getSharedAcceptables($mimeTypes))
        );
        if (count($sharedAcceptables) > 0) {
            return $this->findAcceptableWithGreatestPriorityFromList($sharedAcceptables);
        }

        foreach ($mimeTypes as $acceptable) {
            list($maintype, $subtype) = explode('/', $acceptable);
            if (isset($this->acceptables[$maintype . '/*'])) {
                return $acceptable;
            }
        }

        if (isset($this->acceptables['*/*'])) {
            return array_shift($mimeTypes);
        }

        return null;
    }

    /**
     * helper method to find the acceptable with the greatest priority from a given list of acceptables
     *
     * @param   array  $acceptables
     * @return  string
     */
    protected function findAcceptableWithGreatestPriorityFromList(array $acceptables)
    {
        if (count($acceptables) === 0) {
            return null;
        }

        arsort($acceptables);
        // use temp var to prevent E_STRICT Only variables should be passed by reference
        $acceptableKeys = array_keys($acceptables);
        return array_shift($acceptableKeys);
    }

    /**
     * returns the acceptable with the greatest priority
     *
     * If two acceptables have the same priority the last one added wins.
     *
     * @return  string
     */
    public function findAcceptableWithGreatestPriority()
    {
        return $this->findAcceptableWithGreatestPriorityFromList($this->acceptables);
    }

    /**
     * checks whether there are shares acceptables in header and given list
     *
     * @param   string[]  $acceptables
     * @return  bool
     */
    public function hasSharedAcceptables(array $acceptables)
    {
        return (count($this->getSharedAcceptables($acceptables)) > 0);
    }

    /**
     * returns a list of acceptables that are both in header and given list
     *
     * @param   string[]  $acceptables
     * @return  string[]
     */
    public function getSharedAcceptables(array $acceptables)
    {
        return array_intersect(array_keys($this->acceptables), $acceptables);
    }

    /**
     * returns current list as string
     *
     * @return  string
     */
    public function asString()
    {
        $parts = [];
        foreach ($this->acceptables as $acceptable => $priority) {
            if (1.0 === $priority) {
                $parts[] = $acceptable;
            } else {
                $parts[] = $acceptable . ';q=' . $priority;
            }
        }

        return join(',', $parts);
    }

    /**
     * returns a string representation of the class
     *
     * @XmlIgnore
     * @return  string
     */
    public function __toString()
    {
        return lang\__toString($this);
    }
}
