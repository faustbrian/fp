<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Closure;

use function Cline\fp\set as fpSet;

if (!function_exists('set')) {
    /**
     * Sets a property or key immutably, returning a modified copy.
     *
     * @see Cline\fp\set()
     */
    function set(string $key, mixed $value): Closure
    {
        return fpSet($key, $value);
    }
}
