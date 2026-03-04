<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use const PREG_OFFSET_CAPTURE;

use function Cline\fp\pregMatch;
use function describe;
use function expect;
use function it;

describe('pregMatch', function (): void {
    describe('Happy Paths', function (): void {
        it('matches pattern and returns matches array', function (): void {
            $result = pregMatch('/\d+/')('test 123 hello');
            expect($result)->toBe(['123']);
        });

        it('captures groups', function (): void {
            $result = pregMatch('/(\w+)@(\w+)\.(\w+)/')('user@example.com');
            expect($result)->toBe(['user@example.com', 'user', 'example', 'com']);
        });
    });

    describe('Edge Cases', function (): void {
        it('uses PREG_OFFSET_CAPTURE flag', function (): void {
            $result = pregMatch('/world/', PREG_OFFSET_CAPTURE)('hello world');
            expect($result)->toBe([['world', 6]]);
        });

        it('uses offset parameter', function (): void {
            $result = pregMatch('/\d+/', 0, 5)('test 123 and 456');
            expect($result)->toBe(['123']);
        });
    });

    describe('Sad Paths', function (): void {
        it('returns null when pattern does not match', function (): void {
            $result = pregMatch('/\d+/')('hello world');
            expect($result)->toBeNull();
        });
    });
});
