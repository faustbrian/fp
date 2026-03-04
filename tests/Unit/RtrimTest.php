<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\rtrim;
use function describe;
use function expect;
use function it;

describe('rtrim', function (): void {
    describe('Happy Paths', function (): void {
        it('removes whitespace from right side', function (): void {
            expect(rtrim()('  hello world  '))->toBe('  hello world');
        });

        it('removes custom characters from right side', function (): void {
            expect(rtrim('/')('/path/to/file/'))->toBe('/path/to/file');
        });
    });

    describe('Edge Cases', function (): void {
        it('handles already trimmed string', function (): void {
            expect(rtrim()('  hello'))->toBe('  hello');
        });

        it('handles empty string', function (): void {
            expect(rtrim()(''))->toBe('');
        });

        it('removes multiple whitespace character types', function (): void {
            expect(rtrim()("\t  hello  \n"))->toBe("\t  hello");
        });
    });
});
