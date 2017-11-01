# Palmtree WordPress NavMenu

WordPress nav menu component for Palmtree PHP

Includes a custom walker to output WordPress menus as Bootstrap v4 nav bars and a collection class which takes care
of registering and outputting menus.

## Requirements
* PHP >= 5.6

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/wp-cleaner
```

## Usage
```php
<?php
$collection = new \Palmtree\WordPress\NavMenu\NavMenuCollection();
$collection->set('header', 'Header Nav');
```

```php
<nav class="navbar navbar-default"><?php echo $collection->renderMenu('header'); ?></nav>
```

## License

Released under the [MIT license](LICENSE)
