<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;
use ArrayObject;

use function Cline\fp\afilter;
use function Cline\fp\gt;
use function Cline\fp\isNotEmpty;
use function Cline\fp\pipe;
use function Cline\fp\when;
use function count;
use function describe;
use function expect;
use function it;

describe('isNotEmpty', function (): void {
    describe('Edge Cases', function (): void {
        it('returns false for empty array', function (): void {
            expect(isNotEmpty([]))->toBeFalse();
        });

        it('returns true for non-empty array', function (): void {
            expect(isNotEmpty([1, 2, 3]))->toBeTrue();
        });

        it('returns false for empty string', function (): void {
            expect(isNotEmpty(''))->toBeFalse();
        });

        it('returns true for non-empty string', function (): void {
            expect(isNotEmpty('hello'))->toBeTrue();
        });

        it('returns false for empty Countable object', function (): void {
            $arrayObject = new ArrayObject([]);
            expect(isNotEmpty($arrayObject))->toBeFalse();
        });

        it('returns true for non-empty Countable object', function (): void {
            $arrayObject = new ArrayObject([1, 2, 3]);
            expect(isNotEmpty($arrayObject))->toBeTrue();
        });

        it('returns false for empty Traversable', function (): void {
            $iterator = new ArrayIterator([]);
            expect(isNotEmpty($iterator))->toBeFalse();
        });

        it('returns true for non-empty Traversable', function (): void {
            $iterator = new ArrayIterator([1, 2, 3]);
            expect(isNotEmpty($iterator))->toBeTrue();
        });

        it('returns false for empty generator', function (): void {
            $generator = (function (): void {})();
            expect(isNotEmpty($generator))->toBeFalse();
        });

        it('returns true for non-empty generator', function (): void {
            $generator = (function () {
                yield 1;

                yield 2;
            })();
            expect(isNotEmpty($generator))->toBeTrue();
        });

        it('returns false for null value', function (): void {
            expect(isNotEmpty(null))->toBeFalse();
        });

        it('returns false for zero', function (): void {
            expect(isNotEmpty(0))->toBeFalse();
        });

        it('returns false for false boolean', function (): void {
            expect(isNotEmpty(false))->toBeFalse();
        });

        it('returns true for string zero', function (): void {
            expect(isNotEmpty('0'))->toBeTrue();
        });

        it('filters non-empty values from array', function (): void {
            $values = ['', 'hello', [], [1], null, 'world'];
            $filtered = afilter(isNotEmpty(...))($values);

            expect($filtered)->toBe([1 => 'hello', 3 => [1], 5 => 'world']);
        });

        it('works in pipeline to validate data presence', function (): void {
            $result = pipe(
                [1, 2, 3],
                afilter(gt(1)),
                isNotEmpty(...),
            );

            expect($result)->toBeTrue(); // Filtered array has values
        });

        it('guards conditional operations', function (): void {
            $data = ['a', 'b', 'c'];
            $result = when(isNotEmpty(...), fn ($x): int => count($x))($data);

            expect($result)->toBe(3);
        });

        it('skips operations on empty collections', function (): void {
            $data = [];
            $result = when(isNotEmpty(...), fn ($x): int => count($x))($data);

            expect($result)->toBe([]);
        });
    });
});
