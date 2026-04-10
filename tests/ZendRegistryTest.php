<?php

use PHPUnit\Framework\TestCase;

class ZendRegistryTest extends TestCase
{
    public function setUp(): void
    {
        Zend_Registry::_unsetInstance();
    }

    public function tearDown(): void
    {
        Zend_Registry::_unsetInstance();
    }

    public function testRegistryUninitIsRegistered(): void
    {
        // checking entry is set returns false,
        // but does not initialize instance
        $this->assertFalse(Zend_Registry::isRegistered('objectname'));
    }

    public function testRegistryUninitGetInstance(): void
    {
        // getting instance initializes instance
        $registry = Zend_Registry::getInstance();
        $this->assertInstanceOf(Zend_Registry::class, $registry);
    }

    public function testRegistryUninitSet(): void
    {
        // setting value initializes instance
        Zend_Registry::set('foo', 'bar');
        $registry = Zend_Registry::getInstance();
        $this->assertInstanceOf(Zend_Registry::class, $registry);
    }

    public function testRegistryUninitGet(): void
    {
        // getting value initializes instance
        // but throws different exception because
        // entry is not registered
        try {
            Zend_Registry::get('foo');
            $this->fail('Expected exception when trying to fetch a non-existent key.');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('No entry is registered for key', $e->getMessage());
        }
        $registry = Zend_Registry::getInstance();
        $this->assertInstanceOf(Zend_Registry::class, $registry);
    }

    public function testRegistrySingletonSameness(): void
    {
        $registry1 = Zend_Registry::getInstance();
        $registry2 = Zend_Registry::getInstance();
        $this->assertInstanceOf(Zend_Registry::class, $registry1);
        $this->assertInstanceOf(Zend_Registry::class, $registry2);
        $this->assertEquals($registry1, $registry2);
        $this->assertSame($registry1, $registry2);
    }

    public function testRegistryEqualContents(): void
    {
        Zend_Registry::set('foo', 'bar');
        $registry1 = Zend_Registry::getInstance();
        $registry2 = new Zend_Registry(array('foo' => 'bar'));
        $this->assertEquals($registry1, $registry2);
        $this->assertNotSame($registry1, $registry2);
    }

    public function testRegistryUnequalContents(): void
    {
        $registry1 = Zend_Registry::getInstance();
        $registry2 = new Zend_Registry(array('foo' => 'bar'));
        $this->assertNotEquals($registry1, $registry2);
        $this->assertNotSame($registry1, $registry2);
    }

    public function testRegistrySetAndIsRegistered(): void
    {
        $this->assertFalse(Zend_Registry::isRegistered('foo'));
        Zend_Registry::set('foo', 'bar');
        $this->assertTrue(Zend_Registry::isRegistered('foo'));
    }

    public function testRegistryGet(): void
    {
        Zend_Registry::set('foo', 'bar');
        $bar = Zend_Registry::get('foo');
        $this->assertEquals('bar', $bar);
    }

    public function testRegistryArrayObject(): void
    {
        $this->assertInstanceOf(ArrayObject::class, Zend_Registry::getInstance());
    }

    public function testRegistryArrayAsProps(): void
    {
        $registry = new Zend_Registry(array(), ArrayObject::ARRAY_AS_PROPS);
        $registry->foo = 'bar';
        $this->assertTrue(isset($registry->foo));
        $this->assertEquals('bar', $registry->foo);
    }

    public function testRegistryExceptionInvalidClassname(): void
    {
        try {
            $registry = Zend_Registry::setClassName(new StdClass());
            $this->fail('Expected exception, because setClassName() wants a string');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Argument is not a class name', $e->getMessage());
        }
    }

    public function testRegistryExceptionNoEntry(): void
    {
        try {
            $foo = Zend_Registry::get('foo');
            $this->fail('Expected exception when trying to fetch a non-existent key.');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('No entry is registered for key', $e->getMessage());
        }
    }

    public function testRegistryExceptionAlreadyInitialized(): void
    {
        $registry = Zend_Registry::getInstance();

        try {
            Zend_Registry::setClassName('anyclass');
            $this->fail('Expected exception, because we cannot initialize the registry if it is already initialized.');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Registry is already initialized', $e->getMessage());
        }
        try {
            Zend_Registry::setInstance(new Zend_Registry());
            $this->fail('Expected exception, because we cannot initialize the registry if it is already initialized.');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('Registry is already initialized', $e->getMessage());
        }
    }

    public function testRegistryExceptionClassNotFound(): void
    {
        try {
            @Zend_Registry::setClassName('classdoesnotexist');
            $this->fail('Expected exception, because we cannot initialize the registry using a non-existent class.');
        } catch (Zend_Exception $e) {
            $this->assertStringContainsString('class does not exist', $e->getMessage());
        }
    }

    public function testDefaultRegistryArrayAsPropsZF4654(): void
    {
        $registry = Zend_Registry::getInstance();
        $registry->bar = "baz";
        $this->assertEquals('baz', Zend_Registry::get('bar'));
    }
}
