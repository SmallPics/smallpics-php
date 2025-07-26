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
	public const ORIGIN = 'origin';

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
	public const WATERMARK_X_OFFSET = 'markx';

	/**
	 * @var string
	 */
	public const WATERMARK_Y_OFFSET = 'marky';

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
			$method = 'set' . $this->toPascalCase($option);
			if (method_exists($this, $method)) {
				// We want to make sure that we can set the individual arguments from an associative array or a regular array.
				if (is_array($value)) {
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
	 * Set origin
	 */
	public function setOrigin(string $origin): self
	{
		$this->options[self::ORIGIN] = $origin;
		return $this;
	}

	/**
	 * Get origin
	 */
	public function getOrigin(): null|string
	{
		/** @var null|string $value */
		$value = $this->options[self::ORIGIN] ?? null;

		return $value;
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

	/**
	 * Set fit
	 */
	public function setFit(string|Fit $fit, null|string|CropPosition $cropPosition = null, ?int $focalPointX = null, ?int $focalPointY = null, ?int $zoom = null): self
	{
		return $this->setBaseFit(self::FIT, $fit, $cropPosition, $focalPointX, $focalPointY, $zoom);
	}

	/**
	 * Get fit
	 *
	 * @return null|Fit|array{Fit, ?CropPosition, ?int, ?int, ?int}
	 */
	public function getFit(): null|Fit|array
	{
		return $this->getBaseFit(self::FIT);
	}

	/**
	 * Set crop
	 */
	public function setCrop(int $width, int $height, int $x, int $y): self
	{
		$this->options[self::CROP] = implode(',', [$width, $height, $x, $y]);
		return $this;
	}

	/**
	 * Get crop
	 *
	 * @return null|array<array-key, int>
	 */
	public function getCrop(): null|array
	{
		/** @var null|string $value */
		$value = $this->options[self::CROP] ?? null;

		if ($value === null) {
			return null;
		}

		return array_map('intval', explode(',', $value));
	}

	public function setWidth(int $width): self
	{
		$this->options[self::WIDTH] = $width;
		return $this;
	}

	/**
	 * Get width
	 */
	public function getWidth(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::WIDTH] ?? null;

		return $value;
	}

	/**
	 * Set height
	 */
	public function setHeight(int $height): self
	{
		$this->options[self::HEIGHT] = $height;
		return $this;
	}

	/**
	 * Get height
	 */
	public function getHeight(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::HEIGHT] ?? null;

		return $value;
	}

	/**
	 * Set device pixel ratio
	 */
	public function setDevicePixelRatio(int $devicePixelRatio = 1): self
	{
		$this->options[self::DEVICE_PIXEL_RATIO] = $devicePixelRatio;
		return $this;
	}

	/**
	 * Get device pixel ratio
	 */
	public function getDevicePixelRatio(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::DEVICE_PIXEL_RATIO] ?? null;

		return $value;
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
			$filter = Filter::from($filter);
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

	public function setWatermarkWidth(int $watermarkWidth): self
	{
		$this->options[self::WATERMARK_WIDTH] = $watermarkWidth;
		return $this;
	}

	/**
	 * Get watermark width
	 */
	public function getWatermarkWidth(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::WATERMARK_WIDTH] ?? null;

		return $value;
	}

	public function setWatermarkHeight(int $watermarkHeight): self
	{
		$this->options[self::WATERMARK_HEIGHT] = $watermarkHeight;
		return $this;
	}

	/**
	 * Get watermark height
	 */
	public function getWatermarkHeight(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::WATERMARK_HEIGHT] ?? null;

		return $value;
	}

	/**
	 * Set watermark fit
	 */
	public function setWatermarkFit(string|Fit $fit, null|string|CropPosition $cropPosition = null, ?int $focalPointX = null, ?int $focalPointY = null, ?int $zoom = null): self
	{
		return $this->setBaseFit(self::WATERMARK_FIT, $fit, $cropPosition, $focalPointX, $focalPointY, $zoom);
	}

	/**
	 * Get watermark fit
	 *
	 * @return null|Fit|array{Fit, ?CropPosition, ?int, ?int, ?int}
	 */
	public function getWatermarkFit(): null|Fit|array
	{
		return $this->getBaseFit(self::WATERMARK_FIT);
	}

	/**
	 * Set watermark x offset
	 */
	public function setWatermarkXOffset(int $watermarkXOffset): self
	{
		$this->options[self::WATERMARK_X_OFFSET] = $watermarkXOffset;
		return $this;
	}

	/**
	 * Get watermark x offset
	 */
	public function getWatermarkXOffset(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::WATERMARK_X_OFFSET] ?? null;

		return $value;
	}

	/**
	 * Set watermark y offset
	 */
	public function setWatermarkYOffset(int $watermarkYOffset): self
	{
		$this->options[self::WATERMARK_Y_OFFSET] = $watermarkYOffset;
		return $this;
	}

	/**
	 * Get watermark y offset
	 */
	public function getWatermarkYOffset(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::WATERMARK_Y_OFFSET] ?? null;

		return $value;
	}

	public function setWatermarkPadding(int $watermarkPadding): self
	{
		$this->options[self::WATERMARK_PADDING] = $watermarkPadding;
		return $this;
	}

	/**
	 * Get watermark padding
	 */
	public function getWatermarkPadding(): null|int
	{
		/** @var null|int $value */
		$value = $this->options[self::WATERMARK_PADDING] ?? null;

		return $value;
	}

	/**
	 * Set watermark position
	 */
	public function setWatermarkPosition(string|WatermarkPosition $watermarkPosition): self
	{
		if (is_string($watermarkPosition)) {
			$watermarkPosition = WatermarkPosition::from($watermarkPosition);
		}

		$this->options[self::WATERMARK_POSITION] = $watermarkPosition->value;
		return $this;
	}

	/**
	 * Get watermark position
	 */
	public function getWatermarkPosition(): null|WatermarkPosition
	{
		/** @var null|string $value */
		$value = $this->options[self::WATERMARK_POSITION] ?? null;

		return $value ? WatermarkPosition::from($value) : null;
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
	 * To use a relative dimension, simply provide a percentage as a number (between 0 and 100), followed by a w (width) or h (height). For example, 5w represents 5% of the width of the main image.
	 */
	public function setBorder(int|string $width, string $color, string|BorderMethod $borderMethod): self
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
			$format = Format::from($format);
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
	 * @param non-empty-string $key
	 */
	private function setBaseFit(string $key, string|Fit $fit, null|string|CropPosition $cropPosition = null, ?int $focalPointX = null, ?int $focalPointY = null, ?int $zoom = null): self
	{
		if (is_string($fit)) {
			$fit = Fit::from($fit);
		}

		if ($fit === Fit::CROP) {
			$args = [$fit->value];

			if ($focalPointX !== null && $focalPointY !== null) {
				$args[] = $focalPointX;
				$args[] = $focalPointY;
			}

			if ($zoom !== null) {
				$args[] = $zoom;
			}

			$this->options[$key] = implode('-', $args);
		} elseif ($fit === Fit::COVER) {
			if ($cropPosition === null) {
				$cropPosition = CropPosition::CENTER;
			}

			if (is_string($cropPosition)) {
				$cropPosition = CropPosition::from($cropPosition);
			}

			$this->options[$key] = $cropPosition->value;
		} else {
			$this->options[$key] = $fit->value;
		}

		return $this;
	}

	/**
	 * @return null|Fit|array{Fit, ?CropPosition, ?int, ?int, ?int}
	 */
	private function getBaseFit(string $key): null|Fit|array
	{
		/** @var null|string $value */
		$value = $this->options[$key];

		if ($value === null) {
			return null;
		}

		if (str_starts_with($value, Fit::CROP->value)) {
			[$fit, $focalPointX, $focalPointY, $zoom] = explode('-', $value) + [null, null, null, null];
			return [
				Fit::CROP,
				null,
				$focalPointX !== null ? (int) $focalPointX : null,
				$focalPointY !== null ? (int) $focalPointY : null,
				$zoom !== null ? (int) $zoom : null,
			];
		}

		if ($value === Fit::COVER->value) {
			return [Fit::COVER, CropPosition::from($value), null, null, null];
		}

		return Fit::from($value);
	}

	private function toPascalCase(string $input): string
	{
		return ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
	}
}
