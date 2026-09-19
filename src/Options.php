<?php

namespace smallpics\smallpics;

use smallpics\smallpics\enums\BorderMethod;
use smallpics\smallpics\enums\CropPosition;
use smallpics\smallpics\enums\Filter;
use smallpics\smallpics\enums\Fit;
use smallpics\smallpics\enums\Format;
use smallpics\smallpics\enums\WatermarkPosition;

class Options implements \Stringable
{
	/**
	 * @var string
	 */
	public const ORIENTATION = 'or';

	/**
	 * @var string
	 */
	public const FLIP = 'flip';

	/**
	 * @var string
	 */
	public const CROP = 'crop';

	public const FOCAL_POINT = 'fp';

	public const ZOOM = 'zoom';

	public const ZOOM_PADDING = 'zoompad';

	public const FACE = 'face';

	public const DEBUG = 'debug';

	public const PASSTHROUGH = 'passthrough';

	/**
	 * @var string
	 */
	public const WIDTH = 'w';

	/**
	 * @var string
	 */
	public const HEIGHT = 'h';

	/**
	 * @var string
	 */
	public const ASPECT_RATIO = 'ar';

	/**
	 * @var string
	 */
	public const FIT = 'fit';

	/**
	 * @var string
	 */
	public const DEVICE_PIXEL_RATIO = 'dpr';

	/**
	 * @var string
	 */
	public const BRIGHTNESS = 'bri';

	/**
	 * @var string
	 */
	public const CONTRAST = 'con';

	/**
	 * @var string
	 */
	public const GAMMA = 'gam';

	/**
	 * @var string
	 */
	public const SHARPEN = 'sharp';

	/**
	 * @var string
	 */
	public const BLUR = 'blur';

	/**
	 * @var string
	 */
	public const PIXELATE = 'pixel';

	/**
	 * @var string
	 */
	public const FILTER = 'filt';

	/**
	 * @var string
	 */
	public const WATERMARK_PATH = 'mark';

	/**
	 * @var string
	 */
	public const WATERMARK_ORIGIN = 'markorigin';

	/**
	 * @var string
	 */
	public const WATERMARK_WIDTH = 'markw';

	/**
	 * @var string
	 */
	public const WATERMARK_HEIGHT = 'markh';

	/**
	 * @var string
	 */
	public const WATERMARK_FIT = 'markfit';

	/**
	 * @var string
	 */
	public const WATERMARK_FOCAL_POINT = 'markfp';

	/**
	 * @var string
	 */
	public const WATERMARK_ZOOM = 'markzoom';

	/**
	 * @var string
	 */
	public const WATERMARK_PADDING = 'markpad';

	/**
	 * @var string
	 */
	public const WATERMARK_POSITION = 'markpos';

	/**
	 * @var string
	 */
	public const WATERMARK_ALPHA = 'markalpha';

	/**
	 * @var string
	 */
	public const BACKGROUND = 'bg';

	/**
	 * @var string
	 */
	public const BORDER = 'border';

	/**
	 * @var string
	 */
	public const QUALITY = 'q';

	/**
	 * @var string
	 */
	public const FORMAT = 'fm';

	/**
	 * @var string
	 */
	public const INTERLACE = 'interlace';

	/**
	 * Stores all set options and their values
	 *
	 * @var array<non-empty-string, int|float|string>
	 */
	protected array $options = [];

	/**
	 * Option separator for the URL
	 */
	protected string $optionSeparator = '&';

	/**
	 * @param array<string, mixed> $options
	 */
	public function __construct(array $options = [])
	{
		foreach ($options as $option => $value) {
			$option = self::allOptions()[$option] ?? $option;
			$method = 'set' . $this->toPascalCase($option);
			if ($method === 'setBorder' && is_string($value)) {
				$value = explode(',', $value);
				$value[2] ??= BorderMethod::OVERLAY;
			}

			if (method_exists($this, $method)) {
				// We want to make sure that we can set the individual arguments from an associative array or a regular array.
				if (is_array($value)) {
					if ($method === 'setParams') {
						$this->setParams($value);
						continue;
					}

					$this->{$method}(...$value);
				} else {
					$this->{$method}($value);
				}
			}
		}
	}

	/**
	 * Magic method to convert object to string
	 */
	public function __toString(): string
	{
		return $this->toString();
	}

	/**
	 * Get the string representation of all options
	 */
	public function toString(): string
	{
		$options = [];
		foreach ($this->options as $key => $value) {
			$options[] = "{$key}={$value}";
		}

		return implode($this->optionSeparator, $options);
	}

