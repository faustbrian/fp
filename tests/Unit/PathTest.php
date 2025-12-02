<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\path;
use function describe;
use function expect;
use function test;

describe('path', function (): void {
    describe('Happy Paths', function (): void {
        test('accesses nested array properties', function (): void {
            $data = [
                'user' => [
                    'address' => [
                        'city' => 'New York',
                    ],
                ],
            ];
            $result = path('user.address.city')($data);
            expect($result)->toBe('New York');
        });

        test('accesses nested object properties', function (): void {
            $data = new class()
            {
                public object $user;

                public function __construct()
                {
                    $this->user = new class()
                    {
                        public object $address;

                        public function __construct()
                        {
                            $this->address = new class()
                            {
                                public string $city = 'Los Angeles';
                            };
                        }
                    };
                }
            };
            $result = path('user.address.city')($data);
            expect($result)->toBe('Los Angeles');
        });

        test('accesses mixed array and object nesting', function (): void {
            $data = [
                'user' => new class()
                {
                    public array $address = [
                        'city' => 'Chicago',
                    ];
                },
            ];
            $result = path('user.address.city')($data);
            expect($result)->toBe('Chicago');
        });

        test('accesses single level path', function (): void {
            $data = ['name' => 'John'];
            $result = path('name')($data);
            expect($result)->toBe('John');
        });

        test('accesses numeric keys in path', function (): void {
            $data = [
                'users' => [
                    0 => ['name' => 'Alice'],
                    1 => ['name' => 'Bob'],
                ],
            ];
            $result = path('users.0.name')($data);
            expect($result)->toBe('Alice');
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null when path does not exist', function (): void {
            $data = ['user' => ['name' => 'John']];
            $result = path('user.address.city')($data);
            expect($result)->toBeNull();
        });

        test('returns null when partial path exists', function (): void {
            $data = [
                'user' => [
                    'address' => null,
                ],
            ];
            $result = path('user.address.city')($data);
            expect($result)->toBeNull();
        });

        test('returns null when accessing property on primitive value', function (): void {
            $data = ['user' => 'John'];
            $result = path('user.name')($data);
            expect($result)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles empty path string returns null', function (): void {
            $data = ['name' => 'John'];
            $result = path('')($data);
            expect($result)->toBeNull();
        });

        test('accesses zero values in nested path', function (): void {
            $data = [
                'stats' => [
                    'count' => 0,
                ],
            ];
            $result = path('stats.count')($data);
            expect($result)->toBe(0);
        });

        test('accesses empty string values in nested path', function (): void {
            $data = [
                'user' => [
                    'middleName' => '',
                ],
            ];
            $result = path('user.middleName')($data);
            expect($result)->toBe('');
        });

        test('accesses false values in nested path', function (): void {
            $data = [
                'settings' => [
                    'enabled' => false,
                ],
            ];
            $result = path('settings.enabled')($data);
            expect($result)->toBeFalse();
        });

        test('accesses null values that exist in path', function (): void {
            $data = [
                'user' => [
                    'deletedAt' => null,
                ],
            ];
            $result = path('user.deletedAt')($data);
            expect($result)->toBeNull();
        });
    });
});
