<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\indexBy;
use function describe;
use function expect;
use function it;

describe('indexBy', function (): void {
    describe('Happy Path', function (): void {
        it('indexes records by custom key', function (): void {
            $in = [
                ['Jean-Luc', 'Picard'],
                ['James', 'Kirk'],
                ['Benjamin', 'Sisko'],
            ];
            $result = indexBy(fn (array $record) => $record[0])($in);
            expect($result)->toBe([
                'Jean-Luc' => ['Jean-Luc', 'Picard'],
                'James' => ['James', 'Kirk'],
                'Benjamin' => ['Benjamin', 'Sisko'],
            ]);
        });
    });
});
