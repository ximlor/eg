<?php

namespace Illuminate\Container;

use Closure;
use ArrayAccess;
use Illuminate\Contract\Container\Container as ContainerInterface;

class Container implements ArrayAccess, ContainerInterface
{
    protected $binds = [];

    protected $instances = [];

    protected $aliases = [];

    protected $tags = [];

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract) {
        $abstract = $this->normalize($abstract);

        if (isset($this->binds[$abstract]) || isset($this->instances[$abstract])) {
            return true;
        }

        return false;
    }

    /**
     * Alias a type to a different name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     */
    public function alias($abstract, $alias) {
        $this->aliases[$alias] = $this->normalize($abstract);
    }

    /**
     * Assign a set of tags to a given binding.
     *
     * @param  array|string  $abstracts
     * @param  array|mixed   ...$tags
     * @return void
     */
    public function tag($abstracts, $tags) {
        $tags = is_array($tags) ? $tags : array_slice(func_get_args(), 1);

        foreach ($tags as $tag) {
            if (! isset($this->tags[$tag])) {
                $this->tags[$tag] = [];
            }

            foreach ((array) $abstracts as $abstract) {
                $this->tags[$tag][] = $this->normalize($abstract);
            }
        }
    }

    /**
     * Resolve all of the bindings for a given tag.
     *
     * @param  array  $tag
     * @return array
     */
    public function tagged($tag) {
        
    }

    /**
     * Register a binding with the container.
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        $abstract = $this->normalize($abstract);
        $concrete = $this->normalize($concrete);

        // $abstract 为关联数组时设置别名
        if (is_array($abstract)) {
            list($abstract, $alias) = $this->extractAlias($abstract);
            $this->alias($abstract, $alias);
        }

        // 解绑
        $this->dropStaleInstances($abstract);

        if ($concrete instanceof Closure) {
            $this->binds[$abstract] = [$concrete, $shared];
        }
        else {
            $this->instances[$abstract] = [$concrete, $shared];
        }
    }

    /**
     * Register a binding if it hasn't already been registered.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bindIf($abstract, $concrete = null, $shared = false) {

    }

    /**
     * Register a shared binding in the container.
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null) {
        $this->bind($abstract, $concrete, false);
    }

    /**
     * "Extend" an abstract type in the container.
     *
     * @param  string    $abstract
     * @param  \Closure  $closure
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function extend($abstract, Closure $closure) {

    }

    /**
     * Register an existing instance as shared in the container.
     *
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return void
     */
    public function instance($abstract, $instance) {

    }

    /**
     * Define a contextual binding.
     *
     * @param  string  $concrete
     * @return \Illuminate\Contracts\Container\ContextualBindingBuilder
     */
    public function when($concrete) {

    }

    public function make($abstract, array $parameters = []) {
        $abstract = $this->normalize($abstract);

        if(isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        array_unshift($parameters, $this);

        return call_user_func_array($this->binds[$abstract], $parameters);
    }

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null) {

    }

    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param  string $abstract
     * @return bool
     */
    public function resolved($abstract) {

    }

    /**
     * Register a new resolving callback.
     *
     * @param  string    $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function resolving($abstract, Closure $callback = null) {

    }

    /**
     * Register a new after resolving callback.
     *
     * @param  string    $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function afterResolving($abstract, Closure $callback = null) {

    }

    /**
     * Whether a offset exists
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return $this->bound($offset);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->make($offset);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value) {

        // If the value is not a Closure, we will make it one.
        // This simply gives more "drop-in" replacement functionality for the Pimple which this
        // container's simplest functions are base modeled and built after.
        if (! $value instanceof Closure) {
            $value = function () use ($value) {
                return $value;
            };
        }

        $this->bind($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset) {

    }

    /**
     * Normalize the given class name by removing leading slashes.
     *
     * @param  mixed  $service
     * @return mixed
     */
    protected function normalize($name) {
        return is_string($name) ? ltrim($name, '\\') : $name;
    }

    /**
     * Extract the type and alias from a given definition.
     *
     * @param  array  $definition
     * @return array
     */
    protected function extractAlias(array $definition) {
        return [key($definition), current($definition)];
    }

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param  string  $abstract
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract], $this->aliases[$abstract]);
    }
}