<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Cline\fp\Exceptions\RetryFailedException;
use Closure;
use Throwable;

use function is_int;
use function throw_if;
use function usleep;

/**
 * Retries a fallible callable with configurable attempts and exponential backoff support.
 *
 * Returns a closure that executes a callable up to maxAttempts times, retrying on any
 * thrown exception. If all attempts fail, the last exception is re-thrown. This is useful
 * for handling transient failures in network requests, database operations, or external
 * service calls.
 *
 * The optional backoff callable receives the attempt number (1-indexed) and returns the
 * number of microseconds to sleep before the next retry. Common patterns include constant
 * delay, linear backoff, or exponential backoff with jitter.
 *
 * ```php
 * // Simple retry without backoff
 * $result = pipe(
 *     fn() => fetchFromApi(),
 *     retry(3)
 * );
 *
 * // Exponential backoff: 100ms, 200ms, 400ms
 * $backoff = fn($attempt) => 100000 * (2 ** ($attempt - 1));
 * $result = pipe(
 *     fn() => queryDatabase(),
 *     retry(4, $backoff)
 * );
 * ```
 *
 * @param  int           $maxAttempts Maximum number of execution attempts before giving up. Must be at least 1.
 *                                    The callable will be tried this many times before the exception is thrown.
 * @param  null|callable $backoff     Optional backoff strategy with signature (int $attempt): int that
 *                                    receives the 1-indexed attempt number and returns microseconds to sleep
 *                                    before the next retry. No delay occurs after the final failed attempt.
 * @return Closure       A closure with signature (callable $fn): mixed that executes the callable
 *                       with retry logic and returns the successful result or throws the last exception
 */
function retry(int $maxAttempts, ?callable $backoff = null): Closure
{
    return static function (callable $fn) use ($maxAttempts, $backoff): mixed {
        /** @var null|Throwable $lastException */
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxAttempts; ++$attempt) {
            try {
                return $fn();
            } catch (Throwable $e) {
                $lastException = $e;

                // Don't sleep after the last failed attempt
                if ($attempt < $maxAttempts && $backoff !== null) {
                    $microseconds = $backoff($attempt);

                    if ($microseconds > 0 && is_int($microseconds)) {
                        usleep($microseconds);
                    }
                }
            }
        }

        throw_if($lastException === null, RetryFailedException::create());

        throw $lastException;
    };
}
