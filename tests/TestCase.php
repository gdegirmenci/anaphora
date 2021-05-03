<?php

namespace Tests;

use ReflectionClass;
use ReflectionException;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param object &$object
     * @param string $methodName
     * @param array $parameters
     * @throws ReflectionException
     * @return mixed
     */
    protected function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param mixed $object
     * @param string $property
     * @param mixed $value
     * @throws ReflectionException
     */
    public function assertProperty($object, string $property, $value): void
    {
        $this->assertEquals($value, $this->getPrivateProperty($object, $property));
    }

    /**
     * @param mixed $object
     * @param string $property
     * @return mixed
     * @throws ReflectionException
     */
    public function getPrivateProperty($object, string $property)
    {
        $reflection = new ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    /**
     * @param mixed $object
     * @param mixed $property
     * @throws ReflectionException
     * @param mixed $value
     * @return void
     */
    public function setPrivateProperty($object, $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
