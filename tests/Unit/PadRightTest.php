<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\padRight;
use function describe;
use function expect;
use function it;

describe('padRight', function (): void {
    describe('Happy Paths', function (): void {
        it('pads string to length with spaces', function (): void {
            expect(padRight(10)('test'))->toBe('test      ');
        });

        it('pads string with custom pad string', function (): void {
            expect(padRight(10, '0')('42'))->toBe('4200000000');
        });
    });

    describe('Edge Cases', function (): void {
        it('handles string already longer than length', function (): void {
            expect(padRight(5)('testing'))->toBe('testing');
        });

        it('handles empty string', function (): void {
            expect(padRight(5)(''))->toBe('     ');
        });

        it('pads with multi-character pad string', function (): void {
            expect(padRight(10, '-=')('test'))->toBe('test-=-=-=');
        });
    });
});
