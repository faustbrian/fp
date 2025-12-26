<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\head;
use function describe;
use function expect;
use function it;

describe('head', function (): void {
    describe('Happy Path', function (): void {
        it('returns head of array', function (): void {
            $a = [1, 2, 3];
            expect(head($a))->toBe(1);
        });
    });

    describe('Edge Cases', function (): void {
        it('returns null for empty array', function (): void {
            $a = [];
            expect(head($a))->toBeNull();
        });
    });
});
