<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\method;
use function describe;
use function expect;
use function it;

describe('method', function (): void {
    describe('Happy Path', function (): void {
        it('calls method without arguments', function (): void {
            $o = new class()
            {
                public function do(): int
                {
                    return 1;
                }
            };
            $result = method('do')($o);
            expect($result)->toBe(1);
        });

        it('calls method with single argument', function (): void {
            $o = new class()
            {
                public function do(int $x): int
                {
                    return $x;
                }
            };
            $result = method('do', 1)($o);
            expect($result)->toBe(1);
        });

        it('calls method with multiple arguments', function (): void {
            $o = new class()
            {
                public function add(int $a, int $b): int
                {
                    return $a + $b;
                }
            };
            $result = method('add', 5, 3)($o);
            expect($result)->toBe(8);
        });
    });

    describe('Edge Cases', function (): void {
        it('calls method returning string', function (): void {
            $o = new class()
            {
                public function greet(): string
                {
                    return 'hello';
                }
            };
            $result = method('greet')($o);
            expect($result)->toBe('hello');
        });

        it('calls method returning null', function (): void {
            $o = new class()
            {
                public function nothing(): null
                {
                    return null;
                }
            };
            $result = method('nothing')($o);
            expect($result)->toBeNull();
        });
    });
});
