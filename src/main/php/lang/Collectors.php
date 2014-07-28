<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
/**
 * Description of CollectorFactory
 *
 * @since  4.1.0
 */
class Collectors
{
    /**
     * actual sequence of data to reduce
     *
     * @type  Sequence
     */
    private $sequence;

    /**
     * constructor
     *
     * @param  \stubbles\lang\Sequence  $sequence
     */
    public function __construct(Sequence $sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * returns a collector for lists
     *
     * @api
     * @return  array
     */
    public function inList()
    {
        return $this->sequence->collect(Collector::forList());
    }

    /**
     * returns a collector for maps
     *
     * @api
     * @return  array
     */
    public function inMap(callable $selectKey = null, callable $selectValue = null)
    {
        return $this->sequence->collect(Collector::forMap($selectKey, $selectValue));
    }

    /**
     * creates collector which groups the elements in two partitions according to given predicate
     *
     * @api
     * @param   callable                  $predicate  function to evaluate in which partition an element belongs
     * @param   \stubbles\lang\Collector  $base       optional  defaults to Collector::forList()
     * @return  array
     */
    public function inPartitions(callable $predicate, Collector $base = null)
    {
        $base = (null === $base) ? Collector::forList() : $base;
        return $this->sequence->collectWith(
                function() use($base)
                {
                    return [true  => $base->restart(),
                            false => $base->restart()
                    ];
                },
                function(&$partitions, $element) use($predicate)
                {
                    $partitions[$predicate($element)]->accumulate($element);
                },
                function($partitions)
                {
                    return [true  => $partitions[true]->finish(),
                            false => $partitions[false]->finish()
                    ];
                }
        );
    }

    /**
     * creates collector which groups the elements according to given classifier
     *
     * @api
     * @param   callable                  $classifier  function to map elements to keys
     * @param   \stubbles\lang\Collector  $base        optional  defaults to Collector::forList()
     * @return  array
     */
    public function inGroups(callable $classifier, Collector $base = null)
    {
        $base = (null === $base) ? Collector::forList() : $base;
        return $this->sequence->collectWith(
                function() { return []; },
                function(&$groups, $element) use($classifier, $base)
                {
                    $key = $classifier($element);
                    if (!isset($groups[$key])) {
                        $groups[$key] = $base->restart();
                    }

                    $groups[$key]->accumulate($element, $key);
                },
                function($groups)
                {
                    foreach ($groups as $key => $group) {
                        $groups[$key] = $group->finish();
                    }

                    return $groups;
                }
        );
    }

    /**
     * creates collector which concatenates all elements into a single string
     *
     * @api
     * @param   string  $delimiter  optional  sepearator between elements
     * @param   string  $prefix     optional  string prefix
     * @param   string  $suffix     optional  string suffix
     * @return  string
     */
    public function asString($delimiter = ', ', $prefix = '', $suffix = '')
    {
        return $this->sequence->collectWith(
                function () { return null; },
                function(&$joinedElements, $element) use($prefix, $delimiter)
                {
                    if (null === $joinedElements) {
                        $joinedElements = $prefix . $element;
                    } else {
                        $joinedElements .= $delimiter . $element;
                    }
                },
                function($joinedElements) use($suffix) { return $joinedElements . $suffix; }
        );
    }
}
