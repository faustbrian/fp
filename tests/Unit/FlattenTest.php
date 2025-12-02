<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\flatten;
use function describe;
use function expect;
use function it;

describe('flatten', function (): void {
    describe('Happy Path', function (): void {
        it('flattens nested arrays', function (): void {
            $a = [1, 2, [3, 4], [5, [6, 7]]];
            $result = flatten($a);
            expect($result)->toBe([1, 2, 3, 4, 5, 6, 7]);
        });
    });

    describe('Edge Cases', function (): void {
        it('flattens already flat array', function (): void {
            $a = [1, 2, 3, 4];
            $result = flatten($a);
            expect($result)->toBe([1, 2, 3, 4]);
        });

        it('flattens empty array', function (): void {
            $a = [];
            $result = flatten($a);
            expect($result)->toBe([]);
        });
    });
});
