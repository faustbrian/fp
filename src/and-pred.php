<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_all;

/**
 * Combines multiple predicates with AND logic into a single predicate.
 *
 * Returns a predicate function that evaluates to true only if all provided predicates
 * return true for the given arguments. Each predicate receives the same arguments that
 * are passed to the combined predicate. Short-circuits on the first false result for
 * performance optimization.
 *
 * ```php
 * // Combine multiple validation rules
 * $isValidAge = andPred(
 *     fn($age) => is_numeric($age),
 *     fn($age) => $age >= 18,
 *     fn($age) => $age <= 120
 * );
 * $result = $isValidAge(25); // true
 * $result = $isValidAge(15); // false
 *
 * // Chain multiple type checks
 * $isValidUser = andPred(
 *     fn($user) => isset($user['name'], $user['email']),
 *     fn($user) => strlen($user['name']) > 0,
 *     fn($user) => filter_var($user['email'], FILTER_VALIDATE_EMAIL)
 * );
 * $result = $isValidUser(['name' => 'John', 'email' => 'john@example.com']); // true
 * ```
 *
 * @param  callable(mixed...): bool ...$predicates Variable number of predicate functions to combine
 *                                                 with AND logic. Each predicate receives identical
 *                                                 arguments and must return a boolean value. All
 *                                                 predicates must return true for the combined
 *                                                 predicate to return true.
 * @return Closure(mixed...): bool  Combined predicate function accepting any arguments and returning
 *                                  true only if all predicates return true. Short-circuits on the
 *                                  first false result to avoid unnecessary predicate evaluations.
 *
 * @see orPred() For combining predicates with OR logic
 * @see notPred() For negating a predicate
 */
function andPred(callable ...$predicates): Closure
{
    return static fn (mixed ...$args): bool => array_all($predicates, fn ($predicate): bool => $predicate(...$args));
}
