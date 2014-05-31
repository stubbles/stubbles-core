<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc;
use stubbles\test\ioc\Mikey;
use stubbles\test\ioc\SessionBindingScope;
/**
 * Test for stubbles\ioc\Injector with the session scope.
 *
 * @group  ioc
 */
class InjectorSessionTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * binder instance to be used in tests
     *
     * @var  Binder
     */
    protected $binder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->binder = new Binder();
        $this->binder->setSessionScope(new SessionBindingScope());
    }

    /**
     * @test
     */
    public function storesCreatedInstanceInSession()
    {
        $this->binder->bind('stubbles\test\ioc\Person2')
                     ->to('stubbles\test\ioc\Mikey')
                     ->inSession();
        $injector = $this->binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Person2'));

        $text = $injector->getInstance('stubbles\test\ioc\Person2');
        $this->assertInstanceOf('stubbles\test\ioc\Person2', $text);
        $this->assertInstanceOf('stubbles\test\ioc\Mikey', $text);
        $this->assertSame($text, $injector->getInstance('stubbles\test\ioc\Person2'));
    }

    /**
     * @test
     */
    public function usesInstanceFromSessionIfAvailable()
    {
        $this->binder->bind('stubbles\test\ioc\Person2')
                     ->to('stubbles\test\ioc\Mikey')
                     ->inSession();
        $injector = $this->binder->getInjector();
        $text     = new Mikey();
        SessionBindingScope::$instances['stubbles\test\ioc\Mikey'] = $text;
        $this->assertTrue($injector->hasBinding('stubbles\test\ioc\Person2'));
        $this->assertSame($text, $injector->getInstance('stubbles\test\ioc\Person2'));
    }
}
