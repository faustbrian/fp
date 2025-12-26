<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use SplPriorityQueue;
use SplStack;

use function Cline\fp\typeIs;
use function describe;
use function expect;
use function it;

describe('typeIs', function (): void {
    describe('Happy Path', function (): void {
        it('validates int type correctly', function (): void {
            expect(typeIs('int')(1))->toBeTrue();
        });

        it('validates string type correctly', function (): void {
            expect(typeIs('string')('1'))->toBeTrue();
        });

        it('validates float type correctly', function (): void {
            expect(typeIs('float')(1.0))->toBeTrue();
        });

        it('validates bool type correctly', function (): void {
            expect(typeIs('bool')(true))->toBeTrue();
        });

        it('validates class type correctly', function (): void {
            expect(typeIs(SplStack::class)(new SplStack()))->toBeTrue();
        });
    });

    describe('Edge Cases', function (): void {
        it('returns false for int when given string', function (): void {
            expect(typeIs('int')('1'))->toBeFalse();
        });

        it('returns false for string when given int', function (): void {
            expect(typeIs('string')(1))->toBeFalse();
        });

        it('returns false for float when given int', function (): void {
            expect(typeIs('float')(1))->toBeFalse();
        });

        it('returns false for float when given array', function (): void {
            expect(typeIs('float')([]))->toBeFalse();
        });

        it('returns false for bool when given int', function (): void {
            expect(typeIs('bool')(1))->toBeFalse();
        });

        it('returns false for wrong class type', function (): void {
            expect(typeIs(SplStack::class)(new SplPriorityQueue()))->toBeFalse();
        });
    });
});
