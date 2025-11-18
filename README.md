# smallpics-php

A PHP client library for generating URLs with [Small Pics](https://www.smallpics.io). This package provides a simple way to build URLs with Small Pics processing options.

## Installation

```bash
composer require smallpics/smallpics-php
```

## Usage

### Creating Processing Options

```php
use smallpics\smallpics\Options;

// Create a new Options object
$options = new Options();

// Set options with individual setters
$options
    ->setWidth(300)
    ->setHeight(400);

// Generate the query string parameters
echo $options->toString();
// Output: "width=300&height=400"

// Options object can also be used directly in a string context
echo $options;
// Output: "width=300&height=400"
```

### Alternative Initialization

It is possible to initialize the Options object with an associative array. The keys of the array are the option names and the values are the option values.

The values can either be a single value, an array of values, or an associative array of values.

- When a single value is used, it is passed as the first argument to the setter method.
- When an array of values is used, they are destructured as passed as arguments in order to the setter method.
- When an associative array is used, the key/value pairs are destructured and passed in as named arguments to the setter method.

```php
// Create Options object with array of values
$options = new Options([
		'width' => 300,
		'height' => 400,
		'fit' => 'fill',
		'watermarkPosition' => 'center',
		'border' => [
			'width' => 10,
			'color' => '000000',
			'borderMethod' => 'overlay',
		],
	]);
```

### Building URLs

```php
use smallpics\smallpics\Options;
use smallpics\smallpics\UrlBuilder;

// Create options object
$options = new Options();
$options
    ->setQuality(80)
    ->setFormat('png');

// Create URL builder (without signing, with plain URLs)
$builder = new UrlBuilder('https://images.my-origin.com', null, null, false);

// Build URL
$imageUrl = 'https://example.com/images/image.jpg';
$url = $builder->buildUrl($imageUrl, $options);

echo $url;
// Output: https://images.my-origin.com/images/image.jpg?q=80&fm=png
```

### Signed URLs

```php
// Create URL builder with signing keys
$secret = '0123456789abcdef0123456789abcdef';
$builder = new UrlBuilder('https://images.my-origin.com', $secret);

// Build signed URL
$imageUrl = 'images/image.jpg';
$url = $builder->buildUrl($imageUrl, $options);

echo $url;
// Output will include a signature: https://images.my-origin.com/images/image.jpg?signature=[signature]
```

## Development

### Testing

This package uses [Pest PHP](https://pestphp.com/) for testing. To run the tests:

```bash
composer test
```

or

```bash
vendor/bin/pest
```
