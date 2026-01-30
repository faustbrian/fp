<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use stdClass;

use function Cline\fp\omit;
use function describe;
use function expect;
use function property_exists;
use function test;

describe('omit', function (): void {
    describe('Happy Paths', function (): void {
        test('omits single key from array', function (): void {
            $data = ['name' => 'John', 'age' => 30, 'email' => 'john@example.com'];
            $result = omit('email')($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });

        test('omits multiple keys from array', function (): void {
            $data = ['name' => 'John', 'age' => 30, 'email' => 'john@example.com', 'phone' => '555-1234'];
            $result = omit('email', 'phone')($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });

        test('omits single property from object', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;

                public string $email = 'jane@example.com';
            };
            $result = omit('email')($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane')
                ->and($result->age)->toBe(25)
                ->and(property_exists($result, 'email'))->toBeFalse();
        });

        test('omits multiple properties from object', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;

                public string $email = 'jane@example.com';

                public string $phone = '555-5678';
            };
            $result = omit('email', 'phone')($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane')
                ->and($result->age)->toBe(25)
                ->and(property_exists($result, 'email'))->toBeFalse()
                ->and(property_exists($result, 'phone'))->toBeFalse();
        });
    });

    describe('Sad Paths', function (): void {
        test('omits non-existent key from array returns same values', function (): void {
            $data = ['name' => 'John', 'age' => 30];
            $result = omit('missing')($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });

        test('omits non-existent property from object returns same properties', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;
            };
            $result = omit('missing')($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane')
                ->and($result->age)->toBe(25);
        });

        test('omits mix of existing and non-existent keys removes only existing', function (): void {
            $data = ['name' => 'John', 'age' => 30, 'email' => 'john@example.com'];
            $result = omit('email', 'missing', 'other')($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });
    });

    describe('Edge Cases', function (): void {
        test('omits from empty array returns empty array', function (): void {
            $data = [];
            $result = omit('name', 'age')($data);
            expect($result)->toBe([]);
        });

        test('omits from empty object returns empty object', function (): void {
            $data = new stdClass();
            $result = omit('name', 'age')($data);
            expect($result)->toBeObject()
                ->and(property_exists($result, 'name'))->toBeFalse()
                ->and(property_exists($result, 'age'))->toBeFalse();
        });

        test('omits with no keys from array returns all values', function (): void {
            $data = ['name' => 'John', 'age' => 30];
            $result = omit()($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });

        test('omits with no keys from object returns all properties', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;
            };
            $result = omit()($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane')
                ->and($result->age)->toBe(25);
        });

        test('omits all keys from array returns empty array', function (): void {
            $data = ['name' => 'John', 'age' => 30];
            $result = omit('name', 'age')($data);
            expect($result)->toBe([]);
        });

        test('omits all properties from object returns empty object', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;
            };
            $result = omit('name', 'age')($data);
            expect($result)->toBeObject()
                ->and(property_exists($result, 'name'))->toBeFalse()
                ->and(property_exists($result, 'age'))->toBeFalse();
        });

        test('omits from non-array non-object returns empty array', function (): void {
            $result = omit('key')('string value');
            expect($result)->toBe([]);
        });

        test('omits from integer returns empty array', function (): void {
            $result = omit('key')(42);
            expect($result)->toBe([]);
        });

        test('omits from null returns empty array', function (): void {
            $result = omit('key')(null);
            expect($result)->toBe([]);
        });

        test('omits keys preserving falsy values', function (): void {
            $data = ['zero' => 0, 'empty' => '', 'false' => false, 'null' => null, 'remove' => 'me'];
            $result = omit('remove')($data);
            expect($result)->toBe(['zero' => 0, 'empty' => '', 'false' => false, 'null' => null]);
        });

        test('omits numeric keys from array', function (): void {
            $data = [0 => 'first', 1 => 'second', 'name' => 'value'];
            $result = omit('0', '1')($data);
            expect($result)->toBe(['name' => 'value']);
        });
    });
});
