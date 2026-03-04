<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function array_slice;
use function array_values;
use function Cline\fp\filter;
use function Cline\fp\lines;
use function Cline\fp\map;
use function Cline\fp\pipe;
use function count;
use function describe;
use function expect;
use function mb_trim;
use function test;

describe('lines()', function (): void {
    describe('Happy Paths', function (): void {
        test('splits string by newline', function (): void {
            // Arrange
            $input = "hello\nworld";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('splits string by carriage return and newline', function (): void {
            // Arrange
            $input = "foo\r\nbar\r\nbaz";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['foo', 'bar', 'baz']);
        });

        test('splits string by carriage return', function (): void {
            // Arrange
            $input = "one\rtwo\rthree";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['one', 'two', 'three']);
        });

        test('splits mixed newline formats', function (): void {
            // Arrange
            $input = "line1\nline2\r\nline3\rline4";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['line1', 'line2', 'line3', 'line4']);
        });

        test('splits multi-line text block', function (): void {
            // Arrange
            $input = "First line\nSecond line\nThird line";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['First line', 'Second line', 'Third line']);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: lines() is type-safe at the PHP level and expects string
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns single element for string without newlines', function (): void {
            // Arrange
            $input = 'single line';

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['single line']);
        });

        test('returns empty array element for empty string', function (): void {
            // Arrange
            $input = '';

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['']);
        });

        test('preserves empty lines', function (): void {
            // Arrange
            $input = "one\n\ntwo";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['one', '', 'two']);
        });

        test('handles multiple consecutive newlines', function (): void {
            // Arrange
            $input = "a\n\n\nb";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['a', '', '', 'b']);
        });

        test('handles leading newline', function (): void {
            // Arrange
            $input = "\nhello";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['', 'hello']);
        });

        test('handles trailing newline', function (): void {
            // Arrange
            $input = "hello\n";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['hello', '']);
        });

        test('handles only newline', function (): void {
            // Arrange
            $input = "\n";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['', '']);
        });

        test('handles string with spaces', function (): void {
            // Arrange
            $input = "line with spaces\nanother line with spaces";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['line with spaces', 'another line with spaces']);
        });

        test('handles string with tabs', function (): void {
            // Arrange
            $input = "line\twith\ttabs\nanother\tline";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(["line\twith\ttabs", "another\tline"]);
        });

        test('handles unicode characters', function (): void {
            // Arrange
            $input = "Hello 世界\nBonjour 🌍";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['Hello 世界', 'Bonjour 🌍']);
        });

        test('handles special characters', function (): void {
            // Arrange
            $input = "line!@#$\nline%^&*";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['line!@#$', 'line%^&*']);
        });

        test('useful for processing file contents', function (): void {
            // Arrange
            $fileContent = "header\nfirst row\nsecond row\nfooter";

            // Act
            $result = lines($fileContent);

            // Assert
            expect($result)->toHaveCount(4);
            expect($result[0])->toBe('header');
            expect($result[3])->toBe('footer');
        });

        test('useful in text processing pipeline', function (): void {
            // Arrange
            $text = "  line1  \n  line2  \n  line3  ";

            // Act
            $result = pipe(
                $text,
                fn (string $s): array => lines($s),
                map(fn (string $s): string => mb_trim($s)),
                filter(fn (string $s): bool => $s !== ''),
            );

            // Assert
            expect(array_values($result))->toBe(['line1', 'line2', 'line3']);
        });

        test('handles Windows line endings', function (): void {
            // Arrange
            $input = "Windows\r\nline\r\nendings";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['Windows', 'line', 'endings']);
        });

        test('handles Unix line endings', function (): void {
            // Arrange
            $input = "Unix\nline\nendings";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['Unix', 'line', 'endings']);
        });

        test('handles Mac line endings', function (): void {
            // Arrange
            $input = "Mac\rline\rendings";

            // Act
            $result = lines($input);

            // Assert
            expect($result)->toBe(['Mac', 'line', 'endings']);
        });

        test('counts lines in text', function (): void {
            // Arrange
            $text = "line1\nline2\nline3\nline4\nline5";

            // Act
            $lineCount = count(lines($text));

            // Assert
            expect($lineCount)->toBe(5);
        });

        test('extracts specific lines', function (): void {
            // Arrange
            $text = "header\ndata1\ndata2\ndata3\nfooter";

            // Act
            $allLines = lines($text);
            $dataLines = array_slice($allLines, 1, 3);

            // Assert
            expect($dataLines)->toBe(['data1', 'data2', 'data3']);
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
