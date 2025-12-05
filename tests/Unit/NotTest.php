<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\not;
use function describe;
use function expect;
use function it;

describe('not', function (): void {
    describe('Happy Path', function (): void {
        it('negates simple predicate returning true', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $isNotPositive = not($isPositive);

            expect($isNotPositive(-5))->toBeTrue();
            expect($isNotPositive(5))->toBeFalse();
        });

        it('negates simple predicate returning false', function (): void {
            $isEmpty = static fn (array $arr): bool => $arr === [];
            $isNotEmpty = not($isEmpty);

            expect($isNotEmpty([1, 2, 3]))->toBeTrue();
            expect($isNotEmpty([]))->toBeFalse();
        });

        it('negates complex predicate', function (): void {
            $isAdult = static fn (array $person): bool => $person['age'] >= 18;
            $isMinor = not($isAdult);

            expect($isMinor(['age' => 15]))->toBeTrue();
            expect($isMinor(['age' => 21]))->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        it('negates with multiple arguments', function (): void {
            $inRange = static fn (int $x, int $min, int $max): bool => $x >= $min && $x <= $max;
            $outOfRange = not($inRange);

            expect($outOfRange(5, 1, 10))->toBeFalse();
            expect($outOfRange(15, 1, 10))->toBeTrue();
        });

        it('handles double negation', function (): void {
            $isPositive = static fn (int $x): bool => $x > 0;
            $isNotPositive = not($isPositive);
            $isPositiveAgain = not($isNotPositive);

            expect($isPositiveAgain(5))->toBeTrue();
            expect($isPositiveAgain(-5))->toBeFalse();
        });
    });
});
