<?php

namespace Civil;

use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

/**
 * The query will be your main factory to produce/process a builder.
 *
 * @author Daison Carino <daison12006013@gmail.com>
 */
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

    /**
     * __construct
     *
     * @param mixed $builder
     * @return void
     */
    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Magic method to check registrar.
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        # if method not exists
        # we will call the Illuminate Builder instead
        if (!isset($this->registrar[$method])) {
            return call_user_func_array([$this->builder, $method], $args);
        }

        # call the builder at this moment
        $reflection = new ReflectionClass($this->registrar[$method]);
        $this->makeBuilderInstance($reflection->newInstanceArgs($args));

        return $this;
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

            if (!$model) {
                throw new Exception('Provide the [model] property in your query class.');
            }

            $builder = (new $model)->newQuery();
        }

        return new static($builder);
    }

    /**
     * Initialize or Initialise in english as fallback.
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
     * Get the model instance
     *
     * @return mixed
     */
    public function modelInstance()
    {
        return $this->model ? new $this->model : false;
    }

    /**
     * Rebuild the builder.
     *
     * @param string $alias
     * @return void
     */
    public function rebuild($alias = null)
    {
        $this->builder = static::wrapper(
            $this->builder,
            $alias ?? ($mi = $this->modelInstance()) ? $mi->getTable() : 'civil'
        );

        return $this;
    }

    /**
     * Wrap your builder into new table.
     *
     * @param  mixed  $builder
     * @param  string $table
     * @param  mixed  $connection
     * @return void
     */
    public static function wrapper($builder, $table, $connection = null)
    {
        $class = class_exists(Application::class) ? DB::class : Capsule::class;

        return $class::connection($connection)->table(
            $class::raw(sprintf('(%s) as %s', static::toRaw($builder), $table))
        );
    }

    /**
     * Transform query builder to raw query.
     *
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $builder
     * @return string
     */
    public static function toRaw($builder)
    {
        # first escape the custom percent in the builder
        $str = str_replace('%', '%%', $builder->toSql());

        # then replace all ? into %s
        $str = str_replace(['?'], ['\'%s\''], $str);

        # now pass in the bindings
        return vsprintf($str, $builder->getBindings());
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
    public function makeBuilderInstance($instance)
    {
        if (!$instance instanceof ElevationInterface) {
            throw new Exception(sprintf(
                'Class [%s] must implements %s',
                $instance->getClass(),
                ElevationInterface::class
            ));
        }

        $instance->handle($this->builder);
    }
    /**
     * Paginate the given query.
     *
     * @param  int  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * @throws \InvalidArgumentException
     */
    public function paginate(int $perPage = null, array $columns = ['*'], string $pageName = 'page', int $page = null)
    {
        $perPage = $perPage ?: $this->modelInstance()->getPerPage();
        $page    = $page ?: Paginator::resolveCurrentPage($pageName);

        $total = (clone $this->builder())->count();
        $items = (clone $this->builder())
            ->skip(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return new LengthAwarePaginator($items->toArray(), $total, $perPage, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
}
