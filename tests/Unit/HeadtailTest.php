<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;

use function Cline\fp\headtail;
use function describe;
use function expect;
use function it;

describe('headtail', function (): void {
    describe('Happy Path', function (): void {
        it('processes headtail with callbacks', function (): void {
            $a = [1, 2, 3];
            $first = static fn (int $count, int $val): int => $count - $val;
            $rest = static fn (int $count, int $val): int => $count + $val;
            $result = headtail(5, $first, $rest)($a);
            expect($result)->toBe(9);
        });

        it('processes headtail with iterable', function (): void {
            $a = (function () {
                yield from [1, 2, 3];
            })();
            $first = static fn (int $count, int $val): int => $count - $val;
            $rest = static fn (int $count, int $val): int => $count + $val;
            $result = headtail(5, $first, $rest)($a);
            expect($result)->toBe(9);
        });
    });

    describe('Edge Cases', function (): void {
        it('returns initial value for empty array', function (): void {
            $a = [];
            $first = static fn (int $count, int $val): int => $count - $val;
            $rest = static fn (int $count, int $val): int => $count + $val;
            $result = headtail(5, $first, $rest)($a);
            expect($result)->toBe(5);
        });

        it('returns initial value for empty iterable', function (): void {
            $a = new ArrayIterator([]);
            $first = static fn (int $count, int $val): int => $count - $val;
            $rest = static fn (int $count, int $val): int => $count + $val;
            $result = headtail(5, $first, $rest)($a);
            expect($result)->toBe(5);
        });
    });
});
