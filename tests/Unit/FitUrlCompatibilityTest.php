<?php

declare(strict_types=1);

use smallpics\smallpics\enums\CropPosition;
use smallpics\smallpics\enums\Fit;
use smallpics\smallpics\Options;
use smallpics\smallpics\UrlBuilder;

test('legacy and current fit calls produce identical URLs with current parameters', function (string $method, string $query): void {
	$legacy = (new Options())->{$method}(Fit::CROP, CropPosition::TOP, 25, 75, 2);
	$current = (new Options())->{$method}('crop')
		->setCropPosition('top')
		->setFocalPoint('25p', '75p')
		->setZoom(2);
	$builder = new UrlBuilder('https://images.example.com');
	$expected = 'https://images.example.com/bird.jpg?' . $query;

	expect($builder->buildUrl('bird.jpg', $legacy))->toBe($expected)
		->and($builder->buildUrl('bird.jpg', $current))->toBe($expected);
})->with([
	'image fit' => ['setFit', 'crop=top&fit=crop&fp=25p:75p&zoom=2'],
	'watermark fit' => ['setWatermarkFit', 'crop=top&fp=25p:75p&markfit=crop&zoom=2'],
]);
