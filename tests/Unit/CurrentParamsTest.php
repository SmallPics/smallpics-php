<?php

declare(strict_types=1);

use smallpics\smallpics\enums\CropPosition;
use smallpics\smallpics\enums\Fit;
use smallpics\smallpics\Options;

test('unaffected public Options signatures remain unchanged', function (): void {
	$expected = json_decode(file_get_contents(__DIR__ . '/../fixtures/options-public-api.json'), true);
	$source = file_get_contents(__DIR__ . '/../../src/Options.php');
	preg_match_all('/(public (?:static )?function (\w+)\([^\n]+)/', $source, $matches, PREG_SET_ORDER);
	$actual = [];
	foreach ($matches as $match) {
		$actual[$match[2]] = $match[1];
	}

	foreach ($expected as $method => $signature) {
		if (in_array($method, ['setCrop', 'getCrop', 'setWidth', 'getWidth', 'setHeight', 'getHeight', 'setAspectRatio', 'setDevicePixelRatio', 'getDevicePixelRatio', 'setWatermarkWidth', 'getWatermarkWidth', 'setWatermarkHeight', 'getWatermarkHeight', 'setWatermarkXOffset', 'getWatermarkXOffset', 'setWatermarkYOffset', 'getWatermarkYOffset', 'setWatermarkPadding', 'getWatermarkPadding', 'setWatermarkPosition', 'getWatermarkPosition', 'setBorder'], true)) {
			continue;
		}

		expect($actual[$method] ?? null)->toBe($signature);
	}
});

test('legacy fit arguments accept current crop position enums', function (): void {
	$options = (new Options())->setFit(Fit::COVER, CropPosition::TOP_LEFT);
	expect(CropPosition::TOP_LEFT->value)->toBe('top-left')
		->and($options->getParam('fit'))->toBe('crop')
		->and($options->getCropPosition())->toBe('top-left')
		->and($options->getFit())->toBe([Fit::CROP, null, null, null, null]);
	$options->setWatermarkFit(fit: Fit::CROP, focalPointX: 25, focalPointY: 75, zoom: 2);
	expect($options->getParam('markfit'))->toBe('crop')
		->and($options->getFocalPoint())->toBe('25p:75p')
		->and($options->getZoom())->toBe(2);
	$options->setWatermarkFit(Fit::COVER, CropPosition::BOTTOM_RIGHT);
	expect($options->getParam('markfit'))->toBe('crop')
		->and($options->getCropPosition())->toBe('bottom-right');
});

test('legacy fit strings retain current overrides in either option order', function (): void {
	foreach (['crop-25-75-2.5', 'crop-top', 'cover-top-left', 'cover'] as $fit) {
		foreach ([false, true] as $reverse) {
			$config = [
				'fit' => $fit,
				'crop' => 'bottom',
				'fp' => '10:20',
				'zoom' => 3,
			];
			$options = new Options($reverse ? array_reverse($config, true) : $config);
			expect($options->getParam('fit'))->toBe('crop')
				->and($options->getParam('crop'))->toBe('bottom')
				->and($options->getFocalPoint())->toBe('10:20')
				->and($options->getZoom())->toBe(3);
		}
	}
});

test('legacy watermark offsets remain separate from current padding and coordinates', function (): void {
	foreach ([false, true] as $reverse) {
		$config = [
			'markx' => '5w',
			'marky' => 10,
			'markpad' => '2:3',
			'markpos' => '20:30',
		];
		$options = new Options($reverse ? array_reverse($config, true) : $config);
		expect($options->getWatermarkXOffset())->toBe('5w')
			->and($options->getWatermarkYOffset())->toBe(10)
			->and($options->getWatermarkPadding())->toBe('2:3')
			->and($options->getParam('markpos'))->toBe('20:30');
	}
});

