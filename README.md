# Trumbowyg editor field for Silverstripe

Decorate textarea fields with the Trumbowyg editor.

Supports:
+ content sanitising
+ restricted feature set by default (see below)

This field is not intended for use in the administration area (although PRs are welcome for that)

## Requirements

+ silverstripe/framework ^4
+ php-xml extension
+ Trumbowyg depends on jQuery (latest at time of release)

## Installation

```bash
composer require nswdpc/silverstripe-trumbowyg
```

## Usage

```php
use NSWDPC\Utilities\Trumbowyg\TrumboywgEditorField;

// TrumboywgEditorField extends TextareaField
$field = TrumboywgEditorField::create('MyEditorField', 'Write something')
            ->setDescription("This is a description")
            ->setRightTitle("This is a right title");
```

## License

[BSD-3-Clause](./LICENSE.md)

## Documentation

* [Documentation](./docs/en/001_index.md)

## Configuration

See [config.yml](./_config/config.yml) for module configuration values

## Maintainers

+ [dpcdigital@NSWDPC:~$](https://dpc.nsw.gov.au)

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
