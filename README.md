[![GitHub Workflow Status][ico-tests]][link-tests]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

------

This library contains functional utilities intended for use with PHP 8.4 and later. Its primary tool is the `pipe()` function, which takes a starting argument and then a series of callables to "pipe" that argument through. Most other functions are utilities that produce a closure that takes the return from a previous `pipe()` step as its only argument.

## Requirements

> **Requires [PHP 8.4+](https://php.net/releases/)**

## Installation

```bash
composer require cline/fp
```

## Documentation

- **[Pipes and Composition](cookbook/pipes-and-composition.md)** - Introduction to `pipe()` and `compose()` functions
- **[Pipeable Functions](cookbook/pipeable-functions.md)** - Understanding partially applied functions
- **[String Functions](cookbook/string-functions.md)** - String manipulation utilities
- **[Object Functions](cookbook/object-functions.md)** - Object property and method utilities
- **[Array Functions](cookbook/array-functions.md)** - Comprehensive array operations (map, filter, reduce, etc.)
- **[Utility Traits](cookbook/utility-traits.md)** - `Evolvable` and `Newable` traits for functional object patterns
- **[Examples](cookbook/examples.md)** - Real-world usage examples and patterns

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please use the [GitHub security reporting form][link-security] rather than the issue queue.

## Credits

- [Brian Faust][link-maintainer]
- [Larry Garfield][link-author]
- [All Contributors][link-contributors]

## License

The MIT License. Please see [License File](LICENSE.md) for more information.

[ico-tests]: https://github.com/faustbrian/fp/actions/workflows/quality-assurance.yaml/badge.svg
[ico-version]: https://img.shields.io/packagist/v/cline/fp.svg
[ico-license]: https://img.shields.io/badge/License-LGPLv3-green.svg
[ico-downloads]: https://img.shields.io/packagist/dt/cline/fp.svg

[link-tests]: https://github.com/faustbrian/fp/actions
[link-packagist]: https://packagist.org/packages/cline/fp
[link-downloads]: https://packagist.org/packages/cline/fp
[link-security]: https://github.com/faustbrian/fp/security
[link-maintainer]: https://github.com/faustbrian
[link-author]: https://github.com/Crell
[link-contributors]: ../../contributors
