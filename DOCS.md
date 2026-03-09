## Table of Contents

1. [Pipes and Composition](#doc-docs-pipes-and-composition) (`docs/pipes-and-composition.md`)
2. [Pipeable Functions](#doc-docs-pipeable-functions) (`docs/pipeable-functions.md`)
3. [String Functions](#doc-docs-string-functions) (`docs/string-functions.md`)
4. [Object Functions](#doc-docs-object-functions) (`docs/object-functions.md`)
5. [Array Functions](#doc-docs-array-functions) (`docs/array-functions.md`)
6. [Utility Traits](#doc-docs-utility-traits) (`docs/utility-traits.md`)
7. [Examples](#doc-docs-examples) (`docs/examples.md`)
<a id="doc-docs-pipes-and-composition"></a>

The most important function in this library is `pipe()`. It takes an arbitrary number of arguments. The first is whatever starting value you want to send through a sequence of functions. The rest are any unary callable (single-argument callable) that returns a value. `pipe()` will pass the first value to the first callable, then pass the result of that to the second callable, then pass the result of that to the third callable, and so on until the pipe ends. The final result will then be returned.

## Basic Usage

For a trivial example:

```php
use function Cline\fp\pipe;

$result = pipe(5,
  static fn ($in) => $in ** 4,     // Returns 625
  static fn ($in) => $in / 4,     // Returns 156.25
  static fn ($in) => (string)$in,  // Coerces the number to a string
  strlen(...),                    // Returns the length of the string
);
// $result is now 6, because "156.25" has 6 characters in it.
```

## Composition

There is also a similar method `compose()`, which takes only an arbitrary number of callables and produces a function that will take one argument and pass it through all of them the same way. The difference is that `compose()` returns the resulting callable, while `pipe()` executes immediately. Technically it is trivial to implement either one in terms of the other, but for performance reasons they are separate.

<a id="doc-docs-pipeable-functions"></a>

As stated, `pipe()` works only with unary functions. PHP has numerous functions that are not unary, however, including many of the most useful array and string functions. For that reason, this library provides alternate, pipe-friendly versions of most common operations. All of them will take some number of arguments and return a Closure that has those arguments partially applied; that is, the provided arguments get "saved" and used when the returned function is invoked. Normally that would be within a `pipe()` chain, but they may be directly invoked as well if desired.

For example, the `explode()` function (which is namespaced to not conflict with the global function), takes a single argument, the delimiter. Its return value is a callable that will, when called with a string, call the built-in `\explode()` function with the provided string and the saved delimiter.

```php
use function Cline\fp\explode;

$result = pipe("Hello world",
  explode(' '),  // Produces ['Hello', 'world']
  count(...),    // Returns the number of array elements, which is 2
);
// $result is now 2

// or

$words = explode(' ')("Hello World");
// $words is now ['Hello', 'world']
```

The upshot of this approach is that _nearly all needle/haystack questions go away_, as either the value to operate on is subsumed into the pipe itself or very clearly provided as a secondary argument list.

Most functions will simply wrap and fall back to standard-lib functions where possible.

<a id="doc-docs-string-functions"></a>

All functions below are in the `Cline\fp` namespace.

## Available Functions

### explode(string $delimiter)

Explode a piped string using `$delimiter`.

```php
use function Cline\fp\explode;

$words = pipe("Hello World",
  explode(' ')
);
// $words is ['Hello', 'World']
```

### implode(string $glue)

Implode a piped array using `$glue`.

```php
use function Cline\fp\implode;

$sentence = pipe(['Hello', 'World'],
  implode(' ')
);
// $sentence is 'Hello World'
```

### replace(array|string $find, array|string $replace)

Does a find/replace in a piped string, using [`str_replace()`](https://www.php.net/str_replace).

```php
use function Cline\fp\replace;

$result = pipe("Hello World",
  replace('World', 'PHP')
);
// $result is 'Hello PHP'
```

<a id="doc-docs-object-functions"></a>

All functions below are in the `Cline\fp` namespace.

## Available Functions

### prop(string $prop)

Returns the `$prop` public property of a piped object.

```php
use function Cline\fp\prop;

class Person {
    public function __construct(public string $name) {}
}

$name = pipe(new Person('Alice'),
  prop('name')
);
// $name is 'Alice'
```

### method(string $method, ...$args)

Invokes `$method` on a piped object using `$args` as arguments. Both positional and named arguments are supported.

```php
use function Cline\fp\method;

class Calculator {
    public function add(int $a, int $b): int {
        return $a + $b;
    }
}

$result = pipe(new Calculator(),
  method('add', 5, 3)
);
// $result is 8
```

### typeIs(string $type)

Returns `true` if a piped value is of the specified type, `false` otherwise. Legal types are `int`, `string`, `float`, `bool`, `array`, `resource`, or a class/interface name. This will usually be the last function in a pipe.

```php
use function Cline\fp\typeIs;

$isString = pipe("Hello",
  typeIs('string')
);
// $isString is true

$isPerson = pipe(new Person('Alice'),
  typeIs(Person::class)
);
// $isPerson is true
```

<a id="doc-docs-array-functions"></a>

All functions below are in the `Cline\fp` namespace.

In many cases below, there are multiple versions of a function. There are two axes on which they vary: Whether they return an array or an iterable, and whether they operate on array keys as well.

PHP's built-in array functions do not accept iterables; Nearly all the functions below do. Those that begin with `a` will return an array, even if what was passed in is an iterator. Those that begin with `it` will return a Generator, which will produce values lazily.

Unless otherwise specified, functions will operate only on array values. Array keys will be explicitly ignored and not passed to the provided callback, but preserved. If a function has the suffix `withKeys`, then the key will be made available to the provided callback.

These must be separated due to the combination of three ill-interacting PHP features:

1. All arrays are associative, but some are short-circuited to have list-like keys, rather than having lists and maps be two separate constructs.
2. PHP supports optional arguments, which means some functions will misbehave if passed an array key as an optional second argument.
3. PHP user-space functions will silently ignore excess arguments but bundled functions will fail if called with excess arguments.

The upshot of these design choices is that it is not possible to reliably build a function that applies a callable to an array without knowing if the keys are important. That distinction must be made by the developer. The non-key versions require a callback with a single argument only (the array value), while the `withKeys` version will pass the value and key as two separate arguments to the callback.

Deciding whether to use the greedy (array) or lazy (iterable) versions of functions depends on the tradeoffs appropriate to your use case. As a general rule, the greedy version will be faster but may use more memory, while the lazy version will use less memory but may be slower. How much the difference is will vary widely with the specific use case.

## Mapping

Applies a provided callable to each entry in an iterable, producing a new iterable with the same keys as the source, but with the values replaced with the corresponding callback result.

* `amap(callable $c)`
* `amapWithKeys(callable $c)`
* `itmap(callable $c)`
* `itmapWithKeys(callable $c)`

```php
use function Cline\fp\{pipe, amap};

$doubled = pipe([1, 2, 3],
  amap(fn($x) => $x * 2)
);
// $doubled is [2, 4, 6]
```

## Filtering

Produces a new array containing only those array entries where the callable returns true. Array keys are preserved. If no callback is provided, a default of "is truthy" is used, just like PHP's native [`array_filter()`](https://www.php.net/array_filter).

* `afilter(?callable $c = null)`
* `afilterWithKeys(?callable $c = null)`
* `itfilter(?callable $c = null)`
* `itfilterWithKeys(?callable $c = null)`

```php
use function Cline\fp\{pipe, afilter};

$evens = pipe([1, 2, 3, 4],
  afilter(fn($x) => $x % 2 === 0)
);
// $evens is [2, 4]
```

## Collecting

The `collect()` function will accept a piped iterable or array, and produce an array. It's really just a wrapper around `iterator_to_array()` that guards against passing it an array, which is not supported in PHP 8.1. In PHP 8.2 and later, this function is equivalent to just using `iterator_to_array(...)` directly in a pipe, as it now accepts arrays as well.

## Reducing

Reducing, also known as `fold` or `foldl` in some languages, involves iteratively applying an operation across an array to produce a single final result. See [`array_reduce()`](https://www.php.net/array_reduce) for more details.

* `reduce(mixed $init, callable $c)` - Starting with `$init`, `$c` will be called with `$init` and each element in a piped iterable, and the result used as `$init` for the next entry. The callable signature is `($runningValue, $valueFromTheArray)`. The return from the last callable invocation is returned.
* `reduceWithKeys(mixed $init, callable $c)` - Same as `reduce()`, but the callback signature is `($runningValue, $valueFromTheArray, $keyFromTheArray)`.
* `reduceUntil(mixed $init, callable $c, callable $stop)` - Same as `reduce()`, but after each iteration `$stop($runningValue)` is called. If that returns true, the process stops early and whatever the current running value is will be returned.

```php
use function Cline\fp\{pipe, reduce};

$sum = pipe([1, 2, 3, 4],
  reduce(0, fn($carry, $x) => $carry + $x)
);
// $sum is 10
```

## First, conditionally

Several functions provide a way to obtain the first value in a sequence that meets some criteria. In all cases, they return null if nothing does.

* `first(callable $c)` - Returns the first value in a piped iterable for which `$c` returns `true`.
* `firstWithKeys(callable $c)` - Same as `first()`, but the callback is passed the value and key of each entry rather than just the value.
* `firstValue(callable $c)` - Invokes the provided callable on each item in a piped iterable, and returns the first result that is truthy, according to PHP.
* `firstValueWithKeys(callable $c)` - Same as `firstValue()`, but the callback is passed the value and key of each entry rather than just the value.

```php
use function Cline\fp\{pipe, first};

$firstEven = pipe([1, 3, 4, 6],
  first(fn($x) => $x % 2 === 0)
);
// $firstEven is 4
```

## Miscellaneous functions

* `indexBy(callable $keyMaker)` - Takes a piped array and returns a new array with the same values, but the key for each value is the result of calling `$keyMaker` with the value.
* `keyedMap(callable $values, ?callable $keys = null)` - Produces a new array from a piped array, in which the keys are the result of calling `$keys($key, $value)` and the values are the result of calling `$values($key, $value)`. If no `$keys` callback is specified, a default is provided that just indexes the entries numerically.
* `any(callable $c)` - Returns true if `$c` returns `true` for any value in a piped iterable. It may not be invoked on all items.
* `anyWithKeys(callable $c)` - Same as `any()`, but the callback is passed the value and key of each entry rather than just the value.
* `all(callable $c)` - Returns true if `$c` returns `true` for all value in a piped iterable. It may not be invoked on all items.
* `allWithKeys(callable $c)` - Same as `all()`, but the callback is passed the value and key of each entry rather than just the value.
* `flatten(array $arr)` - Accepts a multidimensional piped array and returns all the same values, but flattened into a single-dimensional sequential array.
* `append(mixed $value, mixed $key = null)` - Returns a piped array, but with the provided value added. If `$key` is provided, the value is assigned to that key regardless of whether it already exists. If not, the value is appended with `[]` and PHP's normal array handling applies.
* `atake(int $count)` - Accepts a piped iterable and returns an array, consisting of the first `$count` items from the iterable/array, or all the items if there are fewer than `$count`.
* `ittake(int $count)` - Accepts a piped iterable and returns an iterable, consisting of the first `$count` items from the iterable/array, or all the items if there are fewer than `$count`.
* `headtail(mixed $init, callable $first, callable $rest)` - Similar to `reduce()`, but uses a different reducing function for the first item.

## Utility functions

The following functions are not designed to be used with `pipe()`, but are more "traditional" functions. That said, they may be referenced as a first-class-closure.

* `iterate(mixed $init, callable $mapper)` - Produces an infinite list Generator. The first item is `$init`, the second is the result of calling `$mapper($init)`, the third is the result of calling `$mapper` on that result, etc. Note: This generator produces an infinite list! Make sure you have some termination check when calling it to avoid iterating forever.
* `nth(int $count, mixed $init, callable $mapper)` - Similar to `iterate()`, but returns the `$count`th item from the sequence and then stops.
* `head(array $a)` - Returns the first item from an array, or `null` if the array is empty.
* `tail(array $a)` - Returns all but the first item from an array.

<a id="doc-docs-utility-traits"></a>

`Cline/fp` provides two traits to aid usage of objects in a functional environment.

## Evolvable

The `Evolvable` trait provides a single method, `with()`, which accepts a variadic list of named arguments. It will produce a new copy of the same object, but with the provided values assigned to properties of the same name. Note that because it runs in private scope, visibility is ignored. This may be undesirable in some cases.

This trait is most useful with `readonly` classes:

```php
use Cline\fp\Evolvable;

readonly class Person
{
    use Evolvable;

    public function __construct(
        public string $name,
        public int $age,
        public string $jobTitle,
    ) {}
}

$p = new Person("Larry", 25, "Engineer");
$p2 = $p->with(age: 18, jobTitle: "Developer");
```

## Newable

Constructor calls in PHP are screwy, and cannot be easily chained or piped. The `Newable` trait provides a simple standard static method that wraps the constructor, making it possible to chain and pipe. Its arguments are variadic and passed to the constructor verbatim.

```php
use Cline\fp\Newable;

readonly class Person
{
    use Newable;

    public function __construct(
        public string $name,
        public int $age,
        public string $jobTitle,
    ) {}
}

$p = Person::new("Larry", 18, "Developer");
```

You can also use it within a pipe:

```php
use function Cline\fp\pipe;
use Cline\fp\Newable;

readonly class Person
{
    use Newable;

    public function __construct(
        public string $name,
    ) {}
}

$person = pipe("Alice",
    Person::new(...)
);
// $person is a Person with name "Alice"
```

<a id="doc-docs-examples"></a>

It may not be entirely obvious how all of the above fit together. To help make it clear, here's some examples of pipes and their functions in action.

## File Processing Example

The following example will take an input file name `$inputFile`, load its content into memory, trim whitespace, split it by lines into an array, call a custom `pairUp()` function on that array, filter the resulting array, and then count the remaining values. All of that in one simple statement.

```php
use function Cline\fp\{pipe, explode, afilter};

$result = pipe($inputFile,
    file_get_contents(...),
    trim(...),
    explode(PHP_EOL),
    pairUp(...),
    afilter(static fn($v): bool => $v[0] > $v[1]),
    count(...),
);
```

## Lazy Generator Example

This example uses a custom function to read lines from a file lazily. That produces a lazy generator, which is then passed to `itmap()` which will apply `parse()` to each item in the generator, but is itself a generator so is also lazy. `parse()` produces a `Step` object from each line of input. `reduce()` therefore receives an iterable of `Step` objects, and applies `move()` to each one in turn, from a starting point. Each time, `move()` returns a new `Position`.

In other words, these few lines of code constitute a complete script parsing engine, albeit a simple one. Because each step is lazy, only one `Step` is loaded into memory at once.

```php
use function Cline\fp\{pipe, itmap, reduce};

function lines(string $file): iterable
{
    $fp = fopen($file, 'rb');

    while ($line = fgets($fp)) {
        yield trim($line);
    }

    fclose($fp);
}

function parse(string $line): Step
{
    [$cmd, $size] = \explode(' ', $line);
    return new Step(cmd: Command::from($cmd), size: (int)$size);
}

$end = pipe($inputFile,
    lines(...),
    itmap(parse(...)),
    reduce(new Position(0, 0), move(...)),
);
```

## Greedy Array Version

The alternate version below uses the greedy `amap()` instead. That will produce an already-computed array of `Step` objects rather than a generator that produces `Step` objects.

```php
use function Cline\fp\{pipe, amap, reduce};

$end = pipe($inputFile,
    lines(...),
    amap(parse(...)),
    reduce(new Position(0, 0), move(...)),
);
```
