# Acorn FSE Helper

![Latest Stable Version](https://img.shields.io/packagist/v/roots/acorn-fse-helper.svg?style=flat-square)
[![Packagist Downloads](https://img.shields.io/packagist/dt/roots/acorn-fse-helper?label=downloads&colorB=2b3072&colorA=525ddc&style=flat-square)](https://packagist.org/packages/roots/acorn-fse-helper)
![Build Status](https://img.shields.io/github/actions/workflow/status/roots/acorn-fse-helper/main.yml?branch=main&style=flat-square)
[![Follow Roots](https://img.shields.io/badge/follow%20@rootswp-1da1f2?logo=twitter&logoColor=ffffff&message=&style=flat-square)](https://twitter.com/rootswp)
[![Sponsor Roots](https://img.shields.io/badge/sponsor%20roots-525ddc?logo=github&style=flat-square&logoColor=ffffff&message=)](https://github.com/sponsors/roots)

Acorn FSE Helper provides an easy way to initialize and work with block templates in themes powered by Acorn.

## Support us

We're dedicated to pushing modern WordPress development forward through our open source projects, and we need your support to keep building. You can support our work by purchasing [Radicle](https://roots.io/radicle/), our recommended WordPress stack, or by [sponsoring us on GitHub](https://github.com/sponsors/roots). Every contribution directly helps us create better tools for the WordPress ecosystem.

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

### Vite Asset Integration

Acorn FSE Helper can automatically inject Vite assets (CSS and JavaScript) into the `<head>` of your FSE theme.

To enable this feature:

1. Publish the configuration file:
   ```bash
   $ wp acorn vendor:publish --tag=fse-config
   ```

2. Enable Vite asset injection in `config/fse.php`:
   ```php
   'vite_enabled' => true,
   ```

By default, it includes:
- `resources/css/app.css`
- `resources/js/app.js`

You can customize the entry points using the `acorn/fse/vite_entrypoints` filter:

```php
add_filter('acorn/fse/vite_entrypoints', function ($entryPoints) {
    return [
        'resources/css/app.css',
        'resources/css/editor.css',
        'resources/js/app.js',
        'resources/js/custom.js',
    ];
});
```

## Community

Keep track of development and community news.

- Join us on Discord by [sponsoring us on GitHub](https://github.com/sponsors/roots)
- Join us on [Roots Discourse](https://discourse.roots.io/)
- Follow [@rootswp on Twitter](https://twitter.com/rootswp)
- Follow the [Roots Blog](https://roots.io/blog/)
- Subscribe to the [Roots Newsletter](https://roots.io/subscribe/)