	/**
	 * Set orientation
	 *
	 * @param 0|90|180|270|'auto' $orientation
	 */
	public function setOrientation(int|string $orientation): self
	{
		$this->options[self::ORIENTATION] = $orientation;
		return $this;
	}

	/**
	 * Get orientation
	 */
	public function getOrientation(): null|int|string
	{
		/** @var null|int|string $value */
		$value = $this->options[self::ORIENTATION] ?? null;

		return $value;
	}

	/**
	 * Set flip
	 *
	 * @param 'v'|'h'|'both' $flip
	 */
	public function setFlip(string $flip): self
	{
		$this->options[self::FLIP] = $flip;
		return $this;
	}

	/**
	 * Get flip
	 */
	public function getFlip(): null|string
	{
		/** @var null|string $value */
		$value = $this->options[self::FLIP] ?? null;

		return $value;
	}

	public function setFit(string|Fit $fit): self
	{
		$this->options[self::FIT] = ($fit instanceof Fit ? $fit : Fit::from($fit))->value;
		return $this;
	}

	public function getFit(): ?Fit
	{
		$value = $this->options[self::FIT] ?? null;
		return $value === null ? null : Fit::from((string) $value);
	}

	public function setCropPosition(string|CropPosition $position): self
	{
		$value = $position instanceof CropPosition ? $position->value : $position;
		$this->options[self::CROP] = $value;
		return $this;
	}

	public function getCropPosition(): ?string
	{
		$value = $this->options[self::CROP] ?? null;
		return is_string($value) && count(explode(',', $value)) !== 4 ? $value : null;
	}

	public function setFocalPoint(int|float|string $value, int|float|string|null $y = null): self
	{
		$this->options[self::FOCAL_POINT] = $y === null ? $value : $value . ':' . $y;
		return $this;
	}

	public function getFocalPoint(): null|int|float|string
	{
		/** @var null|int|float|string $value */
		$value = $this->options[self::FOCAL_POINT] ?? null;
		return $value;
	}

	public function setZoom(int|float|string $value, int|float|null $fallback = null): self
	{
		$this->options[self::ZOOM] = $fallback === null ? $value : $value . ',' . $fallback;
		return $this;
	}

	public function getZoom(): null|int|float|string
	{
		/** @var null|int|float|string $value */
		$value = $this->options[self::ZOOM] ?? null;
		return $value;
	}

	public function setZoomPadding(int|float|string $value, int|float|string|null $y = null): self
	{
		$this->options[self::ZOOM_PADDING] = $y === null ? $value : $value . ':' . $y;
		return $this;
	}

	public function getZoomPadding(): null|int|float|string
	{
		/** @var null|int|float|string $value */
		$value = $this->options[self::ZOOM_PADDING] ?? null;
		return $value;
	}

	public function setFace(int $value): self
	{
		$this->options[self::FACE] = $value;
		return $this;
	}

