<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\pipe;
use function Cline\fp\unless;
use function describe;
use function expect;
use function it;

describe('unless', function (): void {
    describe('Happy Path', function (): void {
        it('executes function when condition is false', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $negate = static fn (int $x): int => -$x;
            $negateIfNotPositive = unless($isPositive, $negate);

            expect($negateIfNotPositive(-5))->toBe(5);
        });

        it('returns value unchanged when condition is true', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $negate = static fn (int $x): int => -$x;
            $negateIfNotPositive = unless($isPositive, $negate);

            expect($negateIfNotPositive(5))->toBe(5);
        });
    });

    describe('Edge Cases', function (): void {
        it('works in a pipeline', function (): void {
            $isZero = static fn (int $x): bool => $x === 0;
            $increment = static fn (int $x): int => $x + 1;

            $result = pipe(
                5,
                unless($isZero, $increment),
                static fn (int $x): int => $x * 2,
            );

            expect($result)->toBe(12);
        });

        it('handles nested unless calls', function (): void {
            $isNegative = static fn (int $x): bool => $x < 0;
            $isOdd = static fn (int $x): bool => $x % 2 !== 0;
            $negate = static fn (int $x): int => -$x;
            $double = static fn (int $x): int => $x * 2;

            $transform = unless($isNegative, unless($isOdd, $double));

            expect($transform(4))->toBe(8);   // Positive and even -> doubled
            expect($transform(3))->toBe(3);   // Positive but odd -> unchanged
            expect($transform(-4))->toBe(-4); // Negative -> unchanged
        });
    });
});
