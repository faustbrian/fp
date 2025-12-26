<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;
use stdClass;

use function Cline\fp\compact;
use function describe;
use function expect;
use function test;

describe('compact', function (): void {
    describe('Happy Paths', function (): void {
        test('removes null values from array', function (): void {
            $result = compact()([1, null, 2, null, 3]);
            expect($result)->toBe([0 => 1, 2 => 2, 4 => 3]);
        });

        test('removes false values from array', function (): void {
            $result = compact()([1, false, 2, false, 3]);
            expect($result)->toBe([0 => 1, 2 => 2, 4 => 3]);
        });

        test('removes both null and false values', function (): void {
            $result = compact()([1, null, 2, false, 3]);
            expect($result)->toBe([0 => 1, 2 => 2, 4 => 3]);
        });

        test('preserves array keys', function (): void {
            $result = compact()(['a' => 1, 'b' => null, 'c' => 2, 'd' => false, 'e' => 3]);
            expect($result)->toBe(['a' => 1, 'c' => 2, 'e' => 3]);
        });

        test('returns empty array when input is empty', function (): void {
            $result = compact()([]);
            expect($result)->toBe([]);
        });
    });

    describe('Sad Paths', function (): void {
        test('returns empty array when all values are null', function (): void {
            $result = compact()([null, null, null]);
            expect($result)->toBe([]);
        });

        test('returns empty array when all values are false', function (): void {
            $result = compact()([false, false, false]);
            expect($result)->toBe([]);
        });

        test('returns empty array when all values are null or false', function (): void {
            $result = compact()([null, false, null, false]);
            expect($result)->toBe([]);
        });
    });

    describe('Edge Cases', function (): void {
        test('keeps zero values', function (): void {
            $result = compact()([0, null, 0, false, 0]);
            expect($result)->toBe([0 => 0, 2 => 0, 4 => 0]);
        });

        test('keeps empty string values', function (): void {
            $result = compact()(['', null, '', false, '']);
            expect($result)->toBe([0 => '', 2 => '', 4 => '']);
        });

        test('keeps empty array values', function (): void {
            $result = compact()([[], null, [], false, []]);
            expect($result)->toBe([0 => [], 2 => [], 4 => []]);
        });

        test('keeps true values', function (): void {
            $result = compact()([true, null, true, false, true]);
            expect($result)->toBe([0 => true, 2 => true, 4 => true]);
        });

        test('handles mixed types with null and false scattered', function (): void {
            $result = compact()([
                'a' => 'string',
                'b' => null,
                'c' => 0,
                'd' => false,
                'e' => '',
                'f' => [],
                'g' => true,
                'h' => null,
                'i' => 1,
                'j' => false,
            ]);
            expect($result)->toBe([
                'a' => 'string',
                'c' => 0,
                'e' => '',
                'f' => [],
                'g' => true,
                'i' => 1,
            ]);
        });

        test('handles iterator input', function (): void {
            $iterator = new ArrayIterator([1, null, 2, false, 3, null]);
            $result = compact()($iterator);
            expect($result)->toBe([0 => 1, 2 => 2, 4 => 3]);
        });

        test('handles generator input', function (): void {
            $generator = function () {
                yield 'a' => 1;

                yield 'b' => null;

                yield 'c' => 2;

                yield 'd' => false;

                yield 'e' => 3;
            };
            $result = compact()($generator());
            expect($result)->toBe(['a' => 1, 'c' => 2, 'e' => 3]);
        });

        test('preserves numeric keys with gaps', function (): void {
            $result = compact()([10 => 'a', 20 => null, 30 => 'b', 40 => false, 50 => 'c']);
            expect($result)->toBe([10 => 'a', 30 => 'b', 50 => 'c']);
        });

        test('handles array with only falsy values keeping non-null and non-false', function (): void {
            $result = compact()([0, '', [], false, null]);
            expect($result)->toBe([0 => 0, 1 => '', 2 => []]);
        });

        test('handles nested arrays and objects', function (): void {
            $obj = new stdClass();
            $obj->value = 'test';

            $result = compact()([
                'nested_array' => [1, 2, 3],
                'null_value' => null,
                'object' => $obj,
                'false_value' => false,
                'empty_nested' => [],
            ]);

            expect($result)->toBe([
                'nested_array' => [1, 2, 3],
                'object' => $obj,
                'empty_nested' => [],
            ]);
        });
    });
});
