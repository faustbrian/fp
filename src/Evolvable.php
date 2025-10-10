<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

use ReflectionClass;

use function array_key_exists;

/**
 * Provides immutable object evolution through property replacement.
 *
 * This trait enables value objects to be "evolved" by creating modified clones with
 * specific properties changed, maintaining immutability while avoiding verbose clone
 * and property assignment code. Based on similar implementation from Spatie's php-cloneable.
 *
 * @see https://github.com/spatie/php-cloneable
 *
 * @author Brian Faust <brian@cline.sh>
 */
trait Evolvable
{
    /**
     * Creates a modified clone with specified properties replaced.
     *
     * Returns a new instance of the object with the provided properties replaced while
     * preserving all other properties from the original instance. This enables immutable
     * update patterns similar to JavaScript's spread operator or Rust's struct update syntax.
     * Uninitialized properties remain uninitialized in the clone.
     *
     * ```php
     * class User {
     *     use Evolvable;
     *     public function __construct(
     *         public string $name,
     *         public string $email,
     *     ) {}
     * }
     *
     * $user = new User('John', 'john@example.com');
     * $updated = $user->with(email: 'newemail@example.com');
     * // $updated->name is 'John', $updated->email is 'newemail@example.com'
     * ```
     *
     * @param  mixed  ...$values Named arguments mapping property names to new values.
     *                           Only the specified properties are replaced; all others
     *                           are copied from the original instance. Property names
     *                           must correspond to existing class properties.
     * @return static A new instance with the specified properties replaced and all
     *                other properties copied from the original instance. The original
     *                instance remains unchanged, maintaining immutability.
     */
    public function with(...$values): static
    {
        $r = new ReflectionClass(static::class);

        $clone = $r->newInstanceWithoutConstructor();

        // If a property is still undefined, it won't show up from just iterating $this.
        // We have to go through reflection to get the complete list of properties.
        foreach ($r->getProperties() as $rProp) {
            $field = $rProp->name;

            // PHPStan is just flat out wrong on this line; it assumes
            /**
             * $values is an int-based array when it will be string-based.
             * This is probably a bug in PHPStan's variadic handling.
             *
             * @phpstan-ignore-next-line
             */
            if (array_key_exists($field, $values)) {
                $clone->{$field} = $values[$field];
            } elseif ($rProp->isInitialized($this)) {
                $clone->{$field} = $rProp->getValue($this);
            }

            // If the field is uninitialized, leave it as is.
        }

        return $clone;
    }
}
