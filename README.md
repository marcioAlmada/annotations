Minime Annotations
==================

[![Build Status](https://travis-ci.org/marcioAlmada/minime.annotations.png?branch=master)](https://travis-ci.org/marcioAlmada/minime.annotations)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/minime.annotations/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/minime.annotations?branch=master)

A lightweight (dependency free) PHP annotations library. Minime Annotations is intended to be dynamic.

## Currently Supports

* Class annotations
* Property annotations
* Method annotations
* Annotations reader trait for convenience
* Typed annotations (float, integer, string)
* Freedom (no auxiliary class for each annotation you define)

## Coming Soon

* Cache - any help?
* Maybe I'll add more interesting annotation types (bin, regex)

## Basic Usage

### Using as a trait

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

### Using as a facade

```php
$annotations = Minime\Annotations\Load::fromClass('Full\Qualified\Class\Name');
```