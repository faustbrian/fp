<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\andPred;
use function Cline\fp\not;
use function Cline\fp\orPred;
use function describe;
use function expect;
use function it;

describe('andPred', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when all predicates return true', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $isLessThan100 = static fn (int $x): bool => $x < 100;

            $combined = andPred($isPositive, $isEven, $isLessThan100);

            expect($combined(10))->toBeTrue();
        });

        it('returns false when any predicate returns false', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $isLessThan100 = static fn (int $x): bool => $x < 100;

            $combined = andPred($isPositive, $isEven, $isLessThan100);

            expect($combined(11))->toBeFalse(); // Fails isEven
            expect($combined(-10))->toBeFalse(); // Fails isPositive
            expect($combined(150))->toBeFalse(); // Fails isLessThan100
        });

        it('returns true when given empty predicates list', function (): void {
            $combined = andPred();

            expect($combined(42))->toBeTrue();
            expect($combined('any value'))->toBeTrue();
        });
    });

    describe('Edge Cases', function (): void {
        it('works with single predicate', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $combined = andPred($isPositive);

            expect($combined(5))->toBeTrue();
            expect($combined(-5))->toBeFalse();
        });

        it('passes multiple arguments to each predicate', function (): void {
            $inRange = static fn (int $x, int $min, int $max): bool => $x >= $min && $x <= $max;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $combined = andPred($inRange, $isEven);

            expect($combined(10, 1, 100))->toBeTrue();  // 10 is in range and even
            expect($combined(11, 1, 100))->toBeFalse(); // 11 is in range but odd
            expect($combined(10, 20, 100))->toBeFalse(); // 10 is even but out of range
        });

        it('short-circuits on first false predicate', function (): void {
            $callCount = 0;
            $alwaysFalse = static fn (int $x): bool => false;
            $countingPredicate = static function (int $x) use (&$callCount): bool {
                ++$callCount;

                return true;
            };

            $combined = andPred($alwaysFalse, $countingPredicate);
            $combined(5);

            expect($callCount)->toBe(0); // Second predicate never called
        });

        it('combines with not function', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $combined = andPred(not($isPositive), $isEven);

            expect($combined(-10))->toBeTrue();  // Negative and even
            expect($combined(-5))->toBeFalse();  // Negative but odd
            expect($combined(10))->toBeFalse();  // Positive and even
        });
    });

    describe('andPred + orPred combinations', function (): void {
        it('nests orPred inside andPred', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $isOdd = static fn (int $x): bool => $x % 2 !== 0;
            $isLessThan50 = static fn (int $x): bool => $x < 50;

            // Positive AND (Even OR Odd) AND LessThan50
            $combined = andPred($isPositive, orPred($isEven, $isOdd), $isLessThan50);

            expect($combined(10))->toBeTrue();  // Positive, even, < 50
            expect($combined(11))->toBeTrue();  // Positive, odd, < 50
            expect($combined(-10))->toBeFalse(); // Negative
            expect($combined(100))->toBeFalse(); // >= 50
        });

        it('nests andPred inside orPred', function (): void {
            $isNegative = static fn (int $x): bool => $x < 0;
            $isPositive = static fn (int $x): bool => $x > 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;

            // Negative OR (Positive AND Even)
            $combined = orPred($isNegative, andPred($isPositive, $isEven));

            expect($combined(-5))->toBeTrue();   // Negative
            expect($combined(10))->toBeTrue();   // Positive and even
            expect($combined(5))->toBeFalse();   // Positive but odd
        });

        it('creates complex business logic with multiple nesting levels', function (): void {
            // Check if number is positive AND (even OR odd) OR (negative AND < -100)
            $isPositive = static fn (int $x): bool => $x > 0;
            $isNegative = static fn (int $x): bool => $x < 0;
            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $isOdd = static fn (int $x): bool => $x % 2 !== 0;
            $isVeryNegative = static fn (int $x): bool => $x < -100;

            // (Positive AND (Even OR Odd)) OR (Negative AND VeryNegative)
            $complex = orPred(
                andPred(
                    $isPositive,
                    orPred($isEven, $isOdd),
                ),
                andPred($isNegative, $isVeryNegative),
            );

            expect($complex(10))->toBeTrue();   // Positive and even/odd
            expect($complex(-150))->toBeTrue();  // Negative and very negative
            expect($complex(-50))->toBeFalse();  // Negative but not very negative
        });

        it('works in a filter pipeline', function (): void {
            $numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

            $isEven = static fn (int $x): bool => $x % 2 === 0;
            $isGreaterThan5 = static fn (int $x): bool => $x > 5;
            $isLessThan10 = static fn (int $x): bool => $x < 10;

            // Find numbers that are even AND (greater than 5 OR less than 10)
            $filtered = afilter(
                andPred($isEven, orPred($isGreaterThan5, $isLessThan10)),
            )($numbers);

            expect($filtered)->toBe([1 => 2, 3 => 4, 5 => 6, 7 => 8, 9 => 10, 11 => 12]);
        });
    });
});
