<?php

/**
 * @package  saad/laravel-model-images
 *
 * @author Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license MIT MIT
 */

namespace Saad\ModelImages\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

trait MacroableModel {

	use Macroable {
		__call as macroCall;
	}

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator($key)
    {
        $mutator = 'set'.Str::studly($key).'Attribute';
        return method_exists($this, $mutator) || static::hasMacro($mutator);
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutator($key)
    {
        $mutator = 'get'.Str::studly($key).'Attribute';
        return method_exists($this, $mutator) || static::hasMacro($mutator);
    }

	/**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
    	if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (in_array($method, ['increment', 'decrement'])) {
            return $this->$method(...$parameters);
        }

        return $this->newQuery()->$method(...$parameters);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}