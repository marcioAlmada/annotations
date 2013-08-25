Minime\Annotations
==================

[![Build Status](https://travis-ci.org/marcioAlmada/minime.annotations.png?branch=master)](https://travis-ci.org/marcioAlmada/minime.annotations)
[![Coverage Status](https://coveralls.io/repos/marcioAlmada/minime.annotations/badge.png?branch=master)](https://coveralls.io/r/marcioAlmada/minime.annotations?branch=master)

A lightweight dependency free PHP annotations library.

Using as a trait:

```php
/**
 * @entity bar
 * @has-many Baz
 */
class Foo {
    use Minime\Annotations\Traits\Reader;
}

$foo = new Foo();
$foo->getClassAnnotations()->get('entity')   // "bars"
$foo->getClassAnnotations()->get('has-many') // "Baz"
```