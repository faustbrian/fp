<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function describe;
use function expect;
use function it;

describe('Newable Trait', function (): void {
    describe('Happy Path', function (): void {
        it('creates instance of class without constructor', function (): void {
            $c = NoConstructor::new();
            expect($c)->toBeInstanceOf(NoConstructor::class);
        });

        it('creates instance with positional arguments', function (): void {
            $c = HasConstructor::new(1, 'hello');
            expect($c)->toBeInstanceOf(HasConstructor::class);
            expect($c->x)->toBe(1);
            expect($c->name)->toBe('hello');
        });

        it('creates instance with named arguments', function (): void {
            $c = HasConstructor::new(name: 'hello', x: 1);
            expect($c)->toBeInstanceOf(HasConstructor::class);
            expect($c->x)->toBe(1);
            expect($c->name)->toBe('hello');
        });

        it('creates instance with variadic positional arguments', function (): void {
            $c = HasConstructor::new(...[1, 'hello']);
            expect($c)->toBeInstanceOf(HasConstructor::class);
            expect($c->x)->toBe(1);
            expect($c->name)->toBe('hello');
        });

        it('creates instance with variadic named arguments', function (): void {
            $c = HasConstructor::new(...['name' => 'hello', 'x' => 1]);
            expect($c)->toBeInstanceOf(HasConstructor::class);
            expect($c->x)->toBe(1);
            expect($c->name)->toBe('hello');
        });
    });

    describe('Edge Cases', function (): void {
        it('creates instance with empty string name', function (): void {
            $c = HasConstructor::new(0, '');
            expect($c->x)->toBe(0);
            expect($c->name)->toBe('');
        });

        it('creates instance with mixed argument order using named params', function (): void {
            $c = HasConstructor::new(name: 'world', x: 42);
            expect($c->x)->toBe(42);
            expect($c->name)->toBe('world');
        });

        it('creates multiple distinct instances', function (): void {
            $c1 = NoConstructor::new();
            $c2 = NoConstructor::new();
            expect($c1)->not->toBe($c2);
            expect($c1)->toEqual($c2);
        });
    });
});

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class NoConstructor
{
    use Newable;
}

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class HasConstructor
{
    use Newable;

    public function __construct(
        public int $x,
        public string $name,
    ) {}
}
