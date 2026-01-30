<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\fp;

/**
 * Provides a pipe-friendly static factory method for object instantiation.
 *
 * This trait enables functional programming patterns by offering a `new()` static
 * method that can be used in pipeline operations where constructor invocation
 * would break the flow. The method forwards all arguments to the constructor,
 * making it a drop-in replacement for `new ClassName()` syntax.
 *
 * ```php
 * class User {
 *     use Newable;
 *
 *     public function __construct(public string $name, public int $age) {}
 * }
 *
 * // Traditional construction
 * $user = new User('John', 30);
 *
 * // Pipe-friendly construction
 * $user = User::new('John', 30);
 *
 * // In pipelines
 * $users = pipe(
 *     $data,
 *     map(User::new(...))
 * );
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 */
trait Newable
{
    /**
     * Creates a new instance using a static factory method.
     *
     * Provides a variadic factory method that forwards all arguments to the
     * class constructor. This enables pipe-friendly object instantiation and
     * supports partial application patterns in functional programming contexts.
     *
     * @param  mixed  ...$args Constructor arguments to forward to the class constructor.
     *                         Accepts any number of arguments of any type, matching
     *                         the constructor signature of the implementing class.
     * @return static A new instance of the class with the provided constructor arguments.
     *                This method is particularly useful in functional composition where
     *                the `new` keyword would break the pipeline flow.
     */
    public static function new(...$args): static
    {
        // Because this is completely variadic, phpstan's normal
        // whining about static constructors is not applicable.
        // @phpstan-ignore-next-line
        return new static(...$args);
    }
}