test('legacy fit strings emit current parameters for both setters', function (): void {
	foreach ([
		'setFit' => 'fit',
		'setWatermarkFit' => 'markfit',
	] as $method => $key) {
		foreach ([
			'crop-top' => 'top',
			'cover-bottom-right' => 'bottom-right',
		] as $fit => $position) {
			$options = (new Options())->{$method}($fit);
			expect($options->toString())->toBe($key . '=crop&crop=' . $position);
		}

		$options = (new Options())->{$method}('crop-25-75-2.5');
		expect($options->toString())->toBe($key . '=crop&fp=25p:75p&zoom=2.5');
	}
});

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
		'markfit' => 'crop-top',
		'markx' => '5p',
		'marky' => 20,
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

test('legacy anchors retain source rectangles in either constructor order', function (): void {
	foreach ([false, true] as $reverse) {
		$config = [
			'fit' => ['cover', 'cover-top-left'],
			'crop' => [100, 80, 10, 20],
		];
		$options = new Options($reverse ? array_reverse($config, true) : $config);
		expect($options->getParam('fit'))->toBe('crop')
			->and($options->getCrop())->toBe([100, 80, 10, 20]);
	}
});

test('format filter and border aliases remain accepted', function (): void {
	foreach ([
		'jpeg' => 'jpg',
	] as $alias => $format) {
		expect((new Options([
			'fm' => $alias,
		]))->getFormat()->value)->toBe($format);
	}

	expect((new Options([
		'filt' => 'greyscale',
	]))->getFilter()->value)->toBe('grayscale')
		->and((new Options([
			'border' => '5,fff,pad',
		]))->getParam('border'))->toBe('5,fff,expand')
		->and((new Options())->setBorder(5, 'fff', \smallpics\smallpics\enums\BorderMethod::PAD)->getParam('border'))->toBe('5,fff,expand');
});

test('plain cover emits crop through constructors and fit setters', function (): void {
	foreach (['cover', Fit::COVER] as $value) {
		foreach ([
			'fit' => 'fit',
			'markfit' => 'markfit',
			'watermarkFit' => 'markfit',
		] as $name => $key) {
			expect((new Options([
				$name => $value,
			]))->getParam($key))->toBe('crop');
		}

		expect((new Options())->setFit($value)->getParam('fit'))->toBe('crop')
			->and((new Options())->setWatermarkFit($value)->getParam('markfit'))->toBe('crop');
	}
});


test('dimension setters preserve old and new values through getters and URLs', function (): void {
	foreach ([
		'Width' => 'w',
		'Height' => 'h',
		'WatermarkWidth' => 'markw',
		'WatermarkHeight' => 'markh',
		'WatermarkXOffset' => 'markx',
		'WatermarkYOffset' => 'marky',
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


test('legacy fit arguments emit current params and retain explicit overrides', function (): void {
	foreach ([
		'setFit' => 'fit',
		'setWatermarkFit' => 'markfit',
	] as $method => $key) {
		$options = (new Options())->{$method}(Fit::CROP, CropPosition::TOP, 25, 75, 2);
		expect($options->toString())->toBe($key . '=crop&crop=top&fp=25p:75p&zoom=2');
		foreach ([false, true] as $reverse) {
			$config = [
				$key => ['crop', 'top', 25, 75, 2],
				'crop' => 'bottom',
				'fp' => '10:20',
				'zoom' => 3,
			];
			$options = new Options($reverse ? array_reverse($config, true) : $config);
			expect($options->getParam($key))->toBe('crop')
				->and($options->getCropPosition())->toBe('bottom')
				->and($options->getFocalPoint())->toBe('10:20')
				->and($options->getZoom())->toBe(3);
		}
	}
});


test('crop position enums use current values and legacy strings still normalize', function (): void {
	foreach (CropPosition::cases() as $position) {
		expect($position->value)->not->toStartWith('cover-');
		foreach ([$position, $position->value, 'cover-' . $position->value] as $value) {
			expect((new Options())->setCropPosition($value)->getCropPosition())->toBe($position->value);
		}

		foreach ([
			'fit' => 'getFit',
			'markfit' => 'getWatermarkFit',
		] as $key => $getter) {
			$options = (new Options())->setParam($key, 'cover-' . $position->value);
			expect($options->{$getter}())->toBe([Fit::COVER, $position, null, null, null]);
		}
	}
});
