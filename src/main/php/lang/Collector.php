<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * The contents of this file draw heavily from XP Framework
 * https://github.com/xp-forge/sequence
 *
 * Copyright (c) 2001-2014, XP-Framework Team
 * All rights reserved.
 * https://github.com/xp-framework/xp-framework/blob/master/core/src/main/php/LICENCE
 *
 * @package  stubbles
 */
namespace stubbles\lang;
/**
 * A collector accumulates elements into a structure, optionally transforming the result into a final representation.
 *
 * @since  5.2.0
 */
class Collector
{
    /**
     * returns a fresh structure to collect elements into
     *
     * @type  callable
     */
    private $supplier;
    /**
     * structure to collect elements into
     *
     * @type  mixed
     */
    private $structure;
    /**
     * accumulates elements into structure
     *
     * @type  callable
     */
    private $accumulator;
    /**
     * final operation after all elements have been added to the structure
     *
     * @type  callable
     */
    private $finisher;

    /**
     * constructor
     *
     * @param  callable  $supplier     returns a fresh structure to collect elements into
     * @param  callable  $accumulator  accumulates elements into structure
     * @param  callable  $finisher     optional  final operation after all elements have been added to the structure
     */
    public function __construct(callable $supplier, callable $accumulator, callable $finisher = null)
    {
        $this->supplier    = $supplier;
        $this->structure   = $supplier();
        $this->accumulator = $accumulator;
        $this->finisher    = $finisher;
    }

    /**
     * returns a collector for lists
     *
     * @api
     * @return  \stubbles\lang\Collector
     */
    public static function forList()
    {
        return new self(
                function() { return []; },
                function(&$list, $element) { $list[] = $element; }
        );
    }

    /**
     * returns a collector for maps
     *
     * @api
     * @param   callable  $keySelector    optional  function to select the key for the map entry
     * @param   callable  $valueSelector  optional  function to select the value for the map entry
     * @return  \stubbles\lang\Collector
     */
    public static function forMap(callable $keySelector = null, callable $valueSelector = null)
    {
        $selectKey   = (null !== $keySelector) ? $keySelector : function($value, $key) { return $key; };
        $selectValue = (null !== $valueSelector) ? $valueSelector : function($value) { return $value; };
        return new self(
                function() { return []; },
                function(&$map, $element, $key) use($selectKey, $selectValue)
                {
                    $map[$selectKey($element, $key)] = $selectValue($element, $key);
                }
        );
    }

    /**
     * restarts collection with a fresh instance
     *
     * @return  \stubbles\lang\Collector
     */
    public function fork()
    {
        return new self($this->supplier, $this->accumulator, $this->finisher);
    }

    /**
     * adds given element and key to result structure
     *
     * @param  mixed  $element
     * @param  mixed  $key
     */
    public function accumulate($element, $key)
    {
        $accumulate = $this->accumulator;
        $accumulate($this->structure, $element, $key);
    }

    /**
     * finishes collection of result
     *
     * @param   mixed  $result
     * @return  mixed  finished result
     */
    public function finish()
    {
        if (null === $this->finisher) {
            return $this->structure;
        }

        $finish = $this->finisher;
        return $finish($this->structure);
    }
}
