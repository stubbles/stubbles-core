<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\ioc;
/**
 * Helper class for the test.
 */
class PropertyReceiver
{
    public $foo;

    public $bar;

    /**
     *
     * @param  string  $foo
     * @Inject
     * @Property{foo}('example.foo')
     * @Property{bar}('example.bar')
     */
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
