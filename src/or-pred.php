<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Closure;

use function array_any;

/**
 * Combines multiple predicate functions using logical OR semantics.
 *
 * Creates a new predicate that returns true if at least one of the provided
 * predicates returns true when called with the same arguments. Uses short-circuit
 * evaluation, stopping at the first predicate that returns true without evaluating
 * the remaining predicates. This is useful for filtering with multiple conditions
 * where any match is sufficient.
 *
 * ```php
 * $isShort = fn($s) => strlen($s) < 5;
 * $isNumeric = fn($s) => is_numeric($s);
 * $isValid = orPred($isShort, $isNumeric);
 *
 * $isValid('hi'); // true (short)
 * $isValid('12345'); // true (numeric)
 * $isValid('hello world'); // false (neither short nor numeric)
 *
 * // Filtering with multiple conditions
 * $items = ['foo', '123', 'bar', 'hello world'];
 * $valid = array_filter($items, orPred($isShort, $isNumeric)); // ['foo', '123', 'bar']
 * ```
 *
 * @param  callable ...$predicates Variable number of predicate functions to combine. Each
 *                                 predicate will receive the same arguments and should return
 *                                 a boolean value. Evaluation stops at the first true result.
 * @return Closure  A new predicate that returns true if any of the provided predicates
 *                  returns true, false otherwise. Accepts the same arguments as the
 *                  original predicates.
 */
function orPred(callable ...$predicates): Closure
{
    return static fn (mixed ...$args): bool => array_any($predicates, fn ($predicate) => $predicate(...$args));
}
