<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\afilter;
use function Cline\fp\amap;
use function Cline\fp\pipe;
use function Cline\fp\reduce;
use function describe;
use function expect;
use function it;
use function mb_strlen;

describe('pipe', function (): void {
    describe('Happy Path', function (): void {
        it('pipes value through multiple functions', function (): void {
            $result = pipe(
                'hello',
                fn (string $s): int => mb_strlen($s),
                fn (int $i): int => $i * 2,
                fn (int $i): int => $i * 3,
            );
            expect($result)->toBe(30);
        });

        it('pipes value through spread function array', function (): void {
            $result = pipe(
                'hello',
                ...[
                    fn (string $s): int => mb_strlen($s),
                    fn (int $i): int => $i * 2,
                    fn (int $i): int => $i * 3,
                ],
            );
            expect($result)->toBe(30);
        });

        it('pipes array through functional transformations', function (): void {
            $result = pipe(
                ['hello', 'wide', 'world', 'out', 'there'],
                amap(fn (string $s): int => mb_strlen($s)),
                afilter(fn (int $x): bool => (bool) ($x % 2)),
                reduce(0, fn (int $collect, int $x): int => $x + $collect),
            );
            expect($result)->toBe(18);
        });
    });

    describe('Edge Cases', function (): void {
        it('pipes value through single function', function (): void {
            $result = pipe('hello', fn (string $s): int => mb_strlen($s));
            expect($result)->toBe(5);
        });

        it('pipes value with no transformations', function (): void {
            $result = pipe('hello');
            expect($result)->toBe('hello');
        });
    });
});
