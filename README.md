Minime \ Annotations
==================

[![Build Status](https://travis-ci.org/marcioAlmada/annotations.png?branch=master)](https://travis-ci.org/marcioAlmada/annotations)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/annotations/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/annotations?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/marcioAlmada/annotations/badges/quality-score.png?s=dba04c50549638ca00a6f22ff35903066f351909)](https://scrutinizer-ci.com/g/marcioAlmada/annotations/)
[![Latest Stable Version](https://poser.pugx.org/minime/annotations/v/stable.png)](https://packagist.org/packages/minime/annotations)
[![Total Downloads](https://poser.pugx.org/minime/annotations/downloads.png)](https://packagist.org/packages/minime/annotations)
[![Reference Status](https://www.versioneye.com/php/minime:annotations/reference_badge.svg?style=flat)](https://www.versioneye.com/php/minime:annotations/references)
[![License](https://poser.pugx.org/minime/annotations/license.png)](https://packagist.org/packages/minime/annotations)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5065eb68-a841-4877-9e30-83fd2e224f29/mini.png)](https://insight.sensiolabs.com/projects/5065eb68-a841-4877-9e30-83fd2e224f29)

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

## Setup

First grab an instance of the `Minime\Annotations\Reader` the lazy way:

```php
$reader = \Minime\Annotations\Reader::createFromDefaults();
```

Or instantiate the annotations reader yourself with:

```php
use Minime\Annotations\Reader;
use Minime\Annotations\Parser;
use Minime\Annotations\Cache\ArrayCache;

$reader = new Reader(new Parser, new ArrayCache);
```

Notice that `Reader::createFromDefaults()` creates a reader instance with array cache enabled.
On production you might want to use a persistent cache handler like `FileCache` instead:

```php
use Minime\Annotations\Cache\FileCache;

$reader->setCache(new FileCache('app/storage/path'));
```

## Reading Annotations

Consider the following class with some docblock annotations:

```php
<?php
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
     * @redirect Controllers\BarController@index
     */
    public function index(){}
}
```

Let's use the `Minime\Annotations\Reader` instance to read annotations from classes,
properties and methods. Like so:

```php
$annotations = $reader->getClassAnnotations('FooController');

$annotations->get('name')     // > string(3) "Foo"
$annotations->get('accept')   // > array(3){ [0] => "json" [1] => "xml" [2] => "csv" }
$annotations->get('delta')           // > double(0.60)
$annotations->get('cache-duration')  // > int(60)

$annotations->get('undefined')  // > null
```

The same applies to class properties...

```php
$annotations = $reader->getPropertyAnnotations('FooController', 'repository');
$annotations->get('manages')   // > string(10) "Models\Baz"
```

methods...

```php
$annotations = $reader->getMethodAnnotations('FooController', 'index');
$annotations->get('get')   // > bool(true)
$annotations->get('post')   // > bool(true)
$annotations->get('auto-redirect')   // > string(19) "BarController@index"
```

and functions || closures:

```php
/** @name Foo */ function foo(){}
$annotations = $reader->getFunctionAnnotations('foo');
$annotations->get('name')   // > string(3) "Foo"
```

## Managing Annotations

The annotations reader `Reader::get(*)Annotations`  always returns `AnnotationsBag`
instances so you can easily manage annotations:

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

$annotations = $reader->getClassAnnotations('Foo'); // object<AnnotationsBag>
```

### Namespacing

It's a good idea to namespace custom annotations that belong to a package.
Later you can retrieve all those namespaced annotations using the `AnnotationsBag` api:

```php
$AnnotationsBag->useNamespace('response')->toArray();
// > array(3){
// >    ["xml"]  => (bool) TRUE,
// >    ["xls"]  => (bool) TRUE,
// >    ["json"] => (bool) TRUE,
// >    ["csv"]  => (bool) TRUE
// > }
```

### Piping Filters

You can also easily "pipe" filters. This time let's "grep" all annotations beginning with "x" and within
"response" namespace:

```php
$AnnotationsBag->useNamespace('response')->grep('/^x/')->toArray();
// > array(3){
// >    ["xml"]  => (bool) TRUE,
// >    ["xls"]  => (bool) TRUE
// > }
```

### Traversing results

As you might expect, `AnnotationsBag` is traversable too:

```php
foreach($annotations->useNamespace('method') as $annotation => $value)
{
    // some behavior
}
```

### Union

You can also perform union operations between two annotations sets:

```php
$annotations->union($defaultAnnotations);
```

Please refer to annotations bag public [API](#minimeannotationsreader) for more operations.

## The Default Syntax

![@(<namespace><namespace-delimiter>)?<annotation-identifier> <type>? <value>?](https://dl.dropboxusercontent.com/u/49549530/annotations/grammar.png)

Which basically means that:

- `@` line must start with a docblock annotation tag
-  must  have an annotation identifier
    - annotation identifier can have namespace with segments delimited by  `.` or `\`
- whitespace
- can have an annotation value
    - value can have an optional type [`json`, `string`, `integer`, `float`, `->`]
        - if absent, type is assumed from value
    - whitespace
    - optional value
        - if absent, `true` is assumed

Some valid examples below:

```php
/**
 * Basic docblock showing syntax recognized by the default Minime\Annotations\Parser
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

Sometimes you need your annotations to encapsulate logic and you can only do it by mapping
instructions to formal PHP classes. These kind of "concrete" typed annotations can be declared with
the `->` (arrow symbol):

```php
/**
 * @Model\Field\Validation -> {
 *     "rules" : {
 *         "required" : true,
 *         "max-length" : 100
 *     }
 * }
 */
```

In the example above: when prompted, the annotation parser will instantiate a
`new \Model\Field\Validation()` following the declared JSON prototype `{ "rules" : {...} }`.
Voilà! Instantly classy annotations.

## Caching

This package comes with two basic cache handlers. `ArrayCache` (for testing) and a very simple `FileCache`
handler for persistence. Cache handlers can be set during `Minime\Annotations\Reader` instantiation:

```php
use Minime\Annotations\Reader;
use Minime\Annotations\Parser;
use Minime\Annotations\Cache\FileCache;

$cacheHandler = new FileCache('storage/path');
$reader = new Reader(new Parser, $cacheHandler);
```

Or later with `Reader::setCache()`:

```php
$reader->setCache(new FileCache);
```

## Public API

### Minime\Annotations\Reader
#### ::getClassAnnotations($subject)

Get all annotations from a given class:

```php
$reader->getClassAnnotations('Full\Qualified\Class');
```

#### ::getPropertyAnnotations($subject, $propertyName)

Get all annotations from a given class property:

```php
$reader->getPropertyAnnotations('Full\Qualified\Class', 'propertyName');
```

#### ::getMethodAnnotations($subject, $methodName)

Get all annotations from a given class method:

```php
$reader->getMethodAnnotations('Full\Qualified\Class', 'methodName');
```

#### ::getFunctionAnnotations($fn)

Get all annotations from a given full qualified function name or closure:

```php
$reader->getFunctionAnnotations('utils\foo');
```

#### ::getCache()
#### ::setCache(CacheInterface $cache)
#### ::getParser()
#### ::setParser(ParserInterface $cache)

### Minime\Annotations\AnnotationsBag
#### ::grep($pattern)

Filters annotations using a valid regular expression and returns a new `AnnotationBag`
with the matching results.

#### ::useNamespace($pattern)

Isolates a given namespace of annotations. Basically this method filters annotations by a namespace
and returns a new `AnnotationBag` with simplified annotations identifiers.

#### ::union(AnnotationsBag $bag)

Performs union operation with a subject `AnnotationBag`:

```php
$annotations->union($defaultAnnotations);
```
#### ::toArray()
#### ::get($key, $default = null)
#### ::getAsArray($key)
#### ::has($key)
#### ::set($key, $value)
#### ::count

### Minime\Annotations\Cache\FileCache
#### ::__construct($storagePath = null)

See example in context with `Minime\Annotations\Reader`:

```php
use Minime\Annotations\Cache\FileCache;

$reader->setCache(new FileCache('app/tmp/storage/path'));
```

If no path is given OS tmp dir is assumed as cache storage path.

#### ::clear()

Clears entire cache. See example in context with `Minime\Annotations\Reader`:

```php
$reader->getCache()->clear();
```

### Minime\Annotations\Cache\ArrayCache

#### ::clear()

Clears entire cache. See example in context with `Minime\Annotations\Reader`:

```php
$reader->getCache()->clear();
```

## Contributions

Found a bug? Have an improvement? Take a look at the [issues](https://github.com/marcioAlmada/annotations/issues).

### Guide
 
0. Fork [minime\annotations](https://github.com/marcioAlmada/annotations/fork)
0. Clone forked repository
0. Install composer dependencies `$ composer install`
0. Run unit tests `$ phpunit`
0. Modify code: correct bug, implement features
0. Back to step 4

## Copyright

Copyright (c) 2013-2014 Márcio Almada. Distributed under the terms of an MIT-style license.
See LICENSE for details.
