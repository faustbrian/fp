<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\pipe;
use function Cline\fp\when;
use function describe;
use function expect;
use function it;

describe('when', function (): void {
    describe('Happy Path', function (): void {
        it('executes function when condition is true', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $double = static fn (int $x): int => $x * 2;
            $doubleIfPositive = when($isPositive, $double);

            expect($doubleIfPositive(5))->toBe(10);
        });

        it('returns value unchanged when condition is false', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $double = static fn (int $x): int => $x * 2;
            $doubleIfPositive = when($isPositive, $double);

            expect($doubleIfPositive(-5))->toBe(-5);
        });
    });

    describe('Edge Cases', function (): void {
        it('works in a pipeline', function (): void {
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $double = static fn (int $x): int => $x * 2;

            $result = pipe(
                10,
                when($isEven, $double),
                static fn (int $x): int => $x + 5,
            );

            expect($result)->toBe(25);
        });

        it('handles nested when calls', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $double = static fn (int $x): int => $x * 2;
            $triple = static fn (int $x): int => $x * 3;

            $transform = when($isPositive, when($isEven, $double));

            expect($transform(4))->toBe(8);   // Positive and even -> doubled
            expect($transform(3))->toBe(3);   // Positive but odd -> unchanged
            expect($transform(-4))->toBe(-4); // Negative -> unchanged
        });
    });
});
