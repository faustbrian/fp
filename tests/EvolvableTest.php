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

describe('Evolvable Trait', function (): void {
    describe('Happy Path', function (): void {
        it('evolves object with constructor properties', function (): void {
            $c = new Constructor(1, 2, 3);
            $c2 = $c->with(public: 4, protected: 5, private: 6);
            $expected = new Constructor(4, 5, 6);
            expect($c2)->toEqual($expected);
        });

        it('evolves object with defined properties', function (): void {
            $c = new Props(1, 2, 3);
            $c2 = $c->with(public: 4, protected: 5, private: 6);
            $expected = new Props(4, 5, 6);
            expect($c2)->toEqual($expected);
        });

        it('evolves object with undefined properties setting both', function (): void {
            $c = new Uninitialized();
            $c2 = $c->with(set: 6, notSet: 1);
            expect($c2->set)->toBe(6);
            expect($c2->notSet)->toBe(1);
        });

        it('evolves object with undefined properties setting only one', function (): void {
            $c = new Uninitialized();
            $c2 = $c->with(set: 6);
            expect($c2->set)->toBe(6);
            expect($c2->notSet ?? null)->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        it('evolves object with partial property updates', function (): void {
            $c = new Constructor(1, 2, 3);
            $c2 = $c->with(public: 10);
            expect($c2->public)->toBe(10);
            expect($c2)->not->toEqual($c);
        });

        it('creates new instance without modifying original', function (): void {
            $c = new Constructor(1, 2, 3);
            $c2 = $c->with(public: 99);
            expect($c->public)->toBe(1);
            expect($c2->public)->toBe(99);
        });

        it('evolves with same values creates equal but different instance', function (): void {
            $c = new Constructor(1, 2, 3);
            $c2 = $c->with(public: 1, protected: 2, private: 3);
            expect($c2)->toEqual($c);
            expect($c2)->not->toBe($c);
        });

        it('handles uninitialized property remaining unset', function (): void {
            $c = new Uninitialized();
            $originalSet = $c->set;
            $c2 = $c->with();
            expect($c2->set)->toBe($originalSet);
        });
    });
});

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class Constructor
{
    use Evolvable;

    public function __construct(
        public int $public,
        private int $protected,
        private int $private,
    ) {}
}

/**
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class Props
{
    use Evolvable;

    public function __construct(
        public int $public,
        private int $protected,
        private int $private,
    ) {}
}

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class Uninitialized
{
    use Evolvable;

    public int $notSet;

    public int $set = 5;
}
