<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\nth;
use function describe;
use function expect;
use function it;

describe('nth', function (): void {
    describe('Happy Path', function (): void {
        it('gets nth iteration value', function (): void {
            $a = 0;
            $mapper = static fn (int $x): int => $x + 1;
            $result = nth(2, $a, $mapper);
            expect($result)->toBe(1);
        });
    });
});
