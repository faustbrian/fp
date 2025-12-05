<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use AllowDynamicProperties;
use stdClass;

use function Cline\fp\set;
use function describe;
use function expect;
use function test;

describe('set', function (): void {
    describe('Happy Paths', function (): void {
        test('sets new key on array', function (): void {
            $data = ['name' => 'John'];
            $result = \set('age', 30)($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });

        test('sets new property on object', function (): void {
            $data = new #[AllowDynamicProperties()] class()
            {
                public string $name = 'Jane';
            };
            $result = \set('age', 25)($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane')
                ->and($result->age)->toBe(25);
        });

        test('overwrites existing key on array', function (): void {
            $data = ['name' => 'John', 'age' => 25];
            $result = \set('age', 30)($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30]);
        });

        test('overwrites existing property on object', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 20;
            };
            $result = \set('age', 25)($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane')
                ->and($result->age)->toBe(25);
        });

        test('sets value on empty array creates single element array', function (): void {
            $data = [];
            $result = \set('name', 'John')($data);
            expect($result)->toBe(['name' => 'John']);
        });

        test('sets value on empty object creates single property', function (): void {
            $data = new stdClass();
            $result = \set('name', 'Jane')($data);
            expect($result)->toBeObject()
                ->and($result->name)->toBe('Jane');
        });
    });

    describe('Sad Paths', function (): void {
        test('sets on non-array non-object creates new array with key-value', function (): void {
            $result = \set('key', 'value')('string');
            expect($result)->toBe(['key' => 'value']);
        });

        test('sets on integer creates new array with key-value', function (): void {
            $result = \set('key', 'value')(42);
            expect($result)->toBe(['key' => 'value']);
        });

        test('sets on null creates new array with key-value', function (): void {
            $result = \set('key', 'value')(null);
            expect($result)->toBe(['key' => 'value']);
        });
    });

    describe('Edge Cases', function (): void {
        test('original array remains unchanged after set', function (): void {
            $data = ['name' => 'John', 'age' => 25];
            $result = \set('age', 30)($data);
            expect($data)->toBe(['name' => 'John', 'age' => 25])
                ->and($result)->toBe(['name' => 'John', 'age' => 30]);
        });

        test('original object remains unchanged after set', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 20;
            };
            $originalAge = $data->age;
            $result = \set('age', 25)($data);
            expect($data->age)->toBe($originalAge)
                ->and($result->age)->toBe(25)
                ->and($result)->not->toBe($data);
        });

        test('sets falsy values on array', function (): void {
            $data = ['name' => 'John'];
            expect(\set('zero', 0)($data))->toBe(['name' => 'John', 'zero' => 0])
                ->and(\set('empty', '')($data))->toBe(['name' => 'John', 'empty' => ''])
                ->and(\set('false', false)($data))->toBe(['name' => 'John', 'false' => false])
                ->and(\set('null', null)($data))->toBe(['name' => 'John', 'null' => null]);
        });

        test('sets numeric key on array', function (): void {
            $data = ['name' => 'John'];
            $result = \set('0', 'first')($data);
            expect($result)->toBe(['name' => 'John', 0 => 'first']);
        });

        test('sets array value on array', function (): void {
            $data = ['name' => 'John'];
            $result = \set('tags', ['php', 'laravel'])($data);
            expect($result)->toBe(['name' => 'John', 'tags' => ['php', 'laravel']]);
        });

        test('sets object value on array', function (): void {
            $data = ['name' => 'John'];
            $value = new class()
            {
                public string $type = 'config';
            };
            $result = \set('config', $value)($data);
            expect($result['name'])->toBe('John')
                ->and($result['config'])->toBe($value);
        });

        test('sets new property creates independent object instance', function (): void {
            $data = new #[AllowDynamicProperties()] class()
            {
                public string $name = 'Jane';
            };
            $result1 = \set('age', 25)($data);
            $result2 = \set('age', 30)($data);
            expect($result1->age)->toBe(25)
                ->and($result2->age)->toBe(30)
                ->and($result1)->not->toBe($result2);
        });

        test('sets preserves other properties when overwriting', function (): void {
            $data = ['name' => 'John', 'age' => 25, 'email' => 'john@example.com'];
            $result = \set('age', 30)($data);
            expect($result)->toBe(['name' => 'John', 'age' => 30, 'email' => 'john@example.com']);
        });
    });
});
