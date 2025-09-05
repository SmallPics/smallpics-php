<?php

use smallpics\smallpics\UrlBuilder;

test('can build basic URL', function (): void {
	$options = createOptions([
		'width' => 300,
		'height' => 400,
		'fit' => 'fill',
		'watermarkPosition' => 'center',
	]);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?w=300&h=400&fit=fill&markpos=center');
});

test('can build a basic URL without any options', function (): void {
	$options = createOptions();

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?');
});

test('can build URL with null options', function (): void {
	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', null);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?');
});

test('can handle URLs with leading/trailing slashes', function (): void {
	$options = createOptions([
		'width' => 300,
	]);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('/images/image.jpg/', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?w=300');
});

test('can build URL with quality and format options', function (): void {
	$options = createOptions([
		'width' => 300,
		'height' => 400,
		'quality' => 85,
		'format' => 'webp',
	]);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?w=300&h=400&q=85&fm=webp');
});

test('can build URL with watermark options', function (): void {
	$options = createOptions([
		'width' => 300,
		'height' => 400,
		'watermarkPosition' => 'center',
		'watermarkAlpha' => 50,
	]);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?w=300&h=400&markpos=center&markalpha=50');
});

test('can build URL with enhancement options', function (): void {
	$options = createOptions([
		'width' => 300,
		'height' => 400,
		'brightness' => 10,
		'contrast' => 15,
		'sharpen' => 3,
		'blur' => 2,
	]);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?w=300&h=400&bri=10&con=15&sharp=3&blur=2');
});

test('can build URL with filter options', function (): void {
	$options = createOptions([
		'width' => 300,
		'height' => 400,
		'filter' => 'grayscale',
	]);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?w=300&h=400&filt=grayscale');
});

test('can build URL with crop options', function (): void {
	$options = createOptions();
	$options->setCrop(100, 200, 10, 20);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?crop=100,200,10,20');
});

test('can build URL with background and border options', function (): void {
	$options = createOptions();
	$options->setBackground('ffffff')
		->setBorder(5, 'ff0000', 'overlay');

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?bg=ffffff&border=5,ff0000,overlay');
});

test('can build URL with boolean options', function (): void {
	$options = createOptions();
	$options->setInterlaced(true);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?interlace=1');
});

test('can build URL with fit options', function (): void {
	$options = createOptions();
	$options->setFit('contain');

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?fit=contain');
});

test('can build URL with orientation and flip options', function (): void {
	$options = createOptions();
	$options->setOrientation(90)
		->setFlip('h');

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?or=90&flip=h');
});

test('can build URL with device pixel ratio', function (): void {
	$options = createOptions();
	$options->setDevicePixelRatio(2);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?dpr=2');
});

test('can build URL with gamma adjustment', function (): void {
	$options = createOptions();
	$options->setGamma(1.5);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?gam=1.5');
});

test('can build URL with pixelate effect', function (): void {
	$options = createOptions();
	$options->setPixelate(10);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?pixel=10');
});

test('can build URL with watermark path', function (): void {
	$options = createOptions();
	$options->setWatermarkPath('/watermarks/logo.png');

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?mark=/watermarks/logo.png');
});

test('can build URL with watermark dimensions', function (): void {
	$options = createOptions();
	$options->setWatermarkWidth(100)
		->setWatermarkHeight(50);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?markw=100&markh=50');
});

test('can build URL with watermark offsets and padding', function (): void {
	$options = createOptions();
	$options->setWatermarkXOffset(10)
		->setWatermarkYOffset(20)
		->setWatermarkPadding(5);

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?markx=10&marky=20&markpad=5');
});

test('can build URL with watermark fit', function (): void {
	$options = createOptions();
	$options->setWatermarkFit('contain');

	$builder = new UrlBuilder('https://images.example.com');
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?markfit=contain');
});

test('can generate signed URLs', function (): void {
	$options = createOptions([
		'width' => 300,
		'height' => 400,
	]);

	$secret = 'my-secret-value';

	$builder = new UrlBuilder('https://images.example.com', $secret);
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?w=300&h=400&signature=Qaqt9agh3JLg4LeWmRqqd9qnD9bjPbVqSzrXAXTaY');
});

test('can generate complex signed URLs', function (): void {
	$options = createOptions();
	$options
		->setBorder(5, 'ff0000', 'overlay')
		->setQuality(80)
		->setFormat('png');

	$secret = 'my-secret-value';

	$builder = new UrlBuilder('https://images.example.com', $secret);
	$url = $builder->buildUrl('images/image.jpg', $options);

	expect($url)->toBe('https://images.example.com/t/images/image.jpg?border=5,ff0000,overlay&q=80&fm=png&signature=hE2e5tLayrNP0pgSaxJLFOMfi0PES2erIzhNYR7QqQ');
});

test('generates signature with unicode characters', function (): void {
	$options = createOptions()
		->setOrigin('main')
		->setFormat('avif')
		->setQuality(90)
		->setWidth(600)
		->setHeight(600)
		->setFit(\smallpics\smallpics\enums\Fit::CONTAIN);

	$secret = 'my-secret-value';

	$builder = new UrlBuilder('https://images.example.com', $secret);
	$url = $builder->buildUrl('images/unicode-%E9%BD%90%E8%89%B2-0.png', $options);

	expect($url)->toBe('https://images.example.com/t/images/unicode-%E9%BD%90%E8%89%B2-0.png?origin=main&fm=avif&q=90&w=600&h=600&fit=contain&signature=YjnlgXrNQXqmrfa2qPxXhbmSqgQMvwMjGoQH9YBQxs');
});
