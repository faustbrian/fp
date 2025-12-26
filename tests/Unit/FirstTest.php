<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\first;
use function Cline\fp\firstValue;
use function Cline\fp\firstValueWithKeys;
use function Cline\fp\firstWithKeys;
use function describe;
use function expect;
use function it;

describe('first', function (): void {
    describe('Happy Path', function (): void {
        it('finds first matching element', function (): void {
            $list = [1, 2, 3, 4, 5];
            $result = first(fn (int $x): bool => $x % 2 === 0)($list);
            expect($result)->toBe(2);
        });

        it('finds first value from callback results', function (): void {
            $list = [
                new class()
                {
                    public function foo(): mixed
                    {
                        return null;
                    }
                },
                new class()
                {
                    public function foo(): int
                    {
                        return 0;
                    }
                },
                new class()
                {
                    public function foo(): int
                    {
                        return 2;
                    }
                },
                new class()
                {
                    public function foo(): int
                    {
                        return 3;
                    }
                },
            ];
            $result = firstValue(static fn (object $object): ?int => $object->foo())($list);
            expect($result)->toBe(2);
        });

        it('finds first matching element with keys', function (): void {
            $list = [1, 2, 3, 4, 5];
            $result = firstWithKeys(static fn (int $x, int $k): bool => $x % 2 === 0 && $k % 2)($list);
            expect($result)->toBe(2);
        });

        it('finds first value from callback with keys', function (): void {
            $list = [
                new class()
                {
                    public function foo(): mixed
                    {
                        return 0;
                    }
                },
                new class()
                {
                    public function foo(): int
                    {
                        return -1;
                    }
                },
                new class()
                {
                    public function foo(): int
                    {
                        return 2;
                    }
                },
                new class()
                {
                    public function foo(): int
                    {
                        return 3;
                    }
                },
            ];
            $result = firstValueWithKeys(static fn (object $object, $key): int => $key + $object->foo())($list);
            expect($result)->toBe(4);
        });
    });

    describe('Edge Cases', function (): void {
        it('returns null when no match found', function (): void {
            $list = [1, 3, 5, 7, 9];
            $result = first(fn (int $x): bool => $x % 2 === 0)($list);
            expect($result)->toBeNull();
        });

        it('returns null when no match found with keys', function (): void {
            $list = [1, 3, 5, 7, 9];
            $result = firstWithKeys(fn (int $x, int $k): bool => $x % 2 === 0)($list);
            expect($result)->toBeNull();
        });

        it('returns null when no truthy value found', function (): void {
            $list = [0, false, null, ''];
            $result = firstValue(fn (mixed $x): mixed => $x)($list);
            expect($result)->toBeNull();
        });

        it('returns null when no truthy value found with keys', function (): void {
            $list = [0, false, null, ''];
            $result = firstValueWithKeys(fn (mixed $x, int $k): mixed => $x)($list);
            expect($result)->toBeNull();
        });
    });
});
