<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\tail;
use function describe;
use function expect;
use function it;

describe('tail', function (): void {
    describe('Happy Path', function (): void {
        it('returns tail of array', function (): void {
            $a = [1, 2, 3];
            expect(tail($a))->toBe([2, 3]);
        });
    });

    describe('Edge Cases', function (): void {
        it('returns empty array for empty input', function (): void {
            $a = [];
            expect(tail($a))->toBe([]);
        });
    });
});
