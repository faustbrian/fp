<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

/**
 * Creates a callable that invokes a named method on an object with specified arguments.
 *
 * Returns a closure that accepts an object and calls the specified method name
 * with the provided arguments. Enables method invocation in functional pipelines
 * and higher-order functions where you need to pass method calls as callbacks.
 * Only works with public methods due to PHP visibility rules for dynamic method calls.
 *
 * ```php
 * $toString = method('toString');
 * $toString($user); // Equivalent to: $user->toString()
 *
 * $setStatus = method('setStatus', 'active', true);
 * $setStatus($order); // Equivalent to: $order->setStatus('active', true)
 *
 * // Using in array mapping to call method on each object
 * $users = User::all();
 * $emails = array_map(method('getEmail'), $users);
 *
 * // Using in a pipeline for fluent method chaining
 * pipe(
 *     new StringBuilder(),
 *     method('append', 'Hello'),
 *     method('append', ' World'),
 *     method('toString')
 * ); // 'Hello World'
 *
 * // Chaining collection transformations
 * $collection->map(method('trim'))
 *            ->filter(method('isNotEmpty'))
 *            ->map(method('toUpper'));
 * ```
 *
 * @param  string  $method  The public method name to invoke on the object.
 *                          Must be accessible and exist on the target object
 *                          or a fatal error will occur at runtime.
 * @param  mixed   ...$args Variable number of arguments to pass to the method.
 *                          These arguments are bound when creating the closure
 *                          and will be passed to the method in order.
 * @return Closure Returns a closure that accepts an object and invokes
 *                 the specified method with the bound arguments, returning
 *                 whatever the method returns
 */
function method(string $method, ...$args): Closure
{
    return static fn (object $o): mixed => $o->{$method}(...$args);
}
