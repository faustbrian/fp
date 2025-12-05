<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\replace;
use function describe;
use function expect;
use function test;

describe('replace', function (): void {
    describe('Happy Paths', function (): void {
        test('replaces single character in string', function (): void {
            expect(replace('e', '3')('beep'))->toBe('b33p');
        });

        test('replaces multiple strings in string', function (): void {
            expect(replace(['hello', 'world'], ['goodbye', 'everyone'])('hello world'))->toBe('goodbye everyone');
        });

        test('replaces empty string with empty string', function (): void {
            expect(replace('', '')('beep'))->toBe('beep');
        });
    });

    describe('Edge Cases', function (): void {
        test('handles no replacement when find string not present', function (): void {
            expect(replace('x', 'y')('beep'))->toBe('beep');
        });

        test('replaces all occurrences of character', function (): void {
            expect(replace('e', '3')('cheese'))->toBe('ch33s3');
        });

        test('handles empty input string', function (): void {
            expect(replace('a', 'b')(''))->toBe('');
        });

        test('replaces with longer string', function (): void {
            expect(replace('e', 'EEE')('beep'))->toBe('bEEEEEEp');
        });
    });
});
