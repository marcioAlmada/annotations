# Changelog

## 1.0.0

## 1.2.3

## 1.3.3

* First class namespaced annotations support with the new `AnnotationsBag::useNamespace` method.
* `AnnotationsBag::grepNamespace` is now just an alias for `AnnotationsBag::useNamespace` and will be removed in version ~2.0

## 1.4.3

* Changed some method permissions in parser from private to protected to facilitate reuse
* Preparing for future parser injection

## 1.5.3

* AnnotationsBag is now countable (thanks to super @nyamsprod)
* AnnotationsBag::grep is now optimized
* Changed some method permissions in parser from private to protected to facilitate reuse
* Code is now more PSR-2 compliant to keep further collaboration easier