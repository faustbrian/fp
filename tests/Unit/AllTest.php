<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\all;
use function Cline\fp\allWithKeys;
use function describe;
use function expect;
use function it;

describe('all', function (): void {
    describe('Happy Path', function (): void {
        it('returns true when all elements match', function (): void {
            $list = [2, 4, 6];
            $result = all(fn (int $x): bool => $x % 2 === 0)($list);
            expect($result)->toBeTrue();
        });

        it('returns true when all elements match with keys', function (): void {
            $list = [2, 4, 6];
            $result = allWithKeys(fn (int $x, int $k): bool => $x % 2 === 0)($list);
            expect($result)->toBeTrue();
        });
    });

    describe('Edge Cases', function (): void {
        it('returns false when not all elements match', function (): void {
            $list = [2, 3, 4];
            $result = all(fn (int $x): bool => $x % 2 === 0)($list);
            expect($result)->toBeFalse();
        });

        it('returns false when not all elements match with keys', function (): void {
            $list = [2, 3, 4];
            $result = allWithKeys(fn (int $x, int $k): bool => $x % 2 === 0)($list);
            expect($result)->toBeFalse();
        });
    });
});
