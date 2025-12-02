<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use const PREG_SPLIT_DELIM_CAPTURE;
use const PREG_SPLIT_NO_EMPTY;

use function Cline\fp\split;
use function describe;
use function expect;
use function it;

describe('split', function (): void {
    describe('Happy Paths', function (): void {
        it('splits by regex pattern', function (): void {
            expect(split('/\s+/')('hello world  test'))->toBe(['hello', 'world', 'test']);
        });

        it('splits by character class', function (): void {
            expect(split('/[,;]/')('a,b;c'))->toBe(['a', 'b', 'c']);
        });
    });

    describe('Edge Cases', function (): void {
        it('handles pattern not found', function (): void {
            expect(split('/\d+/')('hello world'))->toBe(['hello world']);
        });

        it('splits with limit', function (): void {
            expect(split('/\s+/', 2)('hello world test'))->toBe(['hello', 'world test']);
        });

        it('splits with PREG_SPLIT_NO_EMPTY flag', function (): void {
            expect(split('/\s+/', -1, PREG_SPLIT_NO_EMPTY)('  hello   world  '))->toBe(['hello', 'world']);
        });

        it('splits with PREG_SPLIT_DELIM_CAPTURE flag', function (): void {
            expect(split('/(\s+)/', -1, PREG_SPLIT_DELIM_CAPTURE)('hello world'))->toBe(['hello', ' ', 'world']);
        });
    });
});
