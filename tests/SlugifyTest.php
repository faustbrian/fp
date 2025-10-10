<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\slugify;
use function describe;
use function expect;
use function it;

describe('slugify', function (): void {
    describe('Happy Paths', function (): void {
        it('converts basic string to slug', function (): void {
            expect(slugify()('Hello World'))->toBe('hello-world');
        });

        it('uses custom separator', function (): void {
            expect(slugify('_')('Hello World'))->toBe('hello_world');
        });
    });

    describe('Edge Cases', function (): void {
        it('handles special characters', function (): void {
            expect(slugify()('Hello! World? #test'))->toBe('hello-world-test');
        });

        it('handles multiple spaces and hyphens', function (): void {
            expect(slugify()('Hello   World  --  Test'))->toBe('hello-world-test');
        });

        it('handles already valid slug', function (): void {
            expect(slugify()('hello-world'))->toBe('hello-world');
        });

        it('removes leading and trailing separators', function (): void {
            expect(slugify()('  Hello World  '))->toBe('hello-world');
        });

        it('handles unicode characters', function (): void {
            expect(slugify()('Café Münchën'))->toBe('caf-m-nch-n');
        });

        it('handles numbers in string', function (): void {
            expect(slugify()('Test 123 Example'))->toBe('test-123-example');
        });
    });
});
