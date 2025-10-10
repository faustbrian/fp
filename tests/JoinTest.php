<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\implode;
use function Cline\fp\join;
use function Cline\fp\map;
use function Cline\fp\pipe;
use function describe;
use function expect;
use function mb_strtoupper;
use function test;

describe('join()', function (): void {
    describe('Happy Paths', function (): void {
        test('joins array with comma separator', function (): void {
            // Arrange
            $joinComma = join(', ');
            $input = ['a', 'b', 'c'];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('a, b, c');
        });

        test('joins array with hyphen separator', function (): void {
            // Arrange
            $joinHyphen = join('-');
            $input = ['hello', 'world', 'foo'];

            // Act
            $result = $joinHyphen($input);

            // Assert
            expect($result)->toBe('hello-world-foo');
        });

        test('joins array with space separator', function (): void {
            // Arrange
            $joinSpace = join(' ');
            $input = ['hello', 'world'];

            // Act
            $result = $joinSpace($input);

            // Assert
            expect($result)->toBe('hello world');
        });

        test('joins numbers into string', function (): void {
            // Arrange
            $joinComma = join(',');
            $input = [1, 2, 3, 4, 5];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('1,2,3,4,5');
        });

        test('joins with empty separator', function (): void {
            // Arrange
            $joinEmpty = join('');
            $input = ['h', 'e', 'l', 'l', 'o'];

            // Act
            $result = $joinEmpty($input);

            // Assert
            expect($result)->toBe('hello');
        });

        test('joins with multi-character separator', function (): void {
            // Arrange
            $joinPipe = join(' | ');
            $input = ['one', 'two', 'three'];

            // Act
            $result = $joinPipe($input);

            // Assert
            expect($result)->toBe('one | two | three');
        });
    });

    describe('Sad Paths', function (): void {
        // Note: join() is type-safe at the PHP level
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('joins empty array', function (): void {
            // Arrange
            $joinComma = join(', ');
            $input = [];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('');
        });

        test('joins single element array', function (): void {
            // Arrange
            $joinComma = join(', ');
            $input = ['only'];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('only');
        });

        test('joins array with two elements', function (): void {
            // Arrange
            $joinAnd = join(' and ');
            $input = ['first', 'second'];

            // Act
            $result = $joinAnd($input);

            // Assert
            expect($result)->toBe('first and second');
        });

        test('handles empty strings in array', function (): void {
            // Arrange
            $joinComma = join(',');
            $input = ['a', '', 'b', '', 'c'];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('a,,b,,c');
        });

        test('handles null values by converting to empty string', function (): void {
            // Arrange
            $joinComma = join(',');
            $input = ['a', null, 'b'];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('a,,b');
        });

        test('handles false values by converting to empty string', function (): void {
            // Arrange
            $joinComma = join(',');
            $input = ['a', false, 'b'];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('a,,b');
        });

        test('handles zero values', function (): void {
            // Arrange
            $joinComma = join(',');
            $input = [0, 1, 0, 2];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('0,1,0,2');
        });

        test('joins associative array values', function (): void {
            // Arrange
            $joinComma = join(', ');
            $input = ['a' => 'first', 'b' => 'second', 'c' => 'third'];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('first, second, third');
        });

        test('ignores array keys', function (): void {
            // Arrange
            $joinComma = join(',');
            $input = [10 => 'a', 20 => 'b', 30 => 'c'];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('a,b,c');
        });

        test('can be reused with different arrays', function (): void {
            // Arrange
            $joinComma = join(', ');

            // Act
            $result1 = $joinComma(['a', 'b']);
            $result2 = $joinComma(['x', 'y', 'z']);

            // Assert
            expect($result1)->toBe('a, b');
            expect($result2)->toBe('x, y, z');
        });

        test('joins with newline separator', function (): void {
            // Arrange
            $joinNewline = join("\n");
            $input = ['line1', 'line2', 'line3'];

            // Act
            $result = $joinNewline($input);

            // Assert
            expect($result)->toBe("line1\nline2\nline3");
        });

        test('joins with tab separator', function (): void {
            // Arrange
            $joinTab = join("\t");
            $input = ['col1', 'col2', 'col3'];

            // Act
            $result = $joinTab($input);

            // Assert
            expect($result)->toBe("col1\tcol2\tcol3");
        });

        test('handles mixed types in array', function (): void {
            // Arrange
            $joinComma = join(',');
            $input = [1, 'two', 3.0, true];

            // Act
            $result = $joinComma($input);

            // Assert
            expect($result)->toBe('1,two,3,1');
        });

        test('useful for CSV generation', function (): void {
            // Arrange
            $toCSV = join(',');
            $row1 = ['Alice', '25', 'Engineer'];
            $row2 = ['Bob', '30', 'Designer'];

            // Act
            $csv1 = $toCSV($row1);
            $csv2 = $toCSV($row2);

            // Assert
            expect($csv1)->toBe('Alice,25,Engineer');
            expect($csv2)->toBe('Bob,30,Designer');
        });

        test('works as alias for implode', function (): void {
            // Arrange
            $input = ['a', 'b', 'c'];
            $glue = ', ';
            $joinResult = join($glue);
            $implodeResult = implode($glue);

            // Act
            $joined = $joinResult($input);
            $imploded = $implodeResult($input);

            // Assert
            expect($joined)->toBe($imploded);
            expect($joined)->toBe('a, b, c');
        });

        test('useful in pipelines', function (): void {
            // Arrange
            $input = ['hello', 'world', 'foo'];

            // Act
            $result = pipe(
                $input,
                map(fn (string $s): string => mb_strtoupper($s)),
                join(' '),
            );

            // Assert
            expect($result)->toBe('HELLO WORLD FOO');
        });
    });

    describe('Regressions', function (): void {
        // Only include tests for documented bugs with ticket references
        // Example structure for future regression tests:
        // test('prevents X bug that caused Y [TICKET-123]', function (): void {
        //     // Test implementation
        // });
    });
});
