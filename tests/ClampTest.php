<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\amap;
use function Cline\fp\clamp;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function test;

describe('clamp', function (): void {
    describe('Happy Paths', function (): void {
        test('returns value when within range', function (): void {
            $clamp0To10 = clamp(0, 10);
            expect($clamp0To10(5))->toBe(5);
        });

        test('returns min when value below range', function (): void {
            $clamp0To10 = clamp(0, 10);
            expect($clamp0To10(-5))->toBe(0);
        });

        test('returns max when value above range', function (): void {
            $clamp0To10 = clamp(0, 10);
            expect($clamp0To10(15))->toBe(10);
        });

        test('returns min when value equals min', function (): void {
            $clamp0To10 = clamp(0, 10);
            expect($clamp0To10(0))->toBe(0);
        });

        test('returns max when value equals max', function (): void {
            $clamp0To10 = clamp(0, 10);
            expect($clamp0To10(10))->toBe(10);
        });
    });

    describe('Edge Cases', function (): void {
        test('works with negative range', function (): void {
            $clampNegative = clamp(-10, -5);
            expect($clampNegative(-7))->toBe(-7);
            expect($clampNegative(-15))->toBe(-10);
            expect($clampNegative(-3))->toBe(-5);
        });

        test('works with float range', function (): void {
            $clampFloat = clamp(0.0, 1.0);
            expect($clampFloat(0.5))->toBe(0.5);
            expect($clampFloat(-0.5))->toBe(0.0);
            expect($clampFloat(1.5))->toBe(1.0);
        });

        test('works in pipeline', function (): void {
            $values = [-5, 0, 5, 10, 15, 20];
            $result = pipe(
                $values,
                amap(clamp(0, 10)),
            );
            expect($result)->toBe([0, 0, 5, 10, 10, 10]);
        });

        test('handles single value range', function (): void {
            $clampToFive = clamp(5, 5);
            expect($clampToFive(5))->toBe(5);
            expect($clampToFive(3))->toBe(5);
            expect($clampToFive(7))->toBe(5);
        });
    });
});
