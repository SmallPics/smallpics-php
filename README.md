# Small Pics PHP

Build image transform URLs for [Small Pics](https://www.smallpics.io) in PHP.

## Requirements

- PHP 8.1+

## Installation

```bash
composer require smallpics/smallpics-php
```

## Quick Start

Create an `Options` instance, configure the transform, then pass it with the image path to a `UrlBuilder`.

```php
use smallpics\smallpics\Options;
use smallpics\smallpics\UrlBuilder;

$options = new Options();
$options
    ->setWidth(800)
    ->setHeight(600)
    ->setFit('crop')
    ->setQuality(80);

$builder = new UrlBuilder('https://images.example.com');

$url = $builder->buildUrl('bird.jpg', $options);
// https://images.example.com/bird.jpg?fit=crop&h=600&q=80&w=800
```

The image path may include leading or trailing slashes; they are normalized when the URL is built.

## Signed URLs

Pass your Small Pics signing secret as the second argument to `UrlBuilder`. The signature is calculated from the normalized URL and added as `s`.

```php
use smallpics\smallpics\Options;
use smallpics\smallpics\UrlBuilder;

$options = new Options([
    'width' => 800,
    'height' => 600,
]);

$builder = new UrlBuilder(
    'https://images.example.com',
    '0123456789abcdef0123456789abcdef',
);

$url = $builder->buildUrl('bird.jpg', $options);
// https://images.example.com/bird.jpg?h=600&w=800&s=...
```

Do not commit signing secrets. Load them from your application's environment or secret manager.

## Options

`Options` is a fluent value object that serializes to Small Pics query parameters.

```php
use smallpics\smallpics\Options;

$options = (new Options())
    ->setWidth(800)
    ->setHeight(600)
    ->setFit('crop')
    ->setCropPosition('top')
    ->setFormat('avif');

echo $options;
// w=800&h=600&fit=crop&crop=top&fm=avif
```

### Initialize From an Array

The constructor accepts setter names in camelCase and short query keys such as `w`, `fp`, and `markpad`.

```php
use smallpics\smallpics\Options;

$options = new Options([
    'width' => 800,
    'height' => 600,
    'fit' => 'crop',
    'crop' => 'top',
    'border' => [
        // Expanded into named parameters for `setBorder`
        'width' => 8,
        'color' => 'ffffff',
        'borderMethod' => 'expand',
    ],
]);

echo $options;
// w=800&h=600&fit=crop&crop=top&border=8,ffffff,expand
```

For a raw or future Small Pics query parameter, use `setParam()` or `setParams()`.

```php
$options->setParam('my-option', 'value');
$options->setParams([
    'another-option' => 1,
    'enabled' => true,
]);
```

Boolean raw parameters are serialized as `1` or `0`.

### Enums

Setters that have a fixed set of values accept their matching enum as well as a string. Enums are in `smallpics\smallpics\enums`.

```php
use smallpics\smallpics\Options;
use smallpics\smallpics\enums\Fit;
use smallpics\smallpics\enums\Format;

$options = (new Options())
    ->setFit(Fit::CROP)->setCropPosition('top')
    ->setFormat(Format::AVIF);
```

Available enums are `BorderMethod`, `Filter`, `Fit`, `Format`, and `WatermarkPosition`.

## Transform Options

Use fluent setters, constructor options, or `setParam()` for serialized query values. Refer to the [Small Pics documentation](https://www.smallpics.io/docs/) for processing behavior and valid ranges.

| Query parameter | Setter | Accepted values | Example |
| --- | --- | --- | --- |
| `or` | `setOrientation()` | `0`, `90`, `180`, `270`, or `auto` | `->setOrientation('auto')` |
| `flip` | `setFlip()` | `v`, `h`, or `both` | `->setFlip('h')` |
| `crop` | `setCrop()` / `setCropPosition()` | Named anchor, `face[,fallback]`, `facesarea[,fallback]`, or width, height, x, y | `->setCrop(400, 300, 10, 20)` |
| `w` | `setWidth()` | Integer or decimal pixels, or relative dimensions | `->setWidth('65p')` |
| `h` | `setHeight()` | Integer or decimal pixels, or relative dimensions | `->setHeight('50w')` |
| `ar` | `setAspectRatio()` | `width:height`, decimal ratio, or dividend and divisor | `->setAspectRatio(16, 9)` |
| `fit` | `setFit()` | See [Fit and Crop Position](#fit-and-crop-position) | `->setFit('crop')->setCropPosition('top')` |
| `dpr` | `setDevicePixelRatio()` | Integer or decimal | `->setDevicePixelRatio(1.5)` |
| `bri` | `setBrightness()` | Integer brightness | `->setBrightness(10)` |
| `con` | `setContrast()` | Integer contrast | `->setContrast(15)` |
| `gam` | `setGamma()` | Float gamma | `->setGamma(1.2)` |
| `sharp` | `setSharpen()` | Integer sharpen amount | `->setSharpen(20)` |
| `blur` | `setBlur()` | Integer blur amount | `->setBlur(5)` |
| `pixel` | `setPixelate()` | Integer pixelate amount | `->setPixelate(8)` |
| `filt` | `setFilter()` | `grayscale` or `sepia` | `->setFilter('grayscale')` |
| `mark` | `setWatermarkPath()` | Watermark image path | `->setWatermarkPath('/watermark.png')` |
| `markorigin` | `setWatermarkOrigin()` | Watermark origin name | `->setWatermarkOrigin('default')` |
| `markw` | `setWatermarkWidth()` | Integer, decimal, or relative width | `->setWatermarkWidth('20w')` |
| `markh` | `setWatermarkHeight()` | Integer, decimal, or relative height | `->setWatermarkHeight('20h')` |
| `markfit` | `setWatermarkFit()` | See [Fit and Crop Position](#fit-and-crop-position) | `->setWatermarkFit('contain')` |
| `markpad` | `setWatermarkPadding()` | Pixels, relative values, or `x:y` | `->setWatermarkPadding(16)` |
| `markpos` | `setWatermarkPosition()` | Named anchor, numeric coordinate, or pixel/relative `x:y` string | `->setWatermarkPosition('bottom-right')` |
| `markalpha` | `setWatermarkAlpha()` | Integer alpha | `->setWatermarkAlpha(80)` |
| `bg` | `setBackground()` | Background color | `->setBackground('ffffff')` |
| `border` | `setBorder()` | Width, color, and method | `->setBorder(8, 'ffffff', 'expand')` |
| `q` | `setQuality()` | Integer quality | `->setQuality(80)` |
| `fm` | `setFormat()` | See [Output Format](#output-format) | `->setFormat('avif')` |
| `interlace` | `setInterlaced()` | Boolean | `->setInterlaced(true)` |
| `fp` | `setFocalPoint()` | Pixels or relative x/y | `->setFocalPoint('25w', '75h')` |
| `zoom` | `setZoom()` | Numeric, `face`, `facesarea`, optional numeric fallback | `->setZoom('face', 2.5)` |
| `zoompad` | `setZoomPadding()` | Pixels or relative x/y | `->setZoomPadding(10, 20)` |
| `face` | `setFace()` | One-based face index | `->setFace(1)` |
| `debug` | `setDebug()` | Boolean | `->setDebug(true)` |
| `passthrough` | `setPassthrough()` | Boolean; false removes the flag | `->setPassthrough(true)` |

### Fit and Crop Position

`setFit()` and `setWatermarkFit()` accept `contain`, `max`, `fill`, `fill-max`, `stretch`, `crop`, and the deprecated values below.

Use `setCropPosition('top')` or constructor `['crop' => 'top']` for a named crop. Anchors are `top-left`, `top`, `top-right`, `left`, `center`, `right`, `bottom-left`, `bottom`, and `bottom-right`.

```php
$options->setFit('crop')->setCropPosition('top');
// fit=crop&crop=top

$options->setFocalPoint('25w', '75h')->setZoom(2.5);
// Adds fp=25w:75h&zoom=2.5
```
### Faces and Zoom

```php
$options = (new Options())
    ->setFit('crop')
    ->setCropPosition('face,top')
    ->setFace(1)
    ->setZoom('face', 2.5)
    ->setZoomPadding('5p', '10p')
    ->setDebug(true);
// fit=crop&crop=face,top&face=1&zoom=face,2.5&zoompad=5p:10p&debug=1
```

### Deprecated Helpers and Values

`setWatermarkXOffset()` / `getWatermarkXOffset()` and their Y equivalents remain available and accept integer, decimal, and relative offsets. They emit `markx` and `marky`. Prefer `setWatermarkPosition('10:20')` or `setWatermarkPadding('10:20')` for new code. Numeric `markpos` overrides legacy offsets; explicit `markpad` takes priority over both.

`Fit::COVER` and plain `cover` emit `crop`. Focal strings such as `crop-25-75-2.5` are still accepted. They emit the current fit and separate `crop`, `fp`, and `zoom` parameters. Legacy focal coordinates become percentages. Explicit current parameters take priority, regardless of option order.

`BorderMethod::PAD` and `pad` remain accepted and emit `expand`.

```php
$options = (new Options())
    ->setFit(Fit::CROP, null, 25, 75, 2)
    ->setWatermarkFit('crop')
    ->setCropPosition('top-left')
    ->setWatermarkXOffset(10)
    ->setWatermarkYOffset(20);
// fit=crop&fp=25p:75p&zoom=2&markfit=crop&crop=top-left&markx=10&marky=20
```

### Relative Values

Image dimensions, focal points, watermark dimensions, positioning, padding, and border width accept relative values. `p` uses the relevant axis, so `25p` means 25% of width for x and 25% of height for y. Append `w` or `h` to a percentage between 0 and 100: `5w` is 5% of the source width and `35h` is 35% of the source height.

```php
$options
    ->setWatermarkWidth('20w')
    ->setWatermarkPadding('5w')
    ->setBorder('2w', 'ffffff', 'overlay');
```

Setters accept current values directly and keep existing numeric calls working:

```php
$options = (new Options())
    ->setWidth('65p')
    ->setHeight('50w')
    ->setDevicePixelRatio(1.5)
    ->setAspectRatio('16:9');
$width = $options->getWidth(); // '65p'
```

Dimension and padding getters preserve numeric and relative values. `getWatermarkPosition()` returns an enum for named positions or the coordinate value. `getAspectRatio()` returns the numeric ratio.

### Output Format

`setFormat()` accepts `jpg`, `jpeg`, `pjpg`, `png`, `gif`, `webp`, `avif`, `jxl`. The alias `jpeg` normalizes to `jpg`.

Unless a specific output format is required, omit `fm`. Small Pics can select a format from the request's `Accept` header. If neither a format nor an `Accept` header is present, Small Pics defaults to AVIF.

```php
$options->setFormat('jpeg');
echo $options; // fm=jpg
```

## SVG passthrough

Set `passthrough: true` in transform parameters (PHP: `['passthrough' => true]`) to serve the original SVG through Small Pics, ignoring transforms. Other image formats still transform normally. Set it to `false` to omit the flag.

The fluent helper is `$options->setPassthrough()`, with `getPassthrough()` to check it. The service checks presence, so raw `setParam('passthrough', false)` still enables passthrough; use `setPassthrough(false)` to disable it.

```php
$options = (new Options())->setPassthrough(true);
$url = (new UrlBuilder('https://images.example.com'))->buildUrl('logo.svg', $options);
// https://images.example.com/logo.svg?passthrough=1

$options->setPassthrough(false); // Removes passthrough from the URL.
```

## Development

Install development dependencies:

```bash
composer install
```

Run the test suite:

```bash
composer test
```

Run static analysis and style checks:

```bash
composer phpstan
composer ecs:check
composer rector:dry-run
```

Apply style fixes:

```bash
composer ecs:fix
```
