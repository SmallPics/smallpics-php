<?php

declare(strict_types=1);

use smallpics\smallpics\enums\CropPosition;
use smallpics\smallpics\Options;

test('new values are returned by their getters', function (): void {
	$options = new Options([
		'w' => '65p',
		'height' => 12.5,
		'ar' => '16:9',
		'dpr' => 1.5,
		'crop' => 'face,top',
		'fp' => '25w:75h',
		'zoom' => 'face,2.5',
		'zoompad' => '10:20',
		'face' => 2,
		'debug' => true,
		'markpos' => '10p:20p',
		'markpad' => '5:10',
		'pixel' => 8,
	]);
	expect($options->getParam('w'))->toBe('65p')
		->and($options->getParam('h'))->toBe(12.5)
		->and($options->getParam('dpr'))->toBe(1.5)
		->and($options->getCropPosition())->toBe('face,top')
		->and($options->getCrop())->toBe('face,top')
		->and($options->getWidth())->toBe('65p')
		->and($options->getWatermarkPosition())->toBe('10p:20p')
		->and($options->getParam('markpos'))->toBe('10p:20p')
		->and($options->getPixelate())->toBe(8);
	$options->setCrop(width: 100, height: 80, x: 10, y: 20);
	expect($options->getCrop())->toBe([100, 80, 10, 20]);
	$options->setCropPosition(CropPosition::TOP);
	expect($options->getParam('crop'))->toBe('top');
});

test('passthrough is enabled by presence and false removes it from generated URLs', function (): void {
	$options = new Options([
		'passthrough' => true,
		'w' => 200,
	]);
	$builder = new \smallpics\smallpics\UrlBuilder('https://images.example.com');
	expect($options->getPassthrough())->toBeTrue()
		->and($builder->buildUrl('logo.svg', $options))->toBe('https://images.example.com/logo.svg?passthrough=1&w=200');
	$options->setPassthrough(false);
	expect($options->getPassthrough())->toBeFalse()
		->and($builder->buildUrl('logo.svg', $options))->toBe('https://images.example.com/logo.svg?w=200')
		->and((new Options([
			'passthrough' => false,
		]))->toString())->toBe('');
	$options->setParam('passthrough', '0');
	expect($options->getPassthrough())->toBeTrue();
});

test('all native parameters accept their short and long constructor names', function (): void {
	$values = [
		'or' => 90,
		'flip' => 'both',
		'crop' => 'facesarea,top',
		'w' => '65p',
		'h' => '50w',
		'ar' => '16:9',
		'fit' => 'crop',
		'dpr' => 1.5,
		'bri' => -10,
		'con' => 20,
		'gam' => 1.5,
		'sharp' => 10,
		'blur' => 5,
		'pixel' => 8,
		'filt' => 'grayscale',
		'mark' => 'logo.svg',
		'markorigin' => 'logos',
		'markw' => '20w',
		'markh' => '10h',
		'markfit' => 'crop',
		'markpad' => '10:20',
		'markpos' => '10p:20p',
		'markalpha' => 50,
		'bg' => 'lavender',
		'border' => '2p,fff,expand',
		'q' => 80,
		'fm' => 'avif',
		'interlace' => true,
		'fp' => '10p:20p',
		'zoom' => 'facesarea,2.5',
		'zoompad' => '5p:10p',
		'face' => 2,
		'debug' => true,
		'passthrough' => true,
	];
	foreach ($values as $key => $value) {
		foreach ([$key, Options::allOptions()[$key]] as $name) {
			$options = new Options([
				$name => $value,
			]);
			expect($options->getParam($key))->toBe($key === 'markfit' ? 'crop' : ($value === true ? '1' : $value));
		}
	}
});

test('dimension setters preserve old and new values through getters and URLs', function (): void {
	foreach ([
		'Width' => 'w',
		'Height' => 'h',
		'WatermarkWidth' => 'markw',
		'WatermarkHeight' => 'markh',
		'WatermarkPadding' => 'markpad',
	] as $name => $key) {
		foreach ([100, 12.5, '65p', '20w', '30h'] as $value) {
			$options = new Options();
			$options->{'set' . $name}($value);
			expect($options->{'get' . $name}())->toBe($value)
				->and($options->toString())->toBe($key . '=' . $value);
		}
	}
});

test('fluent setters accept current compound and numeric values', function (): void {
	$options = (new Options())->setWidth('65p')->setHeight(12.5)->setAspectRatio('16:9')->setDevicePixelRatio(1.5)
		->setWatermarkPosition(12.5)->setWatermarkPadding('5p:10h')->setBorder(2.5, 'fff', 'overlay');
	expect($options->getDevicePixelRatio())->toBe(1.5)
		->and($options->getAspectRatio())->toBe(1.7778)
		->and($options->getWatermarkPosition())->toBe(12.5)
		->and($options->toString())->toBe('w=65p&h=12.5&ar=16:9&dpr=1.5&markpos=12.5&markpad=5p:10h&border=2.5,fff,overlay');
	expect((new Options())->setAspectRatio(16, 9)->getAspectRatio())->toBe(1.7778)
		->and((new Options())->setDevicePixelRatio(2)->getDevicePixelRatio())->toBe(2);
	foreach (['top', 'face,top', 'facesarea,bottom', CropPosition::TOP] as $value) {
		$options->setCrop($value);
		expect($options->getCrop())->toBe($value instanceof CropPosition ? 'top' : $value);
	}

	$options->setCrop(width: 100, height: 80, x: 10, y: 20);
	expect($options->getCrop())->toBe([100, 80, 10, 20]);
});
