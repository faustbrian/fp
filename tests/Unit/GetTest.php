<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\get;
use function describe;
use function expect;
use function test;

describe('get', function (): void {
    describe('Happy Paths', function (): void {
        test('gets value from array with existing key', function (): void {
            $data = ['name' => 'John', 'age' => 30];
            $result = get('name')($data);
            expect($result)->toBe('John');
        });

        test('gets value from object with existing property', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';

                public int $age = 25;
            };
            $result = get('name')($data);
            expect($result)->toBe('Jane');
        });

        test('returns default value when key is missing', function (): void {
            $data = ['name' => 'John'];
            $result = get('age', 18)($data);
            expect($result)->toBe(18);
        });

        test('gets numeric key from array', function (): void {
            $data = [0 => 'first', 1 => 'second'];
            $result = get('0')($data);
            expect($result)->toBe('first');
        });
    });

    describe('Sad Paths', function (): void {
        test('returns default when getting from array with missing key', function (): void {
            $data = ['name' => 'John'];
            $result = get('email', 'default@example.com')($data);
            expect($result)->toBe('default@example.com');
        });

        test('returns default when getting from object with missing property', function (): void {
            $data = new class()
            {
                public string $name = 'Jane';
            };
            $result = get('email', 'default@example.com')($data);
            expect($result)->toBe('default@example.com');
        });

        test('returns default when getting from non-array non-object', function (): void {
            $result = get('key', 'fallback')('string value');
            expect($result)->toBe('fallback');
        });

        test('returns default when getting from integer', function (): void {
            $result = get('key', 'fallback')(42);
            expect($result)->toBe('fallback');
        });
    });

    describe('Edge Cases', function (): void {
        test('returns null as default value when key is missing', function (): void {
            $data = ['name' => 'John'];
            $result = get('age')($data);
            expect($result)->toBeNull();
        });

        test('returns null when no default specified and key missing', function (): void {
            $data = ['name' => 'John'];
            $result = get('age')($data);
            expect($result)->toBeNull();
        });

        test('gets zero value from array', function (): void {
            $data = ['count' => 0];
            $result = get('count')($data);
            expect($result)->toBe(0);
        });

        test('gets empty string from array', function (): void {
            $data = ['name' => ''];
            $result = get('name')($data);
            expect($result)->toBe('');
        });

        test('gets false value from array', function (): void {
            $data = ['enabled' => false];
            $result = get('enabled')($data);
            expect($result)->toBeFalse();
        });

        test('gets null value that exists in array uses null coalescing behavior', function (): void {
            // Note: PHP's ?? operator treats null as "not set", so default is returned
            $data = ['deletedAt' => null];
            $result = get('deletedAt', 'default')($data);
            expect($result)->toBe('default');
        });

        test('returns array as default value', function (): void {
            $data = ['name' => 'John'];
            $default = ['default' => 'value'];
            $result = get('tags', $default)($data);
            expect($result)->toBe($default);
        });

        test('returns object as default value', function (): void {
            $data = ['name' => 'John'];
            $default = new class()
            {
                public string $type = 'default';
            };
            $result = get('config', $default)($data);
            expect($result)->toBe($default);
        });
    });
});
