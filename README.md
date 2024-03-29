# Acorn FSE Helper

![Latest Stable Version](https://img.shields.io/packagist/v/roots/acorn-fse-helper.svg?style=flat-square)
![Total Downloads](https://img.shields.io/packagist/dt/roots/acorn-fse-helper.svg?style=flat-square)
![Build Status](https://img.shields.io/github/actions/workflow/status/roots/acorn-fse-helper/main.yml?branch=main&style=flat-square)

Acorn FSE Helper provides an easy way to initialize and work with block templates in themes powered by Acorn.

## Requirements

- [PHP](https://secure.php.net/manual/en/install.php) >= 8.1
- [Acorn](https://github.com/roots/acorn) >= 4.2

## Installation

Install via Composer:

```sh
$ composer require roots/acorn-fse-helper
```

## Getting Started

Once installed, begin by initializing full-site editing in your theme using Acorn's CLI:

```php
$ wp acorn fse:init
```

Initializing ensures your current activated theme supports `block-templates` as well as provides you with the option to publish initial stubs to get started with.

## Usage

Once initialized, any block templates located in `templates/` will be given priority over existing Blade views.

### Blade Directives

To assist with hybrid theme development, Acorn FSE Helper includes a few useful Blade directives out of the box for working with blocks inside of views.

#### `@blocks`

The `@blocks` directive allows you to render raw block markup inside of a view using [`do_blocks()`](https://developer.wordpress.org/reference/functions/do_blocks/):

```php
@blocks
  <!-- wp:paragraph {"align":"center"} -->
  <p>Lorem ipsum...</p>
  <!-- /wp:paragraph -->
@endblocks
```

#### `@blockpart`

The `@blockpart` directive provides a convenient way to render block template parts inside of your views using [`block_template_part()`](https://developer.wordpress.org/reference/functions/block_template_part/):

```php
@blockpart('header')
```

To render multiple template parts at once, you may pass an array in the order you wish them to be rendered in:

```php
@blockpart(['header', 'footer'])
```

## Bug Reports

If you discover a bug in Acorn FSE Helper, please [open an issue](https://github.com/roots/acorn-fse-helper/issues).

## Contributing

Contributing whether it be through PRs, reporting an issue, or suggesting an idea is encouraged and appreciated.

## License

Acorn FSE Helper is provided under the [MIT License](LICENSE.md).
