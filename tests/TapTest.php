<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use RuntimeException;
use stdClass;

use function Cline\fp\pipe;
use function Cline\fp\tap;
use function describe;
use function expect;
use function it;

describe('tap', function (): void {
    describe('Happy Path', function (): void {
        it('executes side effect and returns original value', function (): void {
            $sideEffectValue = null;
            $sideEffect = function ($value) use (&$sideEffectValue): void {
                $sideEffectValue = $value;
            };

            $result = tap($sideEffect)(42);

            expect($sideEffectValue)->toBe(42);
            expect($result)->toBe(42);
        });

        it('preserves value flow in pipeline', function (): void {
            $sideEffectValue = null;
            $sideEffect = function ($value) use (&$sideEffectValue): void {
                $sideEffectValue = $value;
            };

            $result = pipe(
                10,
                fn ($x): int|float => $x * 2,
                tap($sideEffect),
                fn ($x): int|float => $x + 5,
            );

            expect($sideEffectValue)->toBe(20);
            expect($result)->toBe(25);
        });

        it('executes side effect with closure that modifies external variable', function (): void {
            $counter = 0;
            $sideEffect = function ($value) use (&$counter): void {
                $counter += $value;
            };

            $result = tap($sideEffect)(10);

            expect($counter)->toBe(10);
            expect($result)->toBe(10);
        });

        it('executes side effect with array value', function (): void {
            $sideEffectValue = null;
            $sideEffect = function ($value) use (&$sideEffectValue): void {
                $sideEffectValue = $value;
            };
            $array = [1, 2, 3];

            $result = tap($sideEffect)($array);

            expect($sideEffectValue)->toBe([1, 2, 3]);
            expect($result)->toBe([1, 2, 3]);
        });

        it('executes side effect with object value', function (): void {
            $sideEffectValue = null;
            $sideEffect = function ($value) use (&$sideEffectValue): void {
                $sideEffectValue = $value;
            };
            $object = new stdClass();
            $object->id = 1;
            $object->name = 'test';

            $result = tap($sideEffect)($object);

            expect($sideEffectValue)->toBe($object);
            expect($result)->toBe($object);
            expect($result->id)->toBe(1);
            expect($result->name)->toBe('test');
        });
    });

    describe('Edge Cases', function (): void {
        it('does not modify original value even if side effect tries', function (): void {
            $original = 42;
            $sideEffect = function ($value): void {
                $value = 100; // Try to modify (won't affect original)
            };

            $result = tap($sideEffect)($original);

            expect($result)->toBe(42);
            expect($original)->toBe(42);
        });

        it('handles complex nested structures', function (): void {
            $sideEffectValue = null;
            $sideEffect = function ($value) use (&$sideEffectValue): void {
                $sideEffectValue = $value;
            };
            $nested = [
                'user' => [
                    'name' => 'John',
                    'address' => [
                        'city' => 'NYC',
                        'zip' => '10001',
                    ],
                ],
                'metadata' => ['created' => '2024-01-01'],
            ];

            $result = tap($sideEffect)($nested);

            expect($sideEffectValue)->toBe($nested);
            expect($result)->toBe($nested);
            expect($result['user']['address']['city'])->toBe('NYC');
        });

        it('executes multiple tap calls in sequence', function (): void {
            $calls = [];
            $tap1 = function (int $value) use (&$calls): void {
                $calls[] = 'tap1: '.$value;
            };
            $tap2 = function (int $value) use (&$calls): void {
                $calls[] = 'tap2: '.$value;
            };
            $tap3 = function (int $value) use (&$calls): void {
                $calls[] = 'tap3: '.$value;
            };

            $result = pipe(
                5,
                tap($tap1),
                tap($tap2),
                tap($tap3),
            );

            expect($result)->toBe(5);
            expect($calls)->toBe(['tap1: 5', 'tap2: 5', 'tap3: 5']);
        });

        it('handles different value types in sequence', function (): void {
            $values = [];
            $sideEffect = function ($value) use (&$values): void {
                $values[] = $value;
            };

            // Scalar
            tap($sideEffect)(42);
            // Array
            tap($sideEffect)([1, 2, 3]);
            // Object
            $obj = new stdClass();
            $obj->test = 'value';
            tap($sideEffect)($obj);

            expect($values[0])->toBe(42);
            expect($values[1])->toBe([1, 2, 3]);
            expect($values[2])->toBe($obj);
            expect($values[2]->test)->toBe('value');
        });
    });

    describe('Sad Paths', function (): void {
        it('passes through null value', function (): void {
            $sideEffectRan = false;
            $sideEffect = function ($value) use (&$sideEffectRan): void {
                $sideEffectRan = true;
                expect($value)->toBeNull();
            };

            $result = tap($sideEffect)(null);

            expect($sideEffectRan)->toBeTrue();
            expect($result)->toBeNull();
        });

        it('throws when side effect throws', function (): void {
            $sideEffect = function ($value): void {
                throw new RuntimeException('Side effect error');
            };

            expect(fn () => tap($sideEffect)(42))
                ->toThrow(RuntimeException::class, 'Side effect error');
        });
    });
});
