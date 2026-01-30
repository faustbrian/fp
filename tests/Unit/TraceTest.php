<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use stdClass;

use function Cline\fp\pipe;
use function Cline\fp\trace;
use function describe;
use function expect;
use function it;
use function ob_get_clean;
use function ob_start;
use function range;

describe('trace', function (): void {
    describe('Happy Path', function (): void {
        it('dumps and returns integer value', function (): void {
            ob_start();
            $result = trace(42);
            $output = ob_get_clean();

            expect($result)->toBe(42);
            expect($output)->toContain('int(42)');
        });

        it('dumps and returns string value', function (): void {
            ob_start();
            $result = trace('hello');
            $output = ob_get_clean();

            expect($result)->toBe('hello');
            expect($output)->toContain('string(5) "hello"');
        });

        it('dumps and returns float value', function (): void {
            ob_start();
            $result = trace(3.14);
            $output = ob_get_clean();

            expect($result)->toBe(3.14);
            expect($output)->toContain('float(3.14)');
        });

        it('dumps and returns boolean true', function (): void {
            ob_start();
            $result = trace(true);
            $output = ob_get_clean();

            expect($result)->toBeTrue();
            expect($output)->toContain('bool(true)');
        });

        it('dumps and returns boolean false', function (): void {
            ob_start();
            $result = trace(false);
            $output = ob_get_clean();

            expect($result)->toBeFalse();
            expect($output)->toContain('bool(false)');
        });

        it('dumps and returns array value', function (): void {
            $array = [1, 2, 3];

            ob_start();
            $result = trace($array);
            $output = ob_get_clean();

            expect($result)->toBe([1, 2, 3]);
            expect($output)->toContain('array(3)');
        });

        it('dumps and returns object value', function (): void {
            $object = new stdClass();
            $object->id = 1;
            $object->name = 'test';

            ob_start();
            $result = trace($object);
            $output = ob_get_clean();

            expect($result)->toBe($object);
            expect($result->id)->toBe(1);
            expect($result->name)->toBe('test');
            expect($output)->toContain('object(stdClass)');
        });

        it('preserves value flow in pipeline', function (): void {
            ob_start();
            $result = pipe(
                10,
                fn ($x): int|float => $x * 2,
                trace(...),
                fn ($x): int|float => $x + 5,
            );
            $output = ob_get_clean();

            expect($result)->toBe(25);
            expect($output)->toContain('int(20)');
        });

        it('can be used multiple times in pipeline', function (): void {
            ob_start();
            $result = pipe(
                5,
                trace(...),
                fn ($x): int|float => $x * 2,
                trace(...),
                fn ($x): int|float => $x + 3,
                trace(...),
            );
            $output = ob_get_clean();

            expect($result)->toBe(13);
            expect($output)->toContain('int(5)');
            expect($output)->toContain('int(10)');
            expect($output)->toContain('int(13)');
        });
    });

    describe('Edge Cases', function (): void {
        it('dumps and returns null value', function (): void {
            ob_start();
            $result = trace(null);
            $output = ob_get_clean();

            expect($result)->toBeNull();
            expect($output)->toContain('NULL');
        });

        it('dumps and returns empty array', function (): void {
            ob_start();
            $result = trace([]);
            $output = ob_get_clean();

            expect($result)->toBe([]);
            expect($output)->toContain('array(0)');
        });

        it('dumps and returns empty string', function (): void {
            ob_start();
            $result = trace('');
            $output = ob_get_clean();

            expect($result)->toBe('');
            expect($output)->toContain('string(0) ""');
        });

        it('dumps and returns zero', function (): void {
            ob_start();
            $result = trace(0);
            $output = ob_get_clean();

            expect($result)->toBe(0);
            expect($output)->toContain('int(0)');
        });

        it('handles complex nested structures', function (): void {
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

            ob_start();
            $result = trace($nested);
            $output = ob_get_clean();

            expect($result)->toBe($nested);
            expect($result['user']['address']['city'])->toBe('NYC');
            expect($output)->toContain('array(2)');
            expect($output)->toContain('user');
            expect($output)->toContain('metadata');
        });

        it('handles associative arrays', function (): void {
            $assoc = ['key1' => 'value1', 'key2' => 'value2'];

            ob_start();
            $result = trace($assoc);
            $output = ob_get_clean();

            expect($result)->toBe($assoc);
            expect($output)->toContain('key1');
            expect($output)->toContain('value1');
        });

        it('handles objects with nested properties', function (): void {
            $parent = new stdClass();
            $parent->child = new stdClass();
            $parent->child->value = 'nested';

            ob_start();
            $result = trace($parent);
            $output = ob_get_clean();

            expect($result)->toBe($parent);
            expect($result->child->value)->toBe('nested');
            expect($output)->toContain('object(stdClass)');
        });

        it('handles negative numbers', function (): void {
            ob_start();
            $result = trace(-42);
            $output = ob_get_clean();

            expect($result)->toBe(-42);
            expect($output)->toContain('int(-42)');
        });

        it('handles large arrays', function (): void {
            $large = range(1, 100);

            ob_start();
            $result = trace($large);
            $output = ob_get_clean();

            expect($result)->toBe($large);
            expect($output)->toContain('array(100)');
        });

        it('handles special characters in strings', function (): void {
            $special = "Hello\nWorld\t!";

            ob_start();
            $result = trace($special);
            $output = ob_get_clean();

            expect($result)->toBe($special);
            expect($output)->toContain('string(');
        });

        it('does not modify original value', function (): void {
            $original = ['key' => 'value'];

            ob_start();
            $result = trace($original);
            ob_get_clean();

            expect($result)->toBe($original);
            expect($original)->toBe(['key' => 'value']);
        });

        it('handles multibyte strings', function (): void {
            $unicode = 'Hello 世界';

            ob_start();
            $result = trace($unicode);
            $output = ob_get_clean();

            expect($result)->toBe($unicode);
            expect($output)->toContain('string(');
        });

        it('handles numeric strings', function (): void {
            ob_start();
            $result = trace('123');
            $output = ob_get_clean();

            expect($result)->toBe('123');
            expect($output)->toContain('string(3) "123"');
        });

        it('traces same value multiple times independently', function (): void {
            $value = 42;

            ob_start();
            $result1 = trace($value);
            $output1 = ob_get_clean();

            ob_start();
            $result2 = trace($value);
            $output2 = ob_get_clean();

            expect($result1)->toBe(42);
            expect($result2)->toBe(42);
            expect($output1)->toContain('int(42)');
            expect($output2)->toContain('int(42)');
        });
    });
});
