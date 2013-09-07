Minime \ Annotations
==================

[![Build Status](https://travis-ci.org/marcioAlmada/annotations.png?branch=master)](https://travis-ci.org/marcioAlmada/annotations)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/annotations/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/annotations?branch=master)
[![SemVer](http://calm-shore-6115.herokuapp.com/?label=semver&value=1.0.0&color=yellow)](http://semver.org)
[![Latest Stable Version](https://poser.pugx.org/minime/annotations/v/stable.png)](https://packagist.org/packages/minime/annotations)
[![Total Downloads](https://poser.pugx.org/minime/annotations/downloads.png)](https://packagist.org/packages/minime/annotations)

Minime\Annotations is a lightweight PHP annotation library.
It supports both weak and strong typed annotations that just works.
Smart JSON support included.

## Basic Usage

### Using as a trait

The trait approach is useful for self / internal reflection:

```php
/**
 * @get @post @delete
 * @entity bar
 * @has-many Baz
 * @export json ["json", "xml", "csv"]
 * @max integer 45
 * @delta float .45
 */
class FooController
{
    use Minime\Annotations\Traits\Reader;
}

$foo = new Foo();
$annotations = $foo->getClassAnnotations();

$annotations->get('get') 	  // > bool(true)
$annotations->get('post')     // > bool(true)
$annotations->get('delete')   // > bool(true)

$annotations->get('entity')   // > string(3) "bar"
$annotations->get('has-many') // > string(3) "Baz"

$annotations->get('export')   // > array(3){ [0] => "json" [1] => "xml" [2] => "csv" }
$annotations->get('max')      // > int(45)
$annotations->get('delta')    // > double(0.45)

$annotations->get('undefined')  // > null
```

Getting annotations from property and methods is easy too:

```php
$foo->getPropertyAnnotations('property_name');
$foo->getMethodAnnotations('method_name');
```

### Using the facade

The facade is useful when you want to inspect classes out of your logic domain:

```php
use Minime\Annotations\Facade;

Facade::getClassAnnotations('Full\Class\Name');
Facade::getPropertyAnnotations('Full\Class\Name', 'property_name');
Facade::getMethodAnnotations('Full\Class\Name', 'method_name');
```

### Grepping and traversing

Let's suppose you want to pick just a group of annotations:

```php
/**
 * @response.xml
 * @response.xls
 * @response.json
 * @response.csv
 * @method.get
 * @method.post
 */
class WebService
{
    use Minime\Annotations\Traits\Reader;
}

$annotations = (new WebService())->getClassAnnotations();

# grep all annotations starting with 'response.x'
$annotations->grep('^response.x')->export();
// > array(3){ ["@response.xml"] => TRUE, ["@response.xls"] => TRUE }

# or just "pipe" grep
$annotations->grep('^response')
			->grep('x')
			->export();

# traversing results
foreach($annotations->grep('^method') as $annotation => $value)
{
	// some behavior
}
```


## Currently Supports

* Class annotations
* Property annotations
* Method annotations
* A very convenient Trait
* Optional strong typed annotations: float, integer, string, json
* Grep annotations from a collection based on a regexp


## Coming Soon

* Annotations cache - any help?
* Possibility to inject a custom parser


## Copyright

Copyright (c) 2013 Márcio Almada. Distributed under the terms of an MIT-style license. See LICENSE for details.
