Minime Annotations
==================

[![Build Status](https://travis-ci.org/marcioAlmada/minime.annotations.png?branch=master)](https://travis-ci.org/marcioAlmada/minime.annotations)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/minime.annotations/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/minime.annotations?branch=master)

A lightweight (dependency free) PHP annotations library. Minime Annotations is intended to be dynamic.

## Currently Supports

* Class annotations
* Property annotations
* Method annotations
* Annotations reader trait, just for convenience
* (optional) Strong typed annotations (float, integer, string)
* Freedom (no auxiliary class for each annotation you define)
* Grep annotations from a colletion of annotations based on a regexp

## Basic Usage

### Using as a trait

The trait approach is useful for self / internal reflection:

```php
/**
 * @post @get
 * @entity bar
 * @has-many Baz
 * @export ["json", "xml", "csv"]
 * @max integer 45
 * @delta float .45
 */
class Foo
{
    use Minime\Annotations\Traits\Reader;
}

$foo = new Foo();
$annotations = $foo->getClassAnnotations();
$annotations->get('post')     // > bool(true)
$annotations->get('get') 	  // > bool(false)
$annotations->get('entity')   // > string(3) "bar"
$annotations->get('has-many') // > string(3) "Baz"
$annotations->get('export')   // > array(3){ [0] => "json" [1] => "xml" [2] => "csv" }
$annotations->get('max')      // > int(45)
$annotations->get('delta')    // > double(0.45)
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

## Coming Soon

* Annotations cache - any help?
* Possibility to inject a custom parser
* Maybe add more interesting annotation types (like json):

```php
/**
 * @config json { "x" : -30, "y" : 20, "z" : 45 }
 */
```

## Copyright

Copyright (c) 2013 Márcio Almada. Distributed under the terms of an MIT-style license. See LICENSE for details.