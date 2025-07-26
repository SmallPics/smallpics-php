<?php


test('can set options with individual setters', function (): void {
	$options = createOptions();
	$options->setWidth(300)
		->setHeight(400)
		->setFit('fill')
		->setWatermarkPosition('center');

	expect($options)->toBeOptions();
	expect($options->toString())->toBe('w=300&h=400&fit=fill&markpos=center');
});

test('can set options with constructor array', function (): void {
	$options = createOptions([
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

	expect($options)->toBeOptions();
	expect($options->toString())->toBe('w=300&h=400&fit=fill&markpos=center&border=10,000000,overlay');
});

test('can handle complex options', function (): void {
	$options = createOptions();
	$options->setSharpen(5)
		->setFit('fill')
		->setWatermarkPosition('center')
		->setWatermarkAlpha(50)
		->setQuality(80)
		->setFormat('png')
		->setBorder(10, '000000', 'overlay');

	expect($options)->toBeOptions();
	expect($options->toString())->toBe('sharp=5&fit=fill&markpos=center&markalpha=50&q=80&fm=png&border=10,000000,overlay');
});

test('magic __toString behaves like toString method', function (): void {
	$options = createOptions();
	$options->setSharpen(5)
		->setFit('fill');

	expect($options->toString())->toBe((string) $options);
});

test('boolean values are converted to 1 and 0', function (): void {
	$options = createOptions();
	$options->setInterlaced(true);

	expect($options->toString())->toBe('interlace=1');

	$options->setInterlaced(false);
	expect($options->toString())->toBe('interlace=0');
});

test('watermark methods work correctly', function (): void {
	$options = createOptions();

	// Test watermark path
	$watermarkPath = '/path/to/watermark.png';
	$options->setWatermarkPath($watermarkPath);
	expect($options->getWatermarkPath())->toBe($watermarkPath);

	// Test watermark position
	$options->setWatermarkPosition('center');
	expect($options->getWatermarkPosition()->value)->toBe('center');

	// Test watermark alpha
	$options->setWatermarkAlpha(50);
	expect($options->getWatermarkAlpha())->toBe(50);
});

test('basic image transformation options work', function (): void {
	$options = createOptions();

	// Test basic dimensions
	$options->setWidth(300)->setHeight(400);
	expect($options->getWidth())->toBe(300);
	expect($options->getHeight())->toBe(400);

	// Test quality
	$options->setQuality(85);
	expect($options->getQuality())->toBe(85);

	// Test format
	$options->setFormat('webp');
	expect($options->getFormat()->value)->toBe('webp');
});

test('filter and enhancement options work', function (): void {
	$options = createOptions();

	// Test filter
	$options->setFilter('grayscale');

	expect($options->getFilter()->value)->toBe('grayscale');

	// Test brightness
	$options->setBrightness(10);
	expect($options->getBrightness())->toBe(10);

	// Test contrast
	$options->setContrast(15);
	expect($options->getContrast())->toBe(15);

	// Test gamma
	$options->setGamma(1.2);
	expect($options->getGamma())->toBe(1.2);

	// Test sharpen
	$options->setSharpen(3);
	expect($options->getSharpen())->toBe(3);

	// Test blur
	$options->setBlur(2);
	expect($options->getBlur())->toBe(2);

	// Test pixelate
	$options->setPixelate(5);
	expect($options->getPixelate())->toBe(5);
});

test('crop and fit options work correctly', function (): void {
	$options = createOptions();

	// Test crop
	$options->setCrop(100, 200, 10, 20);

	expect($options->getCrop())->toBe([100, 200, 10, 20]);

	// Test fit
	$options->setFit('contain');
	expect($options->getFit()->value)->toBe('contain');
});

test('background and border options work', function (): void {
	$options = createOptions();

	// Test background
	$options->setBackground('ffffff');

	expect($options->getBackground())->toBe('ffffff');

	// Test border
	$options->setBorder(5, 'ff0000', 'overlay');
	$border = $options->getBorder();
	expect($border[0])->toBe('5');
	expect($border[1])->toBe('ff0000');
	expect($border[2]->value)->toBe('overlay');
});
