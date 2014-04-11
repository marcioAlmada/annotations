Minime \ Annotations
==================

[![Build Status](https://travis-ci.org/marcioAlmada/annotations.png?branch=master)](https://travis-ci.org/marcioAlmada/annotations)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/annotations/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/annotations?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/marcioAlmada/annotations/badges/quality-score.png?s=dba04c50549638ca00a6f22ff35903066f351909)](https://scrutinizer-ci.com/g/marcioAlmada/annotations/)
[![Latest Stable Version](https://poser.pugx.org/minime/annotations/v/stable.png)](https://packagist.org/packages/minime/annotations)
[![Total Downloads](https://poser.pugx.org/minime/annotations/downloads.png)](https://packagist.org/packages/minime/annotations)
[![License](https://poser.pugx.org/minime/annotations/license.png)](https://packagist.org/packages/minime/annotations)

Minime\Annotations is the first KISS PHP annotations library.

## Features & Roadmap
- [TODO] v2.0.0
- ~~[DONE]~~ HHVM support (see [#19](https://github.com/marcioAlmada/annotations/issues/19))
- ~~[DONE]~~ Concrete annotations (see [#18](https://github.com/marcioAlmada/annotations/issues/18))
- ~~[DONE]~~ Parser improvements and optimizations (see [#17](https://github.com/marcioAlmada/annotations/issues/17))
- ~~[DONE]~~ Class, property and method annotations
- ~~[DONE]~~ API to filter and traverse annotations
- ~~[DONE]~~ Traits (for convenient integration)
- ~~[DONE]~~ Namespaced annotations
- ~~[DONE]~~ <b>Optional</b> strong typed annotations: float, integer, string, json
- ~~[DONE]~~ Dynamic annotations (eval type)
- ~~[DONE]~~ Implicit boolean annotations
- ~~[DONE]~~ Multiple value annotations
- ~~[DONE]~~ Inline Docblock support (see [#15](https://github.com/marcioAlmada/annotations/issues/15))
- ~~[DONE]~~ Multiline annotations (see [#16](https://github.com/marcioAlmada/annotations/issues/16))

## Composer Installation

```json
{
  "require": {
    "minime/annotations": "~1.12"
  }
}
```

Through terminal: `composer require minime/annotations:~1.12` :8ball:


## The Syntax

Annotations are declared through a very simple DSL: `@<optional-namespace>.<annotation-name> <optional-type> <value>`. Examples below:

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
For detailed information, please read below.

## Retrieveing Annotations

### Using Traits

The trait approach is useful when your API needs classes with self/internal inspection capabilities:

```php
/**
 * @get @post @delete
 * @entity bar
 * @has-many Baz
 * @accept ["json", "xml", "csv"]
 * @max 45
 * @delta .45
 * @cache-duration eval 1000 * 24 * 60 * 60
 */
class Foo { use Minime\Annotations\Traits\Reader; }

$foo = new Foo();
$annotations = $foo->getClassAnnotations();

$annotations->get('get')      // > bool(true)
$annotations->get('post')     // > bool(true)
$annotations->get('delete')   // > bool(true)

$annotations->get('entity')   // > string(3) "bar"
$annotations->get('has-many') // > string(3) "Baz"

$annotations->get('accept')   // > array(3){ [0] => "json" [1] => "xml" [2] => "csv" }
$annotations->get('max')      // > int(45)
$annotations->get('delta')    // > double(0.45)
$annotations->get('cache-duration')    // > int(86400000)

$annotations->get('undefined')  // > null
```

Get annotations from property and methods as easily with:

```php
$foo->getPropertyAnnotations('property_name')->...;
$foo->getMethodAnnotations('method_name')->...;
```

### Using The Facade

The facade is useful when you want to inspect classes out of your logic domain:

```php
use Minime\Annotations\Facade;

Facade::getClassAnnotations('Full\Class\Name');
Facade::getPropertyAnnotations('Full\Class\Name', 'property_name');
Facade::getMethodAnnotations('Full\Class\Name', 'method_name');
```

### Grepping and traversing

Annotations will grow and you will need to manage them. That's why we give you an `AnnotationsBag` so you can easily organize and pick annotations by name, namespace or go crazy with some regex:

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
$AnnotationsBag->useNamespace('response')->grep('^x')->export();
// > array(3){
// >    ["xml"]  => (bool) TRUE,
// >    ["xls"]  => (bool) TRUE
// > }
```

#### Traversing results

```php
foreach($annotations_bag->useNamespace('method') as $annotation => $value)
{
    // some behavior
}
```

## Concrete Annotations

Sometimes you need your annotations to encapsulate logic and you can only do it by mapping instructions to formal PHP classes. These kind of "concrete" typed annotations can be declared with the `->` (arrow symbol):

```php
/**
 * @Model\Field\Validation -> {"rules" : { "required" : true, "max-length" : 100 }}
 */
```

In the example above: when prompted, the annotation parser will instantiate a `new \Model\Field\Validation()` following the declared JSON prototype `{ "rules" : {...} }`. Voilà! Instantly classy annotations.

## Contributions

Found a bug? Have an improvement? Take a look at the [issues](https://github.com/marcioAlmada/annotations/issues). Please, send pull requests to develop branch only.

### Guide
 
0. Fork [minime\annotations](https://github.com/marcioAlmada/annotations/fork)
0. Clone forked repository
0. Install composer dependencies `$ composer install`
0. Run unit tests `$ phpunit`
0. Modify code: correct bug, implement features
0. Back to step 4

> PLEASE, be as objective as possible. Avoid combos of improvements + doc + solve bugs + features within the same pull request.

## Copyright

Copyright (c) 2014 Márcio Almada. Distributed under the terms of an MIT-style license. See LICENSE for details.


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/marcioAlmada/annotations/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
