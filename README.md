Minime\Annotations
==================

[![Build Status](https://travis-ci.org/marcioAlmada/minime.annotations.png?branch=master)](https://travis-ci.org/marcioAlmada/minime.annotations)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/minime.annotations/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/minime.annotations?branch=master)

A lightweight dependency free PHP annotations library.

Using as a trait:

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
$annotations->get('export')   // > array(3){ [0] => string(4) "json" [1] => string(3) "xml" [2] => string(3) "csv" }
$annotations->get('max')      // > int(45)
$annotations->get('delta')    // > double(0.45)
```