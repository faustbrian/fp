<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use function Cline\fp\map;
use function Cline\fp\pipe;
use function Cline\fp\words;
use function count;
use function describe;
use function expect;
use function test;

describe('words()', function (): void {
    describe('Happy Paths', function (): void {
        test('splits string by spaces', function (): void {
            // Arrange
            $input = 'hello world';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('splits string by multiple spaces', function (): void {
            // Arrange
            $input = 'hello    world';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('splits string by tabs', function (): void {
            // Arrange
            $input = "hello\tworld";

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('splits string by newlines', function (): void {
            // Arrange
            $input = "hello\nworld";

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('splits string by mixed whitespace', function (): void {
            // Arrange
            $input = "hello  \t\n  world";

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('splits sentence into words', function (): void {
            // Arrange
            $input = 'The quick brown fox';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['The', 'quick', 'brown', 'fox']);
        });
    });

    describe('Sad Paths', function (): void {
        // Note: words() is type-safe at the PHP level and expects string
        // Most "sad paths" would result in TypeError at runtime, which PHP handles
        // These tests focus on logical edge cases rather than type violations
    });

    describe('Edge Cases', function (): void {
        test('returns single word for string without whitespace', function (): void {
            // Arrange
            $input = 'hello';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello']);
        });

        test('returns empty array for empty string', function (): void {
            // Arrange
            $input = '';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('returns empty array for whitespace only', function (): void {
            // Arrange
            $input = '   ';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe([]);
        });

        test('handles leading whitespace', function (): void {
            // Arrange
            $input = '  hello world';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('handles trailing whitespace', function (): void {
            // Arrange
            $input = 'hello world  ';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('handles leading and trailing whitespace', function (): void {
            // Arrange
            $input = '  hello world  ';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world']);
        });

        test('filters out empty strings from consecutive whitespace', function (): void {
            // Arrange
            $input = 'one     two     three';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['one', 'two', 'three']);
        });

        test('handles unicode characters', function (): void {
            // Arrange
            $input = 'Hello 世界 Bonjour 🌍';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['Hello', '世界', 'Bonjour', '🌍']);
        });

        test('preserves punctuation with words', function (): void {
            // Arrange
            $input = 'Hello, world!';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['Hello,', 'world!']);
        });

        test('handles numbers as words', function (): void {
            // Arrange
            $input = 'item 1 item 2 item 3';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['item', '1', 'item', '2', 'item', '3']);
        });

        test('useful for word counting', function (): void {
            // Arrange
            $text = 'The quick brown fox jumps over the lazy dog';

            // Act
            $wordCount = count(words($text));

            // Assert
            expect($wordCount)->toBe(9);
        });

        test('useful in text processing pipeline', function (): void {
            // Arrange
            $text = 'HELLO WORLD FOO BAR';

            // Act
            $result = pipe(
                $text,
                fn (string $s): array => words($s),
                map(strtolower(...)),
            );

            // Assert
            expect($result)->toBe(['hello', 'world', 'foo', 'bar']);
        });

        test('handles carriage returns', function (): void {
            // Arrange
            $input = "hello\rworld\rfoo";

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['hello', 'world', 'foo']);
        });

        test('handles mixed line endings and spaces', function (): void {
            // Arrange
            $input = "word1 word2\nword3\r\nword4\tword5";

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['word1', 'word2', 'word3', 'word4', 'word5']);
        });

        test('useful for parsing simple text formats', function (): void {
            // Arrange
            $csv = 'Name Age City';

            // Act
            $headers = words($csv);

            // Assert
            expect($headers)->toBe(['Name', 'Age', 'City']);
        });

        test('handles hyphenated words as single words', function (): void {
            // Arrange
            $input = 'state-of-the-art technology';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['state-of-the-art', 'technology']);
        });

        test('handles underscored identifiers', function (): void {
            // Arrange
            $input = 'user_name email_address phone_number';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['user_name', 'email_address', 'phone_number']);
        });

        test('extracts words from multi-line text', function (): void {
            // Arrange
            $text = "First line\nSecond line\nThird line";

            // Act
            $result = words($text);

            // Assert
            expect($result)->toBe(['First', 'line', 'Second', 'line', 'Third', 'line']);
        });

        test('handles single character words', function (): void {
            // Arrange
            $input = 'a b c d e';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toBe(['a', 'b', 'c', 'd', 'e']);
        });

        test('filters empty strings correctly', function (): void {
            // Arrange
            $input = '  word1     word2   word3  ';

            // Act
            $result = words($input);

            // Assert
            expect($result)->toHaveCount(3);
            expect($result)->toBe(['word1', 'word2', 'word3']);
        });

        test('useful for tokenization', function (): void {
            // Arrange
            $code = 'function add(a, b) { return a + b; }';

            // Act
            $tokens = words($code);

            // Assert
            expect($tokens)->toContain('function');
            expect($tokens)->toContain('add(a,');
            expect($tokens)->toContain('return');
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
