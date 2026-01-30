<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\collect;
use function describe;
use function expect;
use function it;

describe('collect', function (): void {
    describe('Happy Path', function (): void {
        it('collects array input', function (): void {
            $result = collect([1, 2, 3]);
            expect($result)->toBe([1, 2, 3]);
        });

        it('collects iterator input', function (): void {
            $it = static function () {
                yield 1;

                yield 2;

                yield 3;
            };
            $result = collect($it());
            expect($result)->toBe([1, 2, 3]);
        });
    });
});
