<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\BaseReflectionClass;
/**
 * Interface to be used in the test.
 */
interface Text
{
    /**
     * displays text
     */
    public function display();
}
/**
 * Class to be used in the test.
 */
class Greeting implements Text
{
    /**
     * displays text
     */
    public function display()
    {
        echo 'Hello World';
    }
}
/**
 * Session binding scope for the purpose of this test.
 */
class SessionBindingScope extends BaseObject implements BindingScope
{
    /**
     * simulate session, sufficient for purpose of this test
     *
     * @type  array
     */
    public static $instances = array();

    /**
     * returns the requested instance from the scope
     *
     * @param   BaseReflectionClass  $impl      concrete implementation
     * @param   InjectionProvider    $provider
     * @return  Object
     */
    public function getInstance(BaseReflectionClass $impl, InjectionProvider $provider)
    {
        $key = $impl->getName();
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }

        self::$instances[$key] = $provider->get();
        return self::$instances[$key];
    }
}
/**
 * Test for net\stubbles\ioc\Injector with the session scope.
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
        $this->binder->bind('net\\stubbles\\ioc\\Text')
                     ->to('net\\stubbles\\ioc\\Greeting')
                     ->inSession();
        $injector = $this->binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Text'));

        $text = $injector->getInstance('net\\stubbles\\ioc\\Text');
        $this->assertInstanceOf('net\\stubbles\\ioc\\Text', $text);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Greeting', $text);
        $this->assertSame($text, $injector->getInstance('net\\stubbles\\ioc\\Text'));
    }

    /**
     * @test
     */
    public function usesInstanceFromSessionIfAvailable()
    {
        $this->binder->bind('net\\stubbles\\ioc\\Text')
                     ->to('net\\stubbles\\ioc\\Greeting')
                     ->inSession();
        $injector = $this->binder->getInjector();
        $text     = new Greeting();
        SessionBindingScope::$instances['net\\stubbles\\ioc\\Greeting'] = $text;
        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\Text'));
        $this->assertSame($text, $injector->getInstance('net\\stubbles\\ioc\\Text'));
    }
}
?>