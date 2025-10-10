<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\maybe;
use function describe;
use function expect;
use function it;

describe('maybe', function (): void {
    describe('Happy Path', function (): void {
        it('applies function when value is not null', function (): void {
            $fn = static fn (int $x): int => $x + 1;
            $result = maybe($fn)(1);
            expect($result)->toBe(2);
        });

        it('returns null when value is null', function (): void {
            $fn = static fn (int $x): int => $x + 1;
            $result = maybe($fn)(null);
            expect($result)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        it('handles identity function with non-null', function (): void {
            $fn = static fn ($x) => $x;
            $result = maybe($fn)('test');
            expect($result)->toBe('test');
        });

        it('handles function returning null when given non-null input', function (): void {
            $fn = static fn ($x): null => null;
            $result = maybe($fn)(1);
            expect($result)->toBeNull();
        });
    });
});
