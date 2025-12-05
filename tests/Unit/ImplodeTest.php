<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\implode;
use function describe;
use function expect;
use function test;

describe('implode', function (): void {
    describe('Happy Paths', function (): void {
        test('joins array with empty glue', function (): void {
            expect(implode('')(['b', 'e', 'e', 'p']))->toBe('beep');
        });

        test('joins array with hyphen glue', function (): void {
            expect(implode('-')(['b', 'e', 'e', 'p']))->toBe('b-e-e-p');
        });
    });

    describe('Edge Cases', function (): void {
        test('joins empty array', function (): void {
            expect(implode('-')([]))->toBe('');
        });

        test('joins single element array', function (): void {
            expect(implode('-')(['a']))->toBe('a');
        });

        test('joins with multi-character glue', function (): void {
            expect(implode('::')(['a', 'b', 'c']))->toBe('a::b::c');
        });
    });
});
