<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use stdClass;

use function Cline\fp\pick;
use function describe;
use function expect;
use function property_exists;
use function test;

describe('pick', function (): void {
    describe('Happy Paths', function (): void {
        test('picks single key from array', function (): void {
            $data = ['name' => 'John', 'age' => 30, 'email' => 'john@example.com'];
            $result = pick('name')($data);
            expect($result)->toBe(['name' => 'John']);
        });

        test('picks multiple keys from array', function (): void {
            $data = ['name' => 'John', 'age' => 30, 'email' => 'john@example.com'];
            $result = pick('name', 'age')($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });

        test('picks single property from object', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;

                public string $email = 'jane@example.com';
            };
            $result = pick('name')($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane')
                ->and(property_exists($result, 'age'))->toBeFalse()
                ->and(property_exists($result, 'email'))->toBeFalse();
        });

        test('picks multiple properties from object', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;

                public string $email = 'jane@example.com';
            };
            $result = pick('name', 'age')($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane')
                ->and($result->age)->toBe(25)
                ->and(property_exists($result, 'email'))->toBeFalse();
        });

        test('picks all keys from array', function (): void {
            $data = ['x' => 1, 'y' => 2];
            $result = pick('x', 'y')($data);
            expect($result)->toBe(['x' => 1, 'y' => 2]);
        });
    });

    describe('Sad Paths', function (): void {
        test('picks non-existent key from array returns empty array', function (): void {
            $data = ['name' => 'John', 'age' => 30];
            $result = pick('missing')($data);
            expect($result)->toBe([]);
        });

        test('picks non-existent property from object returns empty object', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;
            };
            $result = pick('missing')($data);
            expect($result)->toBeObject()
                ->and(property_exists($result, 'name'))->toBeFalse()
                ->and(property_exists($result, 'age'))->toBeFalse()
                ->and(property_exists($result, 'missing'))->toBeFalse();
        });

        test('picks mix of existing and non-existent keys returns only existing', function (): void {
            $data = ['name' => 'John', 'age' => 30];
            $result = pick('name', 'missing', 'age', 'other')($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });
    });

    describe('Edge Cases', function (): void {
        test('picks from empty array returns empty array', function (): void {
            $data = [];
            $result = pick('name', 'age')($data);
            expect($result)->toBe([]);
        });

        test('picks from empty object returns empty object', function (): void {
            $data = new stdClass();
            $result = pick('name', 'age')($data);
            expect($result)->toBeObject()
                ->and(property_exists($result, 'name'))->toBeFalse()
                ->and(property_exists($result, 'age'))->toBeFalse();
        });

        test('picks with no keys from array returns empty array', function (): void {
            $data = ['name' => 'John', 'age' => 30];
            $result = pick()($data);
            expect($result)->toBe([]);
        });

        test('picks with no keys from object returns empty object', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;
            };
            $result = pick()($data);
            expect($result)->toBeObject()
                ->and(property_exists($result, 'name'))->toBeFalse()
                ->and(property_exists($result, 'age'))->toBeFalse();
        });

        test('picks from non-array non-object returns empty array', function (): void {
            $result = pick('name', 'age')('string value');
            expect($result)->toBe([]);
        });

        test('picks from integer returns empty array', function (): void {
            $result = pick('key')(42);
            expect($result)->toBe([]);
        });

        test('picks from null returns empty array', function (): void {
            $result = pick('key')(null);
            expect($result)->toBe([]);
        });

        test('picks keys with falsy values from array', function (): void {
            $data = ['zero' => 0, 'empty' => '', 'false' => false, 'null' => null];
            $result = pick('zero', 'empty', 'false', 'null')($data);
            expect($result)->toBe(['zero' => 0, 'empty' => '', 'false' => false, 'null' => null]);
        });

        test('picks numeric keys from array', function (): void {
            $data = [0 => 'first', 1 => 'second', 'name' => 'value'];
            $result = pick('0', '1')($data);
            expect($result)->toBe([0 => 'first', 1 => 'second']);
        });
    });
});
