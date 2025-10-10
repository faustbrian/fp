<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\not;
use function Cline\fp\orPred;
use function describe;
use function expect;
use function it;

describe('orPred', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when any predicate returns true', function (): void {
            $isNegative = static fn (int $x): bool => $x < 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $isGreaterThan100 = static fn (int $x): bool => $x > 100;

            $combined = orPred($isNegative, $isEven, $isGreaterThan100);

            expect($combined(-5))->toBeTrue();   // Matches isNegative
            expect($combined(10))->toBeTrue();   // Matches isEven
            expect($combined(150))->toBeTrue();  // Matches isGreaterThan100
        });

        it('returns false when all predicates return false', function (): void {
            $isNegative = static fn (int $x): bool => $x < 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $isGreaterThan100 = static fn (int $x): bool => $x > 100;

            $combined = orPred($isNegative, $isEven, $isGreaterThan100);

            expect($combined(7))->toBeFalse(); // Positive, odd, less than 100
        });

        it('returns false when given empty predicates list', function (): void {
            $combined = orPred();

            expect($combined(42))->toBeFalse();
            expect($combined('any value'))->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        it('works with single predicate', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $combined = orPred($isPositive);

            expect($combined(5))->toBeTrue();
            expect($combined(-5))->toBeFalse();
        });

        it('passes multiple arguments to each predicate', function (): void {
            $outOfRange = static fn (int $x, int $min, int $max): bool => $x < $min || $x > $max;
            $isOdd = static fn (int $x): bool => $x % 2 !== 0;
            $combined = orPred($outOfRange, $isOdd);

            expect($combined(11, 1, 100))->toBeTrue();  // 11 is in range but odd
            expect($combined(150, 1, 100))->toBeTrue(); // 150 is out of range
            expect($combined(10, 1, 100))->toBeFalse(); // 10 is in range and even
        });

        it('short-circuits on first true predicate', function (): void {
            $callCount = 0;
            $alwaysTrue = static fn (int $x): bool => true;
            $countingPredicate = static function (int $x) use (&$callCount): bool {
                ++$callCount;

                return false;
            };

            $combined = orPred($alwaysTrue, $countingPredicate);
            $combined(5);

            expect($callCount)->toBe(0); // Second predicate never called
        });

        it('combines with not function', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $combined = orPred(not($isPositive), $isEven);

            expect($combined(-5))->toBeTrue();  // Negative (not positive)
            expect($combined(10))->toBeTrue();  // Even
            expect($combined(5))->toBeFalse();  // Positive and odd
        });
    });
});
