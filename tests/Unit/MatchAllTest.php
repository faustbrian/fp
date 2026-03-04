<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

use function Cline\fp\matchAll;
use function describe;
use function expect;
use function it;

describe('matchAll', function (): void {
    describe('Happy Paths', function (): void {
        it('matches all occurrences of pattern', function (): void {
            $result = matchAll('/\d+/')('test 123 and 456');
            expect($result)->toBe([['123', '456']]);
        });

        it('captures all groups', function (): void {
            $result = matchAll('/(\w+):(\d+)/')('port:80 host:443');
            expect($result)->toBe([
                ['port:80', 'host:443'],
                ['port', 'host'],
                ['80', '443'],
            ]);
        });
    });

    describe('Edge Cases', function (): void {
        it('uses PREG_SET_ORDER flag', function (): void {
            $result = matchAll('/(\w+):(\d+)/', PREG_SET_ORDER)('port:80 host:443');
            expect($result)->toBe([
                ['port:80', 'port', '80'],
                ['host:443', 'host', '443'],
            ]);
        });

        it('uses PREG_OFFSET_CAPTURE flag', function (): void {
            $result = matchAll('/\d+/', PREG_OFFSET_CAPTURE)('test 123');
            expect($result)->toBe([[['123', 5]]]);
        });
    });

    describe('Sad Paths', function (): void {
        it('returns empty array when pattern does not match', function (): void {
            $result = matchAll('/\d+/')('hello world');
            expect($result[0])->toBe([]);
        });
    });
});
