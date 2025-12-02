<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\any;
use function Cline\fp\anyWithKeys;
use function describe;
use function expect;
use function it;

describe('any', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when any element matches', function (): void {
            $list = [1, 2, 3, 5, 7, 9];
            $result = any(fn (int $x): bool => $x % 2 === 0)($list);
            expect($result)->toBeTrue();
        });

        it('returns true when any element matches with keys', function (): void {
            $list = [1, 2, 3, 5, 7, 9];
            $result = anyWithKeys(fn (int $x, int $k): bool => $x % 2 === 0)($list);
            expect($result)->toBeTrue();
        });
    });

    describe('Edge Cases', function (): void {
        it('returns false when no element matches', function (): void {
            $list = [1, 3, 5, 7, 9];
            $result = any(fn (int $x): bool => $x % 2 === 0)($list);
            expect($result)->toBeFalse();
        });

        it('returns false when no element matches with keys', function (): void {
            $list = [1, 3, 5, 7, 9];
            $result = anyWithKeys(fn (int $x, int $k): bool => $x % 2 === 0)($list);
            expect($result)->toBeFalse();
        });
    });
});
