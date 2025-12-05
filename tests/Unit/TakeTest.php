<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ArrayIterator;

use function array_combine;
use function Cline\fp\atake;
use function Cline\fp\ittake;
use function describe;
use function expect;
use function it;
use function iterator_to_array;
use function range;

describe('take', function (): void {
    describe('Happy Path', function (): void {
        it('takes first n elements from array with atake', function (): void {
            $a = array_combine(range('a', 'z'), range('A', 'Z'));
            $result = atake(3)($a);
            expect($result)->toBe(['a' => 'A', 'b' => 'B', 'c' => 'C']);
        });

        it('takes first n elements from iterator with atake', function (): void {
            $l = array_combine(range('a', 'z'), range('A', 'Z'));
            $a = new ArrayIterator($l);
            $result = atake(3)($a);
            expect($result)->toBe(['a' => 'A', 'b' => 'B', 'c' => 'C']);
        });

        it('takes first n elements from array with ittake', function (): void {
            $a = array_combine(range('a', 'z'), range('A', 'Z'));
            $result = ittake(3)($a);
            expect(iterator_to_array($result))->toBe(['a' => 'A', 'b' => 'B', 'c' => 'C']);
        });

        it('takes first n elements from iterator with ittake', function (): void {
            $l = array_combine(range('a', 'z'), range('A', 'Z'));
            $a = new ArrayIterator($l);
            $result = ittake(3)($a);
            expect(iterator_to_array($result))->toBe(['a' => 'A', 'b' => 'B', 'c' => 'C']);
        });
    });

    describe('Edge Cases', function (): void {
        it('returns all elements when requesting more than available with atake', function (): void {
            $a = array_combine(range('a', 'b'), range('A', 'B'));
            $result = atake(3)($a);
            expect($result)->toBe(['a' => 'A', 'b' => 'B']);
        });

        it('returns all elements when requesting more than available from iterator with atake', function (): void {
            $l = array_combine(range('a', 'b'), range('A', 'B'));
            $a = new ArrayIterator($l);
            $result = atake(3)($a);
            expect($result)->toBe(['a' => 'A', 'b' => 'B']);
        });

        it('returns all elements when requesting more than available with ittake', function (): void {
            $a = array_combine(range('a', 'b'), range('A', 'B'));
            $result = ittake(3)($a);
            expect(iterator_to_array($result))->toBe(['a' => 'A', 'b' => 'B']);
        });
    });
});
