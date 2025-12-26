<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Tests\Exceptions\DebouncedCallException;

use function Cline\fp\debounce;
use function describe;
use function expect;
use function hrtime;
use function test;

describe('debounce', function (): void {
    describe('Happy Path', function (): void {
        test('delays execution by specified microseconds', function (): void {
            $callCount = 0;
            $lastValue = null;

            $fn = function (int $value) use (&$callCount, &$lastValue): int {
                ++$callCount;
                $lastValue = $value;

                return $value * 2;
            };

            // Create debounced function with 50ms delay
            $debounced = debounce(50_000)($fn);

            // Each call will wait before executing
            $startTime = hrtime(true);
            $result = $debounced(4);
            $elapsedTime = (hrtime(true) - $startTime) / 1_000;

            // Should have waited at least 50ms and executed once
            expect($callCount)->toBe(1);
            expect($lastValue)->toBe(4);
            expect($result)->toBe(8);
            expect($elapsedTime)->toBeGreaterThanOrEqual(50_000);
        });

        test('executes function after delay period', function (): void {
            $executed = false;
            $fn = function () use (&$executed): string {
                $executed = true;

                return 'executed';
            };

            $debounced = debounce(10_000)($fn); // 10ms delay

            $startTime = hrtime(true);
            $result = $debounced();
            $elapsedTime = (hrtime(true) - $startTime) / 1_000; // Convert to microseconds

            expect($executed)->toBeTrue();
            expect($result)->toBe('executed');
            expect($elapsedTime)->toBeGreaterThanOrEqual(10_000);
        });

        test('executes with provided arguments after delay', function (): void {
            $receivedArgs = null;
            $fn = function (...$args) use (&$receivedArgs): array {
                $receivedArgs = $args;

                return $args;
            };

            $debounced = debounce(20_000)($fn); // 20ms delay

            // Call with specific arguments
            $result = $debounced('third', 'fourth');

            // Should execute with the provided arguments after delay
            expect($receivedArgs)->toBe(['third', 'fourth']);
            expect($result)->toBe(['third', 'fourth']);
        });

        test('returns result of debounced function', function (): void {
            $fn = fn (int $x, int $y): int => $x + $y;

            $debounced = debounce(10_000)($fn);

            $result = $debounced(5, 10);

            expect($result)->toBe(15);
        });

        test('works with functions returning different types', function (): void {
            // String return
            $stringFn = fn (): string => 'hello';
            $debouncedString = debounce(5_000)($stringFn);
            expect($debouncedString())->toBe('hello');

            // Array return
            $arrayFn = fn (): array => [1, 2, 3];
            $debouncedArray = debounce(5_000)($arrayFn);
            expect($debouncedArray())->toBe([1, 2, 3]);

            // Object return
            $objectFn = fn (): object => (object) ['key' => 'value'];
            $debouncedObject = debounce(5_000)($objectFn);
            $result = $debouncedObject();
            expect($result->key)->toBe('value');
        });
    });

    describe('Edge Cases', function (): void {
        test('works with zero microseconds delay', function (): void {
            $executed = false;
            $fn = function () use (&$executed): string {
                $executed = true;

                return 'immediate';
            };

            $debounced = debounce(0)($fn);
            $result = $debounced();

            expect($executed)->toBeTrue();
            expect($result)->toBe('immediate');
        });

        test('handles functions with no arguments', function (): void {
            $callCount = 0;
            $fn = function () use (&$callCount): int {
                ++$callCount;

                return 42;
            };

            $debounced = debounce(10_000)($fn);

            $result = $debounced();

            expect($callCount)->toBe(1);
            expect($result)->toBe(42);
        });

        test('handles functions with multiple arguments', function (): void {
            $fn = fn (int $a, int $b, int $c): int => $a + $b + $c;

            $debounced = debounce(10_000)($fn);

            $result = $debounced(10, 20, 30);

            expect($result)->toBe(60);
        });

        test('handles null return values', function (): void {
            $fn = fn (): ?string => null;

            $debounced = debounce(5_000)($fn);

            $result = $debounced();

            expect($result)->toBeNull();
        });

        test('handles exceptions in debounced function', function (): void {
            $fn = function (): void {
                throw DebouncedCallException::create();
            };

            $debounced = debounce(5_000)($fn);

            expect(fn () => $debounced())
                ->toThrow(DebouncedCallException::class, 'Test exception');
        });

        test('each debounced instance maintains separate state', function (): void {
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

            $debounced1 = debounce(10_000)($fn1);
            $debounced2 = debounce(10_000)($fn2);

            $result1 = $debounced1();
            $result2 = $debounced2();

            expect($callCount1)->toBe(1);
            expect($callCount2)->toBe(1);
            expect($result1)->toBe(1);
            expect($result2)->toBe(2);
        });

        test('works with very small microsecond values', function (): void {
            $fn = fn (): string => 'quick';

            $debounced = debounce(1)($fn); // 1 microsecond

            $result = $debounced();

            expect($result)->toBe('quick');
        });

        test('works with large microsecond values', function (): void {
            $fn = fn (): string => 'slow';

            $debounced = debounce(100_000)($fn); // 100ms

            $startTime = hrtime(true);
            $result = $debounced();
            $elapsedTime = (hrtime(true) - $startTime) / 1_000;

            expect($result)->toBe('slow');
            expect($elapsedTime)->toBeGreaterThanOrEqual(100_000);
        });

        test('preserves function context and closures', function (): void {
            $outerValue = 100;

            $fn = fn (int $x): int => $x + $outerValue;

            $debounced = debounce(5_000)($fn);

            $result = $debounced(50);

            expect($result)->toBe(150);
        });
    });
});
