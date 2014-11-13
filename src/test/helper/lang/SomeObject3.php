<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\lang;
/**
 * Helper class for the test.
 *
 * @since  3.1.0
 */
class SomeObject3
{
    /**
     * a property
     *
     * @type  stub1BaseObject
     */
    protected $foo;
    /**
     * a property
     *
     * @type  stub2BaseObject
     */
    protected $bar;
    /**
     * a property
     *
     * @type  mixed
     */
    protected $baz;

    /**
     *
     * @param  SomeObject1  $foo
     * @param  SomeObject2  $bar
     * @param  mixed                $baz
     */
    public function __construct(SomeObject1 $foo, SomeObject2 $bar, $baz)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
    }

    /**
     * returns foo
     *
     * @return  SomeObject1
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * returns bar
     *
     * @return  SomeObject2
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * returns baz
     *
     * @return  mixed
     */
    public function getBaz()
    {
        return $this->baz;
    }

    /**
     * Returns a string representation of itself.
     *
     * @return  string
     */
    public function __toString()
    {
        return \stubbles\lang\__toString($this);
    }
}