	public function getFace(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::FACE] ?? null;
		return $value;
	}

	/**
	 * Return SVG source bytes unchanged. False removes the presence flag.
	 */
	public function setPassthrough(bool $passthrough = true): self
	{
		if ($passthrough) {
			$this->options[self::PASSTHROUGH] = '1';
		} else {
			unset($this->options[self::PASSTHROUGH]);
		}

		return $this;
	}

	public function getPassthrough(): bool
	{
		return isset($this->options[self::PASSTHROUGH]);
	}

	public function setDebug(bool $debug): self
	{
		return $this->setParam(self::DEBUG, $debug);
	}

	public function getDebug(): ?bool
	{
		$value = $this->getParam(self::DEBUG);
		return $value === null ? null : (string) $value === '1';
	}

	/**
	 * Set crop
	 */
	public function setCrop(int|string|CropPosition $width, ?int $height = null, ?int $x = null, ?int $y = null): self
	{
		if ($height === null && $x === null && $y === null && ($width instanceof CropPosition || is_string($width))) {
			return $this->setCropPosition($width);
		}

		if (! is_int($width) || $height === null || $x === null || $y === null) {
			throw new \InvalidArgumentException('Rectangle crops require width, height, x and y integers.');
		}

		$this->options[self::CROP] = implode(',', [$width, $height, $x, $y]);
		return $this;
	}

	/**
	 * Get crop
	 *
	 * @return null|string|array<array-key, int>
	 */
	public function getCrop(): null|string|array
	{
		/** @var null|string $value */
		$value = $this->options[self::CROP] ?? null;

		if ($value === null || count(explode(',', $value)) !== 4) {
			return $value;
		}

		return array_map('intval', explode(',', $value));
	}

	public function setWidth(int|float|string $width): self
	{
		$this->options[self::WIDTH] = $width;
		return $this;
	}

	/**
	 * Get width
	 */
	public function getWidth(): null|int|float|string
	{
		return $this->options[self::WIDTH] ?? null;
	}

	/**
	 * Set height
	 */
	public function setHeight(int|float|string $height): self
	{
		$this->options[self::HEIGHT] = $height;
		return $this;
	}

	/**
	 * Get height
	 */
	public function getHeight(): null|int|float|string
	{
		return $this->options[self::HEIGHT] ?? null;
	}

	/**
	 * Set aspect ratio
	 */
	public function setAspectRatio(int|float|string $dividend, null|int|float $divisor = null): self
	{
		$this->options[self::ASPECT_RATIO] = $divisor === null ? $dividend : round((float) $dividend / $divisor, 4);
		return $this;
	}

	/**
	 * Get aspect ratio
	 */
	public function getAspectRatio(): null|float
	{
		/** @var null|int|float|string $value */
		$value = $this->options[self::ASPECT_RATIO] ?? null;

		if ($value === null) {
			return null;
		}

		if (is_string($value) && str_contains($value, ':')) {
			[$width, $height] = explode(':', $value, 2);
			return round((float) $width / (float) $height, 4);
		}

		return (float) $value;
	}

	/**
	 * Set device pixel ratio
	 */
	public function setDevicePixelRatio(int|float $devicePixelRatio = 1): self
	{
		$this->options[self::DEVICE_PIXEL_RATIO] = $devicePixelRatio;
		return $this;
	}

	/**
	 * Get device pixel ratio
	 */
	public function getDevicePixelRatio(): null|int|float
	{
		$value = $this->options[self::DEVICE_PIXEL_RATIO] ?? null;

		return is_numeric($value) ? $value + 0 : null;
	}

	/**
	 * Set brightness
	 */
	public function setBrightness(int $brightness): self
	{
		$this->options[self::BRIGHTNESS] = $brightness;
		return $this;
	}

	/**
	 * Get brightness
	 */
	public function getBrightness(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::BRIGHTNESS] ?? null;

		return $value;
	}

	/**
	 * Set contrast
	 */
	public function setContrast(int $contrast): self
	{
		$this->options[self::CONTRAST] = $contrast;
		return $this;
	}

	/**
	 * Get contrast
	 */
	public function getContrast(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::CONTRAST] ?? null;

		return $value;
	}

	/**
	 * Set gamma
	 */
	public function setGamma(float $gamma): self
	{
		$this->options[self::GAMMA] = $gamma;
		return $this;
	}

	/**
	 * Get gamma
	 */
	public function getGamma(): null|float
	{
		/** @var null|float $value */
		$value = $this->options[self::GAMMA] ?? null;

		return $value;
	}

	/**
	 * Set sharpen
	 */
	public function setSharpen(int $sharpen): self
	{
		$this->options[self::SHARPEN] = $sharpen;
		return $this;
	}

	/**
	 * Get sharpen
	 */
	public function getSharpen(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::SHARPEN] ?? null;

		return $value;
	}

	/**
	 * Set blur
	 */
	public function setBlur(int $blur): self
	{
		$this->options[self::BLUR] = $blur;
		return $this;
	}

	/**
	 * Get blur
	 */
	public function getBlur(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::BLUR] ?? null;

		return $value;
	}

	/**
	 * Set pixelate
	 */
	public function setPixelate(int $pixelate): self
	{
		$this->options[self::PIXELATE] = $pixelate;
		return $this;
	}

	/**
	 * Get pixelate
	 */
	public function getPixelate(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::PIXELATE] ?? null;

		return $value;
	}

	/**
	 * Set filter
	 */
	public function setFilter(string|Filter $filter): self
	{
		if (is_string($filter)) {
			$filter = Filter::from($filter === 'greyscale' ? 'grayscale' : $filter);
		}

		$this->options[self::FILTER] = $filter->value;
		return $this;
	}

	/**
	 * Get filter
	 */
	public function getFilter(): null|Filter
	{
		/** @var null|string $value */
		$value = $this->options[self::FILTER] ?? null;

		return $value ? Filter::from($value) : null;
	}

	public function setWatermarkPath(string $watermarkPath): self
	{
		$this->options[self::WATERMARK_PATH] = $watermarkPath;
		return $this;
	}

	/**
	 * Get watermark path
	 */
	public function getWatermarkPath(): null|string
	{
		/** @var null|string $value */
		$value = $this->options[self::WATERMARK_PATH] ?? null;

		return $value;
	}

	/**
	 * Set watermark origin
	 */
	public function setWatermarkOrigin(string $watermarkOrigin): self
	{
		$this->options[self::WATERMARK_ORIGIN] = $watermarkOrigin;
		return $this;
	}

	/**
	 * Get watermark origin
	 */
	public function getWatermarkOrigin(): null|string
	{
		/** @var null|string $value */
		$value = $this->options[self::WATERMARK_ORIGIN] ?? null;

		return $value;
	}

	public function setWatermarkWidth(int|float|string $watermarkWidth): self
	{
		$this->options[self::WATERMARK_WIDTH] = $watermarkWidth;
		return $this;
	}

	/**
	 * Get watermark width
	 */
	public function getWatermarkWidth(): null|int|float|string
	{
		/** @var null|int|float|string $value */
		$value = $this->options[self::WATERMARK_WIDTH] ?? null;

		return $value;
	}

	public function setWatermarkHeight(int|float|string $watermarkHeight): self
	{
		$this->options[self::WATERMARK_HEIGHT] = $watermarkHeight;
		return $this;
	}

	/**
	 * Get watermark height
	 */
	public function getWatermarkHeight(): null|int|float|string
	{
		return $this->options[self::WATERMARK_HEIGHT] ?? null;
	}

	public function setWatermarkFit(string|Fit $fit): self
	{
		$this->options[self::WATERMARK_FIT] = ($fit instanceof Fit ? $fit : Fit::from($fit))->value;
		return $this;
	}

	public function getWatermarkFit(): ?Fit
	{
		$value = $this->options[self::WATERMARK_FIT] ?? null;
		return $value === null ? null : Fit::from((string) $value);
	}

	public function setWatermarkFocalPoint(int|float|string $value, int|float|string|null $y = null): self
	{
		$this->options[self::WATERMARK_FOCAL_POINT] = $y === null ? $value : $value . ':' . $y;
		return $this;
	}

	public function getWatermarkFocalPoint(): null|int|float|string
	{
		/** @var null|int|float|string $value */
		$value = $this->options[self::WATERMARK_FOCAL_POINT] ?? null;
		return $value;
	}

	public function setWatermarkZoom(int|float|string $value): self
	{
		$this->options[self::WATERMARK_ZOOM] = $value;
		return $this;
	}

	public function getWatermarkZoom(): null|int|float|string
	{
		/** @var null|int|float|string $value */
		$value = $this->options[self::WATERMARK_ZOOM] ?? null;
		return $value;
	}

	public function setWatermarkPadding(int|float|string $watermarkPadding): self
	{
		$this->options[self::WATERMARK_PADDING] = $watermarkPadding;
		return $this;
	}

	/**
	 * Get watermark padding
	 */
	public function getWatermarkPadding(): null|int|float|string
	{
		/** @var null|int|float|string $value */
		$value = $this->options[self::WATERMARK_PADDING] ?? null;

		return $value;
	}

	/**
	 * Set watermark position
	 */
	public function setWatermarkPosition(int|float|string|WatermarkPosition $watermarkPosition): self
	{
		$this->options[self::WATERMARK_POSITION] = $watermarkPosition instanceof WatermarkPosition ? $watermarkPosition->value : $watermarkPosition;
		return $this;
	}

	/**
	 * Get watermark position
	 */
	public function getWatermarkPosition(): null|int|float|string|WatermarkPosition
	{
		$value = $this->options[self::WATERMARK_POSITION] ?? null;
		return $value === null ? null : (WatermarkPosition::tryFrom((string) $value) ?? $value);
	}

	/**
	 * Set watermark alpha
	 */
	public function setWatermarkAlpha(int $watermarkAlpha): self
	{
		$this->options[self::WATERMARK_ALPHA] = $watermarkAlpha;
		return $this;
	}

	/**
	 * Get watermark alpha
	 */
	public function getWatermarkAlpha(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::WATERMARK_ALPHA] ?? null;

		return $value;
	}

	/**
	 * Set background
	 */
	public function setBackground(string $background): self
	{
		$this->options[self::BACKGROUND] = $background;
		return $this;
	}

	/**
	 * Get background
	 */
	public function getBackground(): null|string
	{
		/** @var null|string $value */
		$value = $this->options[self::BACKGROUND] ?? null;

		return $value;
	}

	/**
	 * Set border
	 *
	 * To use a relative dimension, provide a percentage as a number (between 0 and 100), followed by a w (width) or h (height). For example, 5w represents 5% of the width of the main image.
	 */
	public function setBorder(int|float|string $width, string $color, string|BorderMethod $borderMethod): self
	{
		if (is_string($borderMethod)) {
			$borderMethod = BorderMethod::from($borderMethod);
		}

		$this->options[self::BORDER] = implode(',', [$width, $color, $borderMethod->value]);
		return $this;
	}

	/**
	 * Get border
	 *
	 * @return null|array{int|string, string, BorderMethod}
	 */
	public function getBorder(): null|array
	{
		/** @var null|string $value */
		$value = $this->options[self::BORDER] ?? null;

		if ($value === null) {
			return null;
		}

		[$width, $color, $borderMethod] = explode(',', $value);

		return [$width, $color, BorderMethod::from($borderMethod)];
	}

	/**
	 * Set quality
	 */
	public function setQuality(int $quality): self
	{
		$this->options[self::QUALITY] = $quality;
		return $this;
	}

	/**
	 * Get quality
	 */
	public function getQuality(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::QUALITY] ?? null;

		return $value;
	}

	/**
	 * Set format
	 */
	public function setFormat(string|Format $format): self
	{
		if (is_string($format)) {
			$format = strtolower($format);
			// Small Pics transforms support both "jpg" and "jpeg" as possible values for "jpg".
			$format = match ($format) {
				'jpeg' => Format::JPG,
				default => Format::from($format),
			};
		}

		$this->options[self::FORMAT] = $format->value;
		return $this;
	}

	/**
	 * Get format
	 */
	public function getFormat(): null|Format
	{
		/** @var null|string $value */
		$value = $this->options[self::FORMAT] ?? null;

		return $value ? Format::from($value) : null;
	}

	/**
	 * Set interlaced
	 */
	public function setInterlaced(bool $interlaced): self
	{
		$this->options[self::INTERLACE] = $interlaced ? '1' : '0';
		return $this;
	}

	/**
	 * Get interlaced
	 */
	public function getInterlaced(): null|bool
	{
		$value = $this->options[self::INTERLACE] ?? null;
		return $value === null ? null : $value === '1';
	}

	/**
	 * Set an ad hoc query parameter.
	 *
	 * @param non-empty-string $key
	 */
	public function setParam(string $key, int|float|string|bool $value): self
	{
		$this->options[$key] = is_bool($value) ? ($value ? '1' : '0') : $value;
		return $this;
	}

	/**
	 * Set multiple ad hoc query parameters.
	 *
	 * @param array<non-empty-string, int|float|string|bool> $params
	 */
	public function setParams(array $params): self
	{
		foreach ($params as $key => $value) {
			$this->setParam($key, $value);
		}

		return $this;
	}

	/**
	 * Get an ad hoc or built-in query parameter by its serialized key.
	 *
	 * @param non-empty-string $key
	 */
	public function getParam(string $key): null|int|float|string
	{
		return $this->options[$key] ?? null;
	}

	/**
	 * @return array<non-empty-string, non-empty-string>
	 */
	public static function allOptions(): array
	{
		return [
			self::ORIENTATION => 'orientation',
			self::FLIP => 'flip',
			self::CROP => 'crop',
			self::FOCAL_POINT => 'focalPoint',
			self::ZOOM => 'zoom',
			self::ZOOM_PADDING => 'zoomPadding',
			self::FACE => 'face',
			self::DEBUG => 'debug',
			self::PASSTHROUGH => 'passthrough',
			self::WIDTH => 'width',
			self::HEIGHT => 'height',
			self::ASPECT_RATIO => 'aspectRatio',
			self::FIT => 'fit',
			self::DEVICE_PIXEL_RATIO => 'devicePixelRatio',
			self::BRIGHTNESS => 'brightness',
			self::CONTRAST => 'contrast',
			self::GAMMA => 'gamma',
			self::SHARPEN => 'sharpen',
			self::BLUR => 'blur',
			self::PIXELATE => 'pixelate',
			self::FILTER => 'filter',
			self::WATERMARK_PATH => 'watermarkPath',
			self::WATERMARK_ORIGIN => 'watermarkOrigin',
			self::WATERMARK_WIDTH => 'watermarkWidth',
			self::WATERMARK_HEIGHT => 'watermarkHeight',
			self::WATERMARK_FIT => 'watermarkFit',
			self::WATERMARK_FOCAL_POINT => 'watermarkFocalPoint',
			self::WATERMARK_ZOOM => 'watermarkZoom',
			self::WATERMARK_PADDING => 'watermarkPadding',
			self::WATERMARK_POSITION => 'watermarkPosition',
			self::WATERMARK_ALPHA => 'watermarkAlpha',
			self::BACKGROUND => 'background',
			self::BORDER => 'border',
			self::QUALITY => 'quality',
			self::FORMAT => 'format',
			self::INTERLACE => 'interlaced',
		];
	}

	private function toPascalCase(string $input): string
	{
		return ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
	}
}
