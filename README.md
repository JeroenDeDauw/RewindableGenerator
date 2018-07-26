# Rewindable Generator

[![Build Status](https://secure.travis-ci.org/JeroenDeDauw/RewindableGenerator.png?branch=master)](http://travis-ci.org/JeroenDeDauw/RewindableGenerator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JeroenDeDauw/RewindableGenerator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/RewindableGenerator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/JeroenDeDauw/RewindableGenerator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/JeroenDeDauw/RewindableGenerator/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/jeroen/rewindable-generator/version.png)](https://packagist.org/packages/jeroen/rewindable-generator)
[![Download count](https://poser.pugx.org/jeroen/rewindable-generator/d/total.png)](https://packagist.org/packages/jeroen/rewindable-generator)

Provides a simple adapter to make [generators](http://php.net/manual/en/language.generators.overview.php) rewindable. Please beware that you can do the same by using [PHPs native `CachingIterator`](https://secure.php.net/manual/en/class.cachingiterator.php).

Unfortunately, you cannot do this:

```php
$generator = $myGeneratorFunction();
iterator_to_array($generator);
iterator_to_array($generator); // boom!
```

Or this:

```php
$generator = $myGeneratorFunction();
$generator->next();
$generator->rewind(); // boom!
```

Both result in an `Exception`, as proven by the tests in `tests/GeneratorTest.php`. This library provides
a simple class that takes a generator function (the function, not its return value) and adapts it to
a rewindable `Iterator`.

```php
$generator = new RewindableGenerator($myGeneratorFunction);
iterator_to_array($generator);
iterator_to_array($generator); // works as expected
$generator->rewind(); // works as expected
```

## Installation

To add this package as a local, per-project dependency to your project, simply add a
dependency on `jeroen/rewindable-generator` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
Rewindable Generator 1.x:

```json
{
    "require": {
        "jeroen/rewindable-generator": "~1.0"
    }
}
```

## Running the tests

For tests only

    composer test

For style checks only

	composer cs

For a full CI run

	composer ci

## Release notes

### Version 1.2.0 (2017-05-16)

* Dropped PHP 5.x support

### Version 1.1.1 (2015-11-08)

* Fixed HHVM compatibility. [Maybe...](https://github.com/facebook/hhvm/issues/1871)

### Version 1.1.0 (2015-11-08)

* Added `onRewind` function and second constructor parameter to `RewindableGenerator`

### Version 1.0.0 (2015-11-08)

* Initial release - [read blog post](http://www.bn2vs.com/blog/2015/11/08/rewindable-php-generators/)
