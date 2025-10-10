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
use EmptyIterator;
use IteratorAggregate;
use stdClass;
use Traversable;

use function Cline\fp\afilter;
use function Cline\fp\gt;
use function Cline\fp\isEmpty;
use function Cline\fp\not;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function fclose;
use function fopen;
use function it;

describe('isEmpty', function (): void {
    describe('Edge Cases', function (): void {
        it('returns true for empty array', function (): void {
            expect(isEmpty([]))->toBeTrue();
        });

        it('returns false for non-empty array', function (): void {
            expect(isEmpty([1, 2, 3]))->toBeFalse();
        });

        it('returns true for empty string', function (): void {
            expect(isEmpty(''))->toBeTrue();
        });

        it('returns false for non-empty string', function (): void {
            expect(isEmpty('hello'))->toBeFalse();
        });

        it('returns true for empty Countable object', function (): void {
            $arrayObject = new ArrayObject([]);
            expect(isEmpty($arrayObject))->toBeTrue();
        });

        it('returns false for non-empty Countable object', function (): void {
            $arrayObject = new ArrayObject([1, 2, 3]);
            expect(isEmpty($arrayObject))->toBeFalse();
        });

        it('returns true for empty Traversable', function (): void {
            $iterator = new ArrayIterator([]);
            expect(isEmpty($iterator))->toBeTrue();
        });

        it('returns false for non-empty Traversable', function (): void {
            $iterator = new ArrayIterator([1, 2, 3]);
            expect(isEmpty($iterator))->toBeFalse();
        });

        it('returns true for custom empty Traversable without Countable', function (): void {
            $traversable = new class() implements IteratorAggregate
            {
                public function getIterator(): Traversable
                {
                    return new EmptyIterator();
                }
            };
            expect(isEmpty($traversable))->toBeTrue();
        });

        it('returns false for custom non-empty Traversable without Countable', function (): void {
            $traversable = new class() implements IteratorAggregate
            {
                public function getIterator(): Traversable
                {
                    yield 1;

                    yield 2;
                }
            };
            expect(isEmpty($traversable))->toBeFalse();
        });

        it('returns true for empty generator', function (): void {
            $generator = (function (): void {})();
            expect(isEmpty($generator))->toBeTrue();
        });

        it('returns false for non-empty generator', function (): void {
            $generator = (function () {
                yield 1;

                yield 2;
            })();
            expect(isEmpty($generator))->toBeFalse();
        });

        it('returns true for null value', function (): void {
            expect(isEmpty(null))->toBeTrue();
        });

        it('returns true for zero', function (): void {
            expect(isEmpty(0))->toBeTrue();
        });

        it('returns true for false boolean', function (): void {
            expect(isEmpty(false))->toBeTrue();
        });

        it('returns false for string zero', function (): void {
            expect(isEmpty('0'))->toBeFalse();
        });

        it('returns false for non-zero integer', function (): void {
            expect(isEmpty(42))->toBeFalse();
        });

        it('returns false for non-zero float', function (): void {
            expect(isEmpty(3.14))->toBeFalse();
        });

        it('returns false for true boolean', function (): void {
            expect(isEmpty(true))->toBeFalse();
        });

        it('returns false for stdClass object', function (): void {
            $obj = new stdClass();
            expect(isEmpty($obj))->toBeFalse();
        });

        it('returns false for resource', function (): void {
            $resource = fopen('php://memory', 'rb');
            expect(isEmpty($resource))->toBeFalse();
            fclose($resource);
        });

        it('returns false for closure', function (): void {
            $closure = fn (): string => 'test';
            expect(isEmpty($closure))->toBeFalse();
        });

        it('returns false for object with properties', function (): void {
            $obj = new stdClass();
            $obj->prop = 'value';

            expect(isEmpty($obj))->toBeFalse();
        });

        it('filters empty values from array', function (): void {
            $values = ['', 'hello', [], [1], null, 'world'];
            $filtered = afilter(not(isEmpty(...)))($values);

            expect($filtered)->toBe([1 => 'hello', 3 => [1], 5 => 'world']);
        });

        it('works in pipeline to check collection state', function (): void {
            $result = pipe(
                [1, 2, 3],
                afilter(gt(5)),
                isEmpty(...),
            );

            expect($result)->toBeTrue(); // Filtered array is empty
        });
    });
});
