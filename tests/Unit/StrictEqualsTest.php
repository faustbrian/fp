<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\pipe;
use function Cline\fp\strictEquals;
use function describe;
use function expect;
use function it;

describe('strictEquals', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when values are strictly equal', function (): void {
            $strictlyEquals5 = strictEquals(5);

            expect($strictlyEquals5(5))->toBeTrue();
        });

        it('returns false for loosely equal but different types', function (): void {
            $strictlyEquals5 = strictEquals(5);

            expect($strictlyEquals5('5'))->toBeFalse(); // Strict comparison
        });

        it('enforces type matching for boolean values', function (): void {
            $strictlyEqualsTrue = strictEquals(true);

            expect($strictlyEqualsTrue(true))->toBeTrue();
            expect($strictlyEqualsTrue(1))->toBeFalse();    // 1 !== true
            expect($strictlyEqualsTrue('1'))->toBeFalse();  // '1' !== true
        });

        it('checks strict string equality', function (): void {
            $strictlyEqualsHello = strictEquals('hello');

            expect($strictlyEqualsHello('hello'))->toBeTrue();
            expect($strictlyEqualsHello('Hello'))->toBeFalse(); // Case sensitive
        });
    });

    describe('Edge Cases', function (): void {
        it('compares objects with strict equality', function (): void {
            $obj1 = (object) ['id' => 1, 'name' => 'Test'];
            $obj2 = (object) ['id' => 1, 'name' => 'Test'];
            $strictlyEqualsObj1 = strictEquals($obj1);

            // Objects are different instances
            expect($strictlyEqualsObj1($obj2))->toBeFalse();
            expect($strictlyEqualsObj1($obj1))->toBeTrue();
        });

        it('compares arrays strictly', function (): void {
            $arr1 = [1, 2, 3];
            $arr2 = [1, 2, 3];
            $strictlyEqualsArr1 = strictEquals($arr1);

            expect($strictlyEqualsArr1($arr2))->toBeTrue();
            expect($strictlyEqualsArr1(['1', '2', '3']))->toBeFalse(); // Different types
        });

        it('handles null comparisons strictly', function (): void {
            $strictlyEqualsNull = strictEquals(null);

            expect($strictlyEqualsNull(null))->toBeTrue();
            expect($strictlyEqualsNull(0))->toBeFalse();
            expect($strictlyEqualsNull(''))->toBeFalse();
            expect($strictlyEqualsNull(false))->toBeFalse();
        });

        it('works in pipe with afilter', function (): void {
            $values = [1, '1', 2, '2', 1];
            $result = pipe(
                $values,
                afilter(strictEquals(1)),
            );

            expect($result)->toBe([0 => 1, 4 => 1]); // Only integer 1, not string '1'
        });
    });
});
