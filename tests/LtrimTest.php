<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\ltrim;
use function describe;
use function expect;
use function it;

describe('ltrim', function (): void {
    describe('Happy Paths', function (): void {
        it('removes whitespace from left side', function (): void {
            expect(ltrim()('  hello world  '))->toBe('hello world  ');
        });

        it('removes custom characters from left side', function (): void {
            expect(ltrim('/')('/path/to/file/'))->toBe('path/to/file/');
        });
    });

    describe('Edge Cases', function (): void {
        it('handles already trimmed string', function (): void {
            expect(ltrim()('hello  '))->toBe('hello  ');
        });

        it('handles empty string', function (): void {
            expect(ltrim()(''))->toBe('');
        });

        it('removes multiple whitespace character types', function (): void {
            expect(ltrim()("\t  hello  \n"))->toBe("hello  \n");
        });
    });
});
