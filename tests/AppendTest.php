<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\append;
use function describe;
use function expect;
use function it;

describe('append', function (): void {
    describe('Happy Path', function (): void {
        it('appends value to array', function (): void {
            $a = [1, 2, 3, 4];
            $result = append(5)($a);
            expect($result)->toBe([1, 2, 3, 4, 5]);
        });

        it('appends value with key to associative array', function (): void {
            $a = ['a' => 'A', 'b' => 'B'];
            $result = append('C', 'c')($a);
            expect($result)->toBe(['a' => 'A', 'b' => 'B', 'c' => 'C']);
        });
    });
});
