<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Illuminate\Support\Sleep;
use Tests\Exceptions\ThrottledCallException;

use function Cline\fp\throttle;
use function count;
use function describe;
use function expect;
use function hrtime;
use function test;

describe('throttle', function (): void {
    describe('Happy Path', function (): void {
        test('limits execution to once per time period', function (): void {
            $callCount = 0;
            $fn = function (int $value) use (&$callCount): int {
                ++$callCount;

                return $value * 2;
            };

            $throttled = throttle(50_000)($fn); // 50ms throttle period

            // First call executes immediately
            $result1 = $throttled(1);
            expect($result1)->toBe(2);
            expect($callCount)->toBe(1);

            // Second call within throttle period returns cached result
            $result2 = $throttled(2);
            expect($result2)->toBe(2); // Same as first result
            expect($callCount)->toBe(1); // No additional execution

            // Wait for throttle period to pass
            Sleep::usleep(51_000);

            // Third call after throttle period executes
            $result3 = $throttled(3);
            expect($result3)->toBe(6);
            expect($callCount)->toBe(2);
        });

        test('first call always executes immediately', function (): void {
            $executed = false;
            $fn = function () use (&$executed): string {
                $executed = true;

                return 'executed';
            };

            $throttled = throttle(100_000)($fn); // 100ms throttle

            $startTime = hrtime(true);
            $result = $throttled();
            $elapsedTime = (hrtime(true) - $startTime) / 1_000;

            expect($executed)->toBeTrue();
            expect($result)->toBe('executed');
            expect($elapsedTime)->toBeLessThan(5_000); // Should be nearly instant
        });

        test('returns cached result during throttle period', function (): void {
            $callCount = 0;
            $fn = function () use (&$callCount): int {
                ++$callCount;

                return $callCount;
            };

            $throttled = throttle(30_000)($fn); // 30ms throttle

            $result1 = $throttled();
            $result2 = $throttled();
            $result3 = $throttled();

            // All calls within throttle period return same result
            expect($result1)->toBe(1);
            expect($result2)->toBe(1);
            expect($result3)->toBe(1);
            expect($callCount)->toBe(1);
        });

        test('executes with new arguments after throttle period', function (): void {
            $receivedArgs = [];
            $fn = function (...$args) use (&$receivedArgs): array {
                $receivedArgs[] = $args;

                return $args;
            };

            $throttled = throttle(20_000)($fn); // 20ms throttle

            // First call
            $result1 = $throttled('first');
            expect($result1)->toBe(['first']);

            // Second call within throttle period (ignored)
            $result2 = $throttled('second');
            expect($result2)->toBe(['first']); // Returns cached result

            // Wait for throttle period
            Sleep::usleep(21_000);

            // Third call after throttle period
            $result3 = $throttled('third');
            expect($result3)->toBe(['third']);

            expect($receivedArgs)->toBe([['first'], ['third']]);
        });

        test('works with functions returning different types', function (): void {
            // String return
            $stringFn = fn (): string => 'hello';
            $throttledString = throttle(5_000)($stringFn);
            expect($throttledString())->toBe('hello');

            // Array return
            $arrayFn = fn (): array => [1, 2, 3];
            $throttledArray = throttle(5_000)($arrayFn);
            expect($throttledArray())->toBe([1, 2, 3]);

            // Object return
            $objectFn = fn (): object => (object) ['key' => 'value'];
            $throttledObject = throttle(5_000)($objectFn);
            $result = $throttledObject();
            expect($result->key)->toBe('value');
        });
    });

    describe('Edge Cases', function (): void {
        test('works with zero microseconds throttle', function (): void {
            $callCount = 0;
            $fn = function () use (&$callCount): int {
                ++$callCount;

                return $callCount;
            };

            $throttled = throttle(0)($fn);

            // With zero throttle, every call should execute
            $result1 = $throttled();
            $result2 = $throttled();
            $result3 = $throttled();

            expect($result1)->toBe(1);
            expect($result2)->toBe(2);
            expect($result3)->toBe(3);
            expect($callCount)->toBe(3);
        });

        test('handles functions with no arguments', function (): void {
            $callCount = 0;
            $fn = function () use (&$callCount): int {
                ++$callCount;

                return 42;
            };

            $throttled = throttle(10_000)($fn);

            $result1 = $throttled();
            $result2 = $throttled(); // Within throttle period

            expect($callCount)->toBe(1);
            expect($result1)->toBe(42);
            expect($result2)->toBe(42);
        });

        test('handles functions with multiple arguments', function (): void {
            $fn = fn (int $a, int $b, int $c): int => $a + $b + $c;

            $throttled = throttle(10_000)($fn);

            $result1 = $throttled(10, 20, 30);
            $result2 = $throttled(1, 2, 3); // Different args but within throttle period

            expect($result1)->toBe(60);
            expect($result2)->toBe(60); // Returns cached result
        });

        test('handles null return values', function (): void {
            $fn = fn (): ?string => null;

            $throttled = throttle(5_000)($fn);

            $result1 = $throttled();
            $result2 = $throttled();

            expect($result1)->toBeNull();
            expect($result2)->toBeNull();
        });

        test('handles exceptions in throttled function on first call', function (): void {
            $callCount = 0;
            $fn = function () use (&$callCount): void {
                ++$callCount;

                throw ThrottledCallException::create();
            };

            $throttled = throttle(5_000)($fn);

            // First call throws exception
            expect(fn () => $throttled())
                ->toThrow(ThrottledCallException::class, 'Test exception');

            expect($callCount)->toBe(1);
        });

        test('each throttled instance maintains separate state', function (): void {
            $callCount1 = 0;
            $callCount2 = 0;

            $fn1 = function () use (&$callCount1): int {
                ++$callCount1;

                return 1;
            };

            $fn2 = function () use (&$callCount2): int {
                ++$callCount2;

                return 2;
            };

            $throttled1 = throttle(10_000)($fn1);
            $throttled2 = throttle(10_000)($fn2);

            $result1a = $throttled1();
            $result1b = $throttled1(); // Should return cached
            $result2a = $throttled2();
            $result2b = $throttled2(); // Should return cached

            expect($callCount1)->toBe(1);
            expect($callCount2)->toBe(1);
            expect($result1a)->toBe(1);
            expect($result1b)->toBe(1);
            expect($result2a)->toBe(2);
            expect($result2b)->toBe(2);
        });

        test('works with very small microsecond values', function (): void {
            $callCount = 0;
            $fn = function () use (&$callCount): int {
                ++$callCount;

                return $callCount;
            };

            $throttled = throttle(100)($fn); // 100 microseconds

            $result1 = $throttled();

            // Immediate second call should be throttled
            $result2 = $throttled();

            // Ensure sufficient delay to exceed throttle window
            Sleep::usleep(1_000);
            $result3 = $throttled();

            expect($result1)->toBe(1);
            expect($result2)->toBe(1); // Cached
            expect($result3)->toBe(2); // New execution
        });

        test('works with large microsecond values', function (): void {
            $callCount = 0;
            $fn = function () use (&$callCount): string {
                ++$callCount;

                return 'call-'.$callCount;
            };

            $throttled = throttle(100_000)($fn); // 100ms

            $result1 = $throttled();
            expect($result1)->toBe('call-1');

            // Multiple calls within throttle period
            $result2 = $throttled();
            $result3 = $throttled();

            expect($result2)->toBe('call-1');
            expect($result3)->toBe('call-1');
            expect($callCount)->toBe(1);

            // Wait for throttle period
            Sleep::usleep(101_000);

            $result4 = $throttled();
            expect($result4)->toBe('call-2');
            expect($callCount)->toBe(2);
        });

        test('preserves function context and closures', function (): void {
            $outerValue = 100;

            $fn = fn (int $x): int => $x + $outerValue;

            $throttled = throttle(5_000)($fn);

            $result1 = $throttled(50);
            $result2 = $throttled(75); // Different arg but throttled

            expect($result1)->toBe(150);
            expect($result2)->toBe(150); // Returns cached result
        });

        test('throttle period timing is accurate', function (): void {
            $executionTimes = [];
            $fn = function () use (&$executionTimes): int {
                $executionTimes[] = hrtime(true);

                return count($executionTimes);
            };

            $throttlePeriod = 50_000; // 50ms
            $throttled = throttle($throttlePeriod)($fn);

            // First execution
            $throttled();

            // Wait less than throttle period
            Sleep::usleep(30_000); // 30ms
            $throttled(); // Should be throttled

            // Wait to exceed throttle period
            Sleep::usleep(25_000); // Total 55ms > 50ms
            $throttled(); // Should execute

            expect(count($executionTimes))->toBe(2);

            if (count($executionTimes) !== 2) {
                return;
            }

            $timeDiff = ($executionTimes[1] - $executionTimes[0]) / 1_000; // Convert to microseconds
            expect($timeDiff)->toBeGreaterThanOrEqual($throttlePeriod);
        });
    });
});
