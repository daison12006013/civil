<?php

namespace Civil;

abstract class Query
{
    /**
     * The model to use.
     *
     * @var string
     */
    protected $model = null;

    /**
     * All builders will live here for method call.
     *
     * @var array
     */
    protected $registrar = [];

    /**
     * The builder to be used.
     *
     * @var mixed
     */
    protected $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Magic method to check registrar.
     *
     * @param  string $name
     * @param  array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if (! isset($this->registrar[$name])) {
            throw new \Exception('Builder not found in the $registrar.');
        }

        $reflection = new \ReflectionClass($this->registrar[$name]);

        $this->call($reflection->newInstanceArgs($args));
    }

    /**
     * Initialize the query.
     *
     * @param  mixed $builder
     * @return Query
     */
    public static function initialize($builder = null)
    {
        if ($builder === null) {
            $model = (new static(null))->model;

            if (! $model) {
                throw new \Exception('Provide the [model] property in your query class.');
            }

            $builder = (new $model)->newQuery();
        }

        return new static($builder);
    }

    /**
     * Initialize the query.
     *
     * @param  mixed $builder
     * @return Query
     */
    public static function initialise($builder = null)
    {
        return self::initialize($builder);
    }

    /**
     * Get the builder class.
     *
     * @return mixed
     */
    public function builder()
    {
        return $this->builder;
    }

    /**
     * Register a builder by method.
     *
     * @param  string $method
     * @param  string $class
     * @return Query
     */
    public function register($method, $class)
    {
        $this->registrar[$method] = $class;

        return $this;
    }

    /**
     * Call an instance.
     *
     * @param  ElevationInterface $instance
     * @return void
     */
    public function call($instance)
    {
        if (! $instance instanceof ElevationInterface) {
            throw new \Exception(sprintf(
                'Class [%s] must implements %s',
                $instance->getClass(),
                ElevationInterface::class
            ));
        }

        $instance->handle($this->builder);
    }
}
