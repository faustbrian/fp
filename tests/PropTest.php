<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use Exception;

use const E_WARNING;

use function Cline\fp\prop;
use function describe;
use function expect;
use function it;
use function restore_error_handler;
use function set_error_handler;

describe('prop', function (): void {
    describe('Happy Path', function (): void {
        it('reads existing property from object', function (): void {
            $o = new class()
            {
                public int $x = 1;

                public string $name = 'hello';
            };
            $result = prop('x')($o);
            expect($result)->toBe(1);
        });

        it('reads string property from object', function (): void {
            $o = new class()
            {
                public int $x = 1;

                public string $name = 'hello';
            };
            $result = prop('name')($o);
            expect($result)->toBe('hello');
        });
    });

    describe('Sad Path', function (): void {
        it('throws exception when reading missing property', function (): void {
            set_error_handler(
                static function ($errno, $errstr): void {
                    restore_error_handler();

                    throw new Exception($errstr, $errno);
                },
                E_WARNING,
            );

            expect(fn () => prop('missing')(new class()
            {
                public int $x = 1;

                public string $name = 'hello';
            }))->toThrow(Exception::class);
        });
    });

    describe('Edge Cases', function (): void {
        it('reads property with value zero', function (): void {
            $o = new class()
            {
                public int $count = 0;
            };
            $result = prop('count')($o);
            expect($result)->toBe(0);
        });

        it('reads property with empty string', function (): void {
            $o = new class()
            {
                public string $name = '';
            };
            $result = prop('name')($o);
            expect($result)->toBe('');
        });
    });
});
