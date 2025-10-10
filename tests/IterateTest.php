<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\iterate;
use function Cline\fp\range;
use function describe;
use function expect;
use function it;

describe('iterate', function (): void {
    describe('Happy Path', function (): void {
        it('generates infinite sequence', function (): void {
            $a = 0;
            $mapper = static fn (int $x): int => $x + 1;
            $iterable = iterate($a, $mapper);

            $result = [];

            for ($i = 0; $i < 10; ++$i) {
                $result[] = $iterable->current();
                $iterable->next();
            }

            expect($result)->toBe(range(0, 9));
        });
    });
});
