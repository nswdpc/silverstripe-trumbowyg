# Trumbowyg editor field for Silverstripe

Decorate textarea fields with the [Trumbowyg](https://github.com/Alex-D/Trumbowyg) editor.

Trumbowyg is "A lightweight and amazing WYSIWYG JavaScript editor - 20kB only (8kB gzip)"

It is useful for gathering content where some form of formatting in HTML is required.

This module supports:
+ content sanitising of submitted content (on the client side and server side)
+ restricted feature set by default ([see documentation](./docs/en/001_index.md))

The module will not support:
+ file uploads
+ image uploads
+ image insertion

Please use dedicated upload fields for that purpose.

This field is not intended for use in the administration area (although PRs are welcome for that)

## Requirements

+ silverstripe/framework ^4
+ php-xml extension
+ Trumbowyg depends on jQuery (latest at time of release)

The field pulls in required JS and CSS assets from [cdnjs.com](https://cdnjs.com) along with their respective Sub Resource Integrity hashes.

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
