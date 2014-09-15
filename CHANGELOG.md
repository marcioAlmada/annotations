#Changelog
All Notable changes to `minime/annotations` package will be documented in this file

## NEXT - YYYY-MM-DD

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Remove
- Nothing

### Security
- Nothing

## 2.0.0 - 2014-09-14

### Added
- Formal spec to facilitate further improvements
- Basic built in cache support
- Better DI
- `Minime\Annotations\Reader` is now a centralized class to read annotations
- `Minime\Annotations\Parser::registerType` allows new types to be added at runtime
- `Minime\Annotations\Parser::unregisterType` allows types to be removed at runtime
- `Minime\Annotations\DynamicParser` is a very basic parser without types support

### Deprecated
- Nothing

### Fixed
- `Minime\Annotations\AnnotationsBag::export` was renamed to `Minime\Annotations\AnnotationsBag::toArray` #46
- `Minime\Annotations\AnnotationsBag::grep` now receives a full regexp instead of a fragment of regexp

### Remove
- `Minime\Annotations\AnnotationsBag::merge` #28
- Traits support
- Static facade support
- Eval type from default parser

### Security
- Nothing

## 1.0.0 - 2013-09-07

Initial Release of `minime/annotations` package
