<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation;
use stubbles\lang\iterator\RecursiveArrayIterator;
/**
 * Contains a list of all annotations for a target.
 *
 * @since  5.0.0
 */
class Annotations implements \IteratorAggregate
{
    /**
     * list of annotation types and their instances
     *
     * @type  array
     */
    private $types       = [];
    /**
     * target for which annotations are for
     *
     * @type  string
     */
    private $target;

    /**
     * constructor
     *
     * @param  string  $target
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * adds given annotation
     *
     * @internal  only to be called by the parser
     * @param   \stubbles\lang\reflect\annotation\Annotation  $annotation
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     */
    public function add(Annotation $annotation)
    {
        if (!isset($this->types[$annotation->type()])) {
            $this->types[$annotation->type()] = [$annotation];
        } else {
            $this->types[$annotation->type()][] = $annotation;
        }

        return $this;
    }

    /**
     * target for which annotations are for
     *
     * @return  string
     */
    public function target()
    {
        return $this->target;
    }

    /**
     * checks if at least one annotation of given type is present
     *
     * @api
     * @param   string  $type
     * @return  bool
     */
    public function contain($type)
    {
        return isset($this->types[$type]);
    }

    /**
     * returns a list of all annotations of this type
     *
     * @api
     * @param   string  $type
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     */
    public function of($type)
    {
        if ($this->contain($type)) {
            return $this->types[$type];
        }

        return [];
    }

    /**
     * returns a list of all annotations
     *
     * @api
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     */
    public function all()
    {
        $all = [];
        foreach ($this as $annotation) {
            $all[] = $annotation;
        }

        return $all;
    }

    /**
     * returns an iterator to iterate over all annotations
     *
     * @return  \Traversable
     */
    public function getIterator()
    {
        return new \RecursiveIteratorIterator(
                new RecursiveArrayIterator($this->types)
        );
    }
}
