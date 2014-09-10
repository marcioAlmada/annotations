Minime \ Annotations
==================

[![Build Status](https://travis-ci.org/marcioAlmada/annotations.png?branch=master)](https://travis-ci.org/marcioAlmada/annotations)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/annotations/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/annotations?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/marcioAlmada/annotations/badges/quality-score.png?s=dba04c50549638ca00a6f22ff35903066f351909)](https://scrutinizer-ci.com/g/marcioAlmada/annotations/)
[![Latest Stable Version](https://poser.pugx.org/minime/annotations/v/stable.png)](https://packagist.org/packages/minime/annotations)
[![Total Downloads](https://poser.pugx.org/minime/annotations/downloads.png)](https://packagist.org/packages/minime/annotations)
[![License](https://poser.pugx.org/minime/annotations/license.png)](https://packagist.org/packages/minime/annotations)

Minime\Annotations is the first KISS PHP annotations library.

## Composer Installation

```json
{
  "require": {
    "minime/annotations": "~2.0"
  }
}
```

Through terminal: `composer require minime/annotations:~2.0` :8ball:

## Retrieving Annotations

### Setup

First grab an instance of the `Minime\Annotations\Reader` the lazy way:

```php
$reader = \Minime\Annotations\Reader::createFromDefaults();
```

Or instantiate it yourself:

```php

use Minime\Annotations\Reader;
use Minime\Annotations\Parser;
use Minime\Annotations\Cache\ArrayCache;

$reader = new Reader(new Parser, new ArrayCache);
```

At this point it's a good idea to setup cache so you don't waste resources parsing docblocks twice.
Notice that `Reader::createFromDefaults()` will return a reader instance with `ArrayCache` enabled.
This means cache will be lost on each request, you might want to use a persistent cache like `FileCache` instead.


### Reading Annotations

Consider the following inspiring yada yada example:

```php
<?php namespace Controllers;

/**
 * @name Foo
 * @accept ["json", "xml", "csv"]
 * @delta .60
 * @cache-duration 60
 */
class FooController
{
    /**
     * @manages Models\Baz
     */
    protected $repository;

    /**
     * @get @post
     * @auto-redirect Controllers\BarController@index
     */
    public function index()
    {
        return $this->repository->all();
    }
}
```

Use the `Reader` instance to read annotations from classes, properties and methods. Like so:

```php
$annotations = $reader->getClassAnnotations('Controllers\FooController');

$annotations->get('name')   // > string(3) "Foo"
$annotations->get('accept')   // > array(3){ [0] => "json" [1] => "xml" [2] => "csv" }
$annotations->get('delta')    // > double(0.60)
$annotations->get('cache-duration')    // > int(60)

$annotations->get('undefined')  // > null
```

The same applies for methods and properties:

```php
$propertyAnnotations = $reader->getPropertyAnnotations('Controllers\FooController', 'repository');

$propertyAnnotations->get('manages')   // > string(10) "Models\Baz"

$methodAnnotations = $reader->getMethodAnnotations('Controllers\FooController', 'index');

$methodAnnotations->get('get')   // > bool(true)
$methodAnnotations->get('post')   // > bool(true)
$methodAnnotations->get('auto-redirect')   // > string(31) "Controllers\BarController@index"
```

### Grepping And Traversing

The annotations `Reader` returns `AnnotationsBag` instances so you can easily organize and pick annotations by name, namespace or regex filter:

```php
/**
 * @response.xml
 * @response.xls
 * @response.json
 * @response.csv
 * @method.get
 * @method.post
 */
class Foo {}

$AnnotationsBag = Facade::getClassAnnotations('Foo');
```

#### Namespacing

Retrieving all annotations within "response" namespace:

```php
$AnnotationsBag->useNamespace('response')->export();
// > array(3){
// >    ["xml"]  => (bool) TRUE,
// >    ["xls"]  => (bool) TRUE,
// >    ["json"] => (bool) TRUE,
// >    ["csv"]  => (bool) TRUE
// > }
```

#### Piping

You can easily "pipe" filters. This time we will grep all annotations beginning with "x" and within "response" namespace:

```php
$AnnotationsBag->useNamespace('response')->grep('/^x/')->export();
// > array(3){
// >    ["xml"]  => (bool) TRUE,
// >    ["xls"]  => (bool) TRUE
// > }
```

#### Traversing results

As you might expect, `AnnotationsBag` is traversable too:

```php
foreach($annotations->useNamespace('method') as $annotation => $value)
{
    // some behavior
}
```

## The Default Syntax

According to the default `Parser`, annotations are declared through a very simple DSL: 

```
@(<namespace><namespace-delimiter>)?<annotation-identifier> <type>? <value>?
```

- `@` must have a doc block mark
    -  must  have an annotation identifier
        - annotation identifier can have namespace with segments delimited by  `.` and `\`
    - whitespace
    - can have an annotation value
        - value can have an optional type [`json`, `string`, `integer`, `float`, `->`], if absent type is assumed from value
        - whitespace
        - optional value, if absent `true` is assumed

Some valid examples below:

```php
/**
 * Basic docblock showing DSL syntax recognized by the default Minime\Annotations\Parser
 *
 * @implicit-boolean
 * @explicit-boolean true
 * @explicit-boolean false
 *
 * @implicit-string-annotation  hello world!
 * @explicit-string-annotation "hello world!"
 * @string-strong-typed-annotation string 123456
 *
 * @integer-annotation 15
 * @integer-strong-typed-annotation integer 15
 *
 * @float-annotation   0.15
 * @float-strong-typed float 15
 *
 * @json-annotation { "foo" : ["bar", "baz"] }
 * @strong-typed-json-annotation json ["I", "must", "be", "valid", "json"]
 * 
 * @namespaced.annotation hello!
 *
 * @multiline-json-annotation {
 *   "foo" : [
 *      "bar", "baz"
 *    ]
 * }
 *
 * @multiline-indented-string-annotation
 *   ------
 *   < moo >
 *   ------ 
 *         \   ^__^
 *          \  (oo)\_______
 *             (__)\       )\/\
 *                 ||----w |
 *                 ||     ||
 * 
 * @Concrete\Class\Based\Annotation -> { "foo" : ["bar"] }
 */
```

## Concrete Annotations

Sometimes you need your annotations to encapsulate logic and you can only do it by mapping instructions to formal PHP classes. These kind of "concrete" typed annotations can be declared with the `->` (arrow symbol):

```php
/**
 * @Model\Field\Validation -> {"rules" : { "required" : true, "max-length" : 100 }}
 */
```

In the example above: when prompted, the annotation parser will instantiate a `new \Model\Field\Validation()` following the declared JSON prototype `{ "rules" : {...} }`. Voilà! Instantly classy annotations.

## Caching

This package comes with two cache handlers. `ArrayCache` (for testing) and a very basic `FileCache` for persistence. Cache handler can be set during `Reader` instantiation:

```
use Minime\Annotations\Reader;
use Minime\Annotations\Parser;
use Minime\Annotations\Cache\FileCache;

$cacheHandler = new FileCache('storage/path`);
$reader = new Reader(new Parser, $cacheHandler);
```

Or later with `FileCache::setCache()`:

```
$reader->setCache(new FileCache);
```

## Public API

### Minime\Annotations\Reader
#### Reader::getClassAnnotations($subject)
#### Reader::getPropertyAnnotations($subject, $propertyName)
#### Reader::getMethodAnnotations($subject, $methodName)
#### Reader::setCache(CacheInterface $cache)
#### Reader::getCache()
#### Reader::setParser(ParserInterface $cache)
#### Reader::getParser()

### Minime\Annotations\AnnotationsBag
#### AnnotationsBag::grep($pattern)
#### AnnotationsBag::useNamespace($pattern)
#### AnnotationsBag::union(AnnotationsBag $bag)
#### AnnotationsBag::toArray()
#### AnnotationsBag::get($key)
#### AnnotationsBag::getAsArray($key)
#### AnnotationsBag::has($key)
#### AnnotationsBag::set($key, $value)
#### AnnotationsBag::count

### Minime\Annotations\Cache\FileCache
#### FileCache::__construct($storagePath = null)
#### FileCache::clear()

Clears entire cache. See example in context with `Reader`:

```
$reader->getCache()->clear();
```

### Minime\Annotations\Cache\ArrayCache

Array cache is lost after each request. Use array cache for tests only.

#### ArrayCache::clear()

Clears entire cache. See example in context with `Reader`:

```
$reader->getCache()->clear();
```

## Contributions

Found a bug? Have an improvement? Take a look at the [issues](https://github.com/marcioAlmada/annotations/issues). Please, send pull requests to develop branch only.

### Guide
 
0. Fork [minime\annotations](https://github.com/marcioAlmada/annotations/fork)
0. Clone forked repository
0. Install composer dependencies `$ composer install`
0. Run unit tests `$ phpunit`
0. Modify code: correct bug, implement features
0. Back to step 4

## Copyright

Copyright (c) 2014 Márcio Almada. Distributed under the terms of an MIT-style license. See LICENSE for details.
