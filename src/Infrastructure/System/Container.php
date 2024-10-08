<?php

namespace VendingMachine\Infrastructure\System;

class Container
{
    public function __construct(
        protected $bindings = [],
        protected $instances = [],
        protected $parameters = [],
    ) {
    }

    public function parameter(string $class, string $parameter, $value): void
    {
        $this->parameters[$class][$parameter] = $value;
    }

    public function get(string $paramType): object
    {
        if (isset($this->instances[$paramType])) {
            return $this->instances[$paramType];
        }

        if (isset($this->bindings[$paramType])) {
            return $this->resolve($this->bindings[$paramType]);
        }

        return $this->resolve($paramType);
    }

    protected function resolve($class): object
    {
        if ($class instanceof \Closure) {
            return $class($this);
        }

        $reflector = new \ReflectionClass($class);

        if ($reflector->isInterface()) {
            $implementations = $this->getImplementations($reflector->getName());
            if (0 === count($implementations)) {
                throw new \Exception("No implementation found for interface {$reflector->getName()}");
            }
            if (count($implementations) > 1) {
                throw new \Exception("Multiple implementations found for interface {$reflector->getName()}");
            }

            if (array_key_exists($implementations[0], $this->parameters)) {
                $ref  = new \ReflectionClass($implementations[0]);
                $args = [];
                foreach ($this->parameters as $parameterList) {
                    foreach ($parameterList as $param) {
                        $args[] = $param;
                    }
                }

                return $ref->newInstanceArgs($args);
            }

            return new $implementations[0]();
        }

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class $class is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $class();
        }

        $parameters   = $constructor->getParameters();
        $dependencies = array_map(function ($parameter) use ($class) {
            $type = $parameter->getType();

            if (null === $type) {
                $parameterName = $parameter->getName();
                if (isset($this->parameters[$class][$parameterName])) {
                    return $this->parameters[$class][$parameterName];
                } else {
                    throw new \Exception("Cannot resolve class dependency for parameter {$parameter->getName()}");
                }
            }

            if ($type->isBuiltin()) {
                $parameterName = $parameter->getName();
                if (isset($this->parameters[$class][$parameterName])) {
                    return $this->parameters[$class][$parameterName];
                } else {
                    throw new \Exception("Cannot resolve built-in type dependency for parameter {$parameter->getName()}");
                }
            }

            return $this->get($type->getName());
        }, $parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    private function getImplementations(string $interface): array
    {
        $implementations = [];
        $directory       = new \RecursiveDirectoryIterator(__DIR__ . '/../');
        $iterator        = new \RecursiveIteratorIterator($directory);
        $regex           = new \RegexIterator($iterator, '/^.+\.php$/i', \RegexIterator::GET_MATCH);

        foreach ($regex as $file) {
            require_once $file[0];
            $classes = get_declared_classes();
            foreach ($classes as $class) {
                $reflector = new \ReflectionClass($class);
                if ($reflector->implementsInterface($interface) && !$reflector->isAbstract()) {
                    if (in_array($class, $implementations)) {
                        continue;
                    }

                    $implementations[] = $class;
                }
            }
        }

        return $implementations;
    }
}
