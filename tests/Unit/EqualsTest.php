<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\equals;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function it;

describe('equals', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when values are strictly equal', function (): void {
            $equalsTo5 = equals(5);

            expect($equalsTo5(5))->toBeTrue();
            expect($equalsTo5('5'))->toBeFalse(); // Strict comparison
        });

        it('returns false when values are not equal', function (): void {
            $equalsTo5 = equals(5);

            expect($equalsTo5(10))->toBeFalse();
            expect($equalsTo5('hello'))->toBeFalse();
        });

        it('does not perform type coercion', function (): void {
            $equalsToTrue = equals(true);

            expect($equalsToTrue(1))->toBeFalse();  // 1 !== true
            expect($equalsToTrue('1'))->toBeFalse(); // '1' !== true
            expect($equalsToTrue(true))->toBeTrue();
        });

        it('checks string equality', function (): void {
            $equalsToHello = equals('hello');

            expect($equalsToHello('hello'))->toBeTrue();
            expect($equalsToHello('world'))->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        it('compares objects with strict equality', function (): void {
            $obj1 = (object) ['id' => 1, 'name' => 'Test'];
            $obj2 = (object) ['id' => 1, 'name' => 'Test'];
            $obj3 = (object) ['id' => 2, 'name' => 'Other'];
            $equalsToObj1 = equals($obj1);

            // Objects are only strictly equal if they are the same instance
            expect($equalsToObj1($obj2))->toBeFalse(); // Different instances
            expect($equalsToObj1($obj1))->toBeTrue();  // Same instance
            expect($equalsToObj1($obj3))->toBeFalse();
        });

        it('compares arrays', function (): void {
            $arr1 = [1, 2, 3];
            $arr2 = [1, 2, 3];
            $equalsToArr1 = equals($arr1);

            expect($equalsToArr1($arr2))->toBeTrue();
            expect($equalsToArr1([3, 2, 1]))->toBeFalse(); // Different order
        });

        it('handles null comparisons strictly', function (): void {
            $equalsToNull = equals(null);

            expect($equalsToNull(null))->toBeTrue();
            expect($equalsToNull(0))->toBeFalse();     // 0 !== null
            expect($equalsToNull(''))->toBeFalse();    // '' !== null
            expect($equalsToNull(false))->toBeFalse(); // false !== null
            expect($equalsToNull(1))->toBeFalse();
            expect($equalsToNull('test'))->toBeFalse();
        });

        it('works in pipe with afilter', function (): void {
            $values = [1, 2, 3, 2, 4, 2, 5];
            $result = pipe(
                $values,
                afilter(equals(2)),
            );

            expect($result)->toBe([1 => 2, 3 => 2, 5 => 2]);
        });
    });
});
