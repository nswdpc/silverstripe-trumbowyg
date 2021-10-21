# Trumbowyg editor field for Silverstripe

Decorate textarea fields with the [Trumbowyg](https://github.com/Alex-D/Trumbowyg) editor.

This module supports:
+ content sanitising of submitted content on the client side using Trumbowyg configuration rules and server side using [HTMLPurifier](https://github.com/ezyang/htmlpurifier))
+ restricted feature set by default ([see documentation](./docs/en/001_index.md))

## Use cases

This editor field is useful for gathering content where some form of formatting in HTML is required. It is not intended for use in the administration area (although PRs are welcome for that, for example a restricted content editing field)

As the goal is only a restricted feature set for simple content submissions, the module will not support:

+ file uploads
+ image uploads
+ image insertion

Please use dedicated upload fields for handling file uploads.

## Requirements

Per [composer.json](/composer.json):

+ silverstripe/framework ^4
+ jQuery 3.6.0

The field pulls in required Trumbowyg JS and CSS assets from [cdnjs.com](https://cdnjs.com) along with their respective Sub Resource Integrity (SRI) hashes.

If you wish to use your own jQuery, set the  `TrumboywgEditorField.use_own_jquery` configuration value to `false` in your project configuration. When false, the module will not include its own jQuery.

## Installation

```shell
composer require nswdpc/silverstripe-trumbowyg
```

## Usage

See [the basic example](./docs/en/001_index.md#basic-example)

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
