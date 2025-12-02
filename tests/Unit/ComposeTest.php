<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\compose;
use function describe;
use function expect;
use function it;
use function mb_strlen;

describe('compose', function (): void {
    describe('Happy Path', function (): void {
        it('composes two functions in sequence', function (): void {
            $c = compose(
                fn (string $s): int => mb_strlen($s),
                fn (int $i): int => $i * 2,
            );
            expect($c('hello'))->toBe(10);
        });

        it('composes nested function arrays', function (): void {
            $c = compose(
                ...[
                    ...[
                        mb_strlen(...),
                        fn (int $i): int => $i * 2,
                    ],
                    fn (int $i): int => $i * 3,
                ],
            );
            expect($c('hello'))->toBe(30);
        });
    });

    describe('Edge Cases', function (): void {
        it('composes single function', function (): void {
            $c = compose(fn (int $x): int => $x * 2);
            expect($c(5))->toBe(10);
        });
    });
});
