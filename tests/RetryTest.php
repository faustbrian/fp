<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Exception;
use InvalidArgumentException;
use LogicException;
use RuntimeException;

use function Cline\fp\retry;
use function describe;
use function expect;
use function microtime;
use function sprintf;
use function test;
use function throw_if;

describe('retry', function (): void {
    describe('Happy Paths', function (): void {
        test('successfully executes on first attempt', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): string {
                ++$attempts;

                return 'success';
            };

            $retrier = retry(3);
            $result = $retrier($callable);

            expect($result)->toBe('success');
            expect($attempts)->toBe(1);
        });

        test('retries and succeeds after initial failures', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): string {
                ++$attempts;

                throw_if($attempts < 3, new RuntimeException(sprintf('Attempt %d failed', $attempts)));

                return 'success after retries';
            };

            $retrier = retry(5);
            $result = $retrier($callable);

            expect($result)->toBe('success after retries');
            expect($attempts)->toBe(3);
        });

        test('succeeds on last allowed attempt', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): string {
                ++$attempts;

                throw_if($attempts < 3, new RuntimeException(sprintf('Attempt %d failed', $attempts)));

                return 'just made it';
            };

            $retrier = retry(3);
            $result = $retrier($callable);

            expect($result)->toBe('just made it');
            expect($attempts)->toBe(3);
        });

        test('works with callable that returns falsy values', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): false {
                ++$attempts;

                return false;
            };

            $retrier = retry(2);
            $result = $retrier($callable);

            expect($result)->toBe(false);
            expect($attempts)->toBe(1);
        });

        test('works with callable that returns null', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): null {
                ++$attempts;

                return null;
            };

            $retrier = retry(2);
            $result = $retrier($callable);

            expect($result)->toBeNull();
            expect($attempts)->toBe(1);
        });

        test('works with callable that returns zero', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): int {
                ++$attempts;

                return 0;
            };

            $retrier = retry(2);
            $result = $retrier($callable);

            expect($result)->toBe(0);
            expect($attempts)->toBe(1);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws last exception after exhausting all retries', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): void {
                ++$attempts;

                throw new RuntimeException(sprintf('Attempt %d failed', $attempts));
            };

            $retrier = retry(3);

            expect(fn () => $retrier($callable))
                ->toThrow(RuntimeException::class, 'Attempt 3 failed');
            expect($attempts)->toBe(3);
        });

        test('throws last exception with different exception types', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): void {
                ++$attempts;

                throw_if($attempts === 1, new InvalidArgumentException('First error'));

                throw_if($attempts === 2, new LogicException('Second error'));

                throw new RuntimeException('Final error');
            };

            $retrier = retry(3);

            expect(fn () => $retrier($callable))
                ->toThrow(RuntimeException::class, 'Final error');
            expect($attempts)->toBe(3);
        });

        test('fails immediately with single attempt allowed', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): void {
                ++$attempts;

                throw new Exception('Failed');
            };

            $retrier = retry(1);

            expect(fn () => $retrier($callable))
                ->toThrow(Exception::class, 'Failed');
            expect($attempts)->toBe(1);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles different exception types across attempts', function (): void {
            $attempts = 0;
            $exceptions = [
                new InvalidArgumentException('Invalid argument'),
                new RuntimeException('Runtime error'),
                new LogicException('Logic error'),
                new Exception('Generic error'),
            ];

            $callable = function () use (&$attempts, $exceptions): void {
                $exception = $exceptions[$attempts] ?? new Exception('Unexpected');
                ++$attempts;

                throw $exception;
            };

            $retrier = retry(4);

            expect(fn () => $retrier($callable))
                ->toThrow(Exception::class, 'Generic error');
            expect($attempts)->toBe(4);
        });

        test('works with callable returning arrays', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): array {
                ++$attempts;

                throw_if($attempts < 2, new Exception('Not yet'));

                return ['data' => 'value'];
            };

            $retrier = retry(3);
            $result = $retrier($callable);

            expect($result)->toBe(['data' => 'value']);
            expect($attempts)->toBe(2);
        });

        test('works with callable returning objects', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts) {
                ++$attempts;

                throw_if($attempts < 2, new Exception('Not yet'));

                return (object) ['property' => 'value'];
            };

            $retrier = retry(3);
            $result = $retrier($callable);

            expect($result)->toBeObject();
            expect($result->property)->toBe('value');
            expect($attempts)->toBe(2);
        });
    });

    describe('Backoff Functionality', function (): void {
        test('retries without delay when backoff is null', function (): void {
            $attempts = 0;
            $callable = function () use (&$attempts): string {
                ++$attempts;

                throw_if($attempts < 3, new Exception('Retry'));

                return 'success';
            };

            $startTime = microtime(true);
            $retrier = retry(3);
            $result = $retrier($callable);
            $endTime = microtime(true);

            expect($result)->toBe('success');
            expect($attempts)->toBe(3);
            // Should complete quickly without delays
            expect($endTime - $startTime)->toBeLessThan(0.1);
        });

        test('applies backoff delays between retries', function (): void {
            $attempts = 0;
            $backoffCalls = [];

            $callable = function () use (&$attempts): string {
                ++$attempts;

                throw_if($attempts < 3, new Exception('Retry'));

                return 'success';
            };

            // Backoff that returns microseconds for usleep
            $backoff = function (int $attempt) use (&$backoffCalls): int {
                $backoffCalls[] = $attempt;

                return 10_000; // 10ms in microseconds
            };

            $startTime = microtime(true);
            $retrier = retry(3, $backoff);
            $result = $retrier($callable);
            $endTime = microtime(true);

            expect($result)->toBe('success');
            expect($attempts)->toBe(3);
            expect($backoffCalls)->toBe([1, 2]); // Called after 1st and 2nd attempts
            // Should take at least 20ms (2 * 10ms)
            expect($endTime - $startTime)->toBeGreaterThanOrEqual(0.02);
        });

        test('backoff receives correct attempt numbers (1-indexed)', function (): void {
            $attempts = 0;
            $backoffCalls = [];

            $callable = function () use (&$attempts): void {
                ++$attempts;

                throw new Exception('Always fail');
            };

            $backoff = function (int $attempt) use (&$backoffCalls): int {
                $backoffCalls[] = $attempt;

                return 0; // No actual delay
            };

            $retrier = retry(4, $backoff);

            try {
                $retrier($callable);
            } catch (Exception) {
                // Expected
            }

            expect($backoffCalls)->toBe([1, 2, 3]); // 1-indexed, no call after last attempt
            expect($attempts)->toBe(4);
        });

        test('exponential backoff example', function (): void {
            $attempts = 0;
            $backoffDelays = [];

            $callable = function () use (&$attempts): string {
                ++$attempts;

                throw_if($attempts < 4, new Exception('Retry'));

                return 'success';
            };

            // Exponential backoff: 2^attempt * 1000 microseconds
            $backoff = function (int $attempt) use (&$backoffDelays): float|int {
                $delay = (2 ** $attempt) * 1_000;
                $backoffDelays[] = $delay;

                return $delay;
            };

            $retrier = retry(5, $backoff);
            $result = $retrier($callable);

            expect($result)->toBe('success');
            expect($attempts)->toBe(4);
            expect($backoffDelays)->toBe([2_000, 4_000, 8_000]); // 2ms, 4ms, 8ms
        });

        test('linear backoff example', function (): void {
            $attempts = 0;
            $backoffDelays = [];

            $callable = function () use (&$attempts): string {
                ++$attempts;

                throw_if($attempts < 3, new Exception('Retry'));

                return 'success';
            };

            // Linear backoff: attempt * 5000 microseconds
            $backoff = function (int $attempt) use (&$backoffDelays): int {
                $delay = $attempt * 5_000;
                $backoffDelays[] = $delay;

                return $delay;
            };

            $retrier = retry(3, $backoff);
            $result = $retrier($callable);

            expect($result)->toBe('success');
            expect($attempts)->toBe(3);
            expect($backoffDelays)->toBe([5_000, 10_000]); // 5ms, 10ms
        });

        test('constant backoff example', function (): void {
            $attempts = 0;
            $backoffDelays = [];

            $callable = function () use (&$attempts): string {
                ++$attempts;

                throw_if($attempts < 3, new Exception('Retry'));

                return 'success';
            };

            // Constant backoff: always 3000 microseconds
            $backoff = function (int $attempt) use (&$backoffDelays): int {
                $backoffDelays[] = 3_000;

                return 3_000;
            };

            $retrier = retry(3, $backoff);
            $result = $retrier($callable);

            expect($result)->toBe('success');
            expect($attempts)->toBe(3);
            expect($backoffDelays)->toBe([3_000, 3_000]); // Always 3ms
        });

        test('backoff not called after successful attempt', function (): void {
            $attempts = 0;
            $backoffCalls = [];

            $callable = function () use (&$attempts): string {
                ++$attempts;

                return 'immediate success';
            };

            $backoff = function (int $attempt) use (&$backoffCalls): int {
                $backoffCalls[] = $attempt;

                return 1_000;
            };

            $retrier = retry(3, $backoff);
            $result = $retrier($callable);

            expect($result)->toBe('immediate success');
            expect($attempts)->toBe(1);
            expect($backoffCalls)->toBe([]); // Never called on success
        });

        test('backoff not called after final failed attempt', function (): void {
            $attempts = 0;
            $backoffCalls = [];

            $callable = function () use (&$attempts): void {
                ++$attempts;

                throw new Exception('Always fail');
            };

            $backoff = function (int $attempt) use (&$backoffCalls): int {
                $backoffCalls[] = $attempt;

                return 1_000;
            };

            $retrier = retry(3, $backoff);

            try {
                $retrier($callable);
            } catch (Exception) {
                // Expected
            }

            expect($attempts)->toBe(3);
            expect($backoffCalls)->toBe([1, 2]); // Not called after 3rd (final) attempt
        });
    });
});
