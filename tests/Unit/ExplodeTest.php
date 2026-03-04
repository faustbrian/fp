<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\explode;
use function describe;
use function expect;
use function it;

describe('explode', function (): void {
    describe('Happy Paths', function (): void {
        it('splits string by delimiter', function (): void {
            expect(explode('-')('b-e-e-p'))->toBe(['b', 'e', 'e', 'p']);
        });
    });

    describe('Edge Cases', function (): void {
        it('splits empty string', function (): void {
            expect(explode('-')(''))->toBe(['']);
        });

        it('splits string without delimiter present', function (): void {
            expect(explode('-')('beep'))->toBe(['beep']);
        });

        it('splits with multi-character delimiter', function (): void {
            expect(explode('::')('a::b::c'))->toBe(['a', 'b', 'c']);
        });

        it('splits string ending with delimiter', function (): void {
            expect(explode('-')('a-b-c-'))->toBe(['a', 'b', 'c', '']);
        });

        it('splits string starting with delimiter', function (): void {
            expect(explode('-')('-a-b-c'))->toBe(['', 'a', 'b', 'c']);
        });
    });
});
