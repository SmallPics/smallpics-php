# Upgrading from v1 to v2

```bash
composer require smallpics/smallpics-php:^2.0.0 -W
```

The examples use `smallpics\smallpics\Options` and enums from `smallpics\smallpics\enums`. Each example shows the v1 code before and the v2 code after.

Output comments show the query string for the PHP example, without any other defaults.

## Fit names

Replace `cover` with `crop`.

**Before:**

```php
$options->setFit('cover');
$options->setWatermarkFit(Fit::COVER);
// ?fit=cover-center&markfit=cover-center
```

**After:**

```php
$options->setFit('crop');
$options->setWatermarkFit(Fit::CROP);
// ?fit=crop&markfit=crop
```

### PHP associative array

**Before:**

```php
$options = new Options([
    'fit' => 'cover',
    'markfit' => 'cover',
]);
// ?fit=cover-center&markfit=cover-center
```

**After:**

```php
$options = new Options([
    'fit' => 'crop',
    'markfit' => 'crop',
]);
// ?fit=crop&markfit=crop
```

## Crop position

Move the second fit argument into `setCropPosition()`.

**Before:**

```php
$options->setFit(Fit::COVER, CropPosition::TOP);
// ?fit=cover-top
```

**After:**

```php
$options->setFit(Fit::CROP)
    ->setCropPosition(CropPosition::TOP);
// ?crop=top&fit=crop
```

For strings such as `cover-top` or `crop-top`, split the fit and position:

**Before:**

```php
$options = new Options(['fit' => 'cover-top']);
// ?fit=cover-top
```

**After:**

```php
$options = new Options([
    'fit' => 'crop',
    'crop' => 'top',
]);
// ?crop=top&fit=crop
```

`CropPosition` case names stay the same. Their string values lose the `cover-` prefix.

**Before:**

```php
$position = CropPosition::from('cover-top');
$value = CropPosition::TOP->value; // 'cover-top'
```

**After:**

```php
$position = CropPosition::from('top');
$value = CropPosition::TOP->value; // 'top'
```

## Focal point and zoom

`setFit()` now takes one argument. Move focal coordinates and zoom into separate calls.

**Before:**

```php
$options->setFit('crop', null, 25, 75, 2);
// ?fit=crop-25-75-2
```

**After:**

```php
$options->setFit('crop')
    ->setFocalPoint('25p', '75p')
    ->setZoom(2);
// ?fit=crop&fp=25p:75p&zoom=2
```

The old coordinates were percentages. `25p:75p` keeps that meaning; `25:75` means pixels.

For a combined fit string:

**Before:**

```php
$options = new Options(['fit' => 'crop-25-75-2']);
// ?fit=crop-25-75-2
```

**After:**

```php
$options = new Options([
    'fit' => 'crop',
    'fp' => '25p:75p',
    'zoom' => 2,
]);
// ?fit=crop&fp=25p:75p&zoom=2
```

For `crop-25-75`, use the same replacement without `zoom`. An argument array such as `['crop', null, 25, 75, 2]` uses the same separate options.

### PHP associative array with named arguments

**Before:**

```php
$options = new Options([
    'fit' => [
        'fit' => 'crop',
        'focalPointX' => 25,
        'focalPointY' => 75,
        'zoom' => 2,
    ],
]);
// ?fit=crop-25-75-2
```

**After:**

```php
$options = new Options([
    'fit' => 'crop',
    'fp' => '25p:75p',
    'zoom' => 2,
]);
// ?fit=crop&fp=25p:75p&zoom=2
```

## Watermark fit

`setWatermarkFit()` takes one argument. Use `markfp` for the focal point and `markzoom` for zoom. Set both `markw` and `markh` when cropping without zoom.

**Before:**

```php
$options->setWatermarkFit('crop', null, 25, 75, 2);
// ?markfit=crop-25-75-2
```

**After:**

```php
$options->setWatermarkFit('crop')
    ->setWatermarkFocalPoint('25p', '75p')
    ->setWatermarkZoom(2);
// ?markfit=crop&markfp=25p:75p&markzoom=2
```

**Before:**

```php
$options = new Options(['markfit' => 'cover-top']);
// ?markfit=cover-top
```

**After:**

```php
$options = new Options([
    'markw' => 100,
    'markh' => 50,
    'markfit' => 'crop',
    'markfp' => '50p:0',
]);
// ?markfit=crop&markfp=50p:0&markh=50&markw=100
```

### PHP associative array with focal point and zoom

**Before:**

```php
$options = new Options(['markfit' => 'crop-25-75-2']);
// ?markfit=crop-25-75-2
```

**After:**

```php
$options = new Options([
    'markfit' => 'crop',
    'markfp' => '25p:75p',
    'markzoom' => 2,
]);
// ?markfit=crop&markfp=25p:75p&markzoom=2
```

### Chained setters with a named position

**Before:**

```php
$options->setWatermarkFit(Fit::COVER, CropPosition::TOP);
// ?markfit=cover-top
```

**After:**

```php
$options->setWatermarkWidth(100)
    ->setWatermarkHeight(50)
    ->setWatermarkFit(Fit::CROP)
    ->setWatermarkFocalPoint('50p', 0);
// ?markfit=crop&markfp=50p:0&markh=50&markw=100
```

## Reading fit values

Fit getters now return `Fit` or `null`, never an array.

**Before:**

```php
$options->setFit('crop', null, 25, 75, 2);
[$fit, $position, $x, $y, $zoom] = $options->getFit();
// ?fit=crop-25-75-2
```

**After:**

```php
$options->setFit('crop')->setFocalPoint('25p', '75p')->setZoom(2);
$fit = $options->getFit(); // Fit::CROP
$position = $options->getCropPosition(); // null
$focalPoint = $options->getFocalPoint(); // '25p:75p'
$zoom = $options->getZoom(); // 2
// ?fit=crop&fp=25p:75p&zoom=2
```

Read watermark values with `getWatermarkFit()`, `getWatermarkFocalPoint()`, and `getWatermarkZoom()`. `getWatermarkPosition()` returns placement on the main image.

## Watermark offsets

Use `markpad` for offsets from a named edge.

**Before:**

```php
$options->setWatermarkPosition('bottom-right')
    ->setWatermarkXOffset('5w')
    ->setWatermarkYOffset(20);
// ?markpos=bottom-right&markx=5w&marky=20
```

**After:**

```php
$options->setWatermarkPosition('bottom-right')
    ->setWatermarkPadding('5w:20');
// ?markpad=5w:20&markpos=bottom-right
```

**Before:**

```php
$options = new Options([
    'markpos' => 'bottom-right',
    'markx' => 10,
    'marky' => 20,
]);
// ?markpos=bottom-right&markx=10&marky=20
```

**After:**

```php
$options = new Options([
    'markpos' => 'bottom-right',
    'markpad' => '10:20',
]);
// ?markpad=10:20&markpos=bottom-right
```

For coordinates measured from the top-left corner, use `markpos` instead:

**Before:**

```php
$options = new Options([
    'markpos' => 'top-left',
    'markx' => 10,
    'marky' => 20,
]);
// ?markpos=top-left&markx=10&marky=20
```

**After:**

```php
$options = new Options(['markpos' => '10:20']);
// ?markpos=10:20
```

The old offset getters and constants are removed too.

**Before:**

```php
$x = $options->getWatermarkXOffset();
$y = $options->getWatermarkYOffset();
$options->setParam(Options::WATERMARK_X_OFFSET, 10);
$options->setParam(Options::WATERMARK_Y_OFFSET, 20);
// ?markx=10&marky=20
```

**After:**

```php
$padding = $options->getWatermarkPadding(); // e.g. '10:20'
$options->setParam(Options::WATERMARK_PADDING, '10:20');
// ?markpad=10:20
```

The long array keys `watermarkXOffset` and `watermarkYOffset` become `watermarkPadding: '10:20'`. Keep relative units. If `markpad` is already set, keep it and remove the old offsets.

For top-left coordinates through setters:

**Before:**

```php
$options->setWatermarkPosition('top-left')
    ->setWatermarkXOffset(10)
    ->setWatermarkYOffset(20);
// ?markpos=top-left&markx=10&marky=20
```

**After:**

```php
$options->setWatermarkPosition('10:20');
// ?markpos=10:20
```

## Border method

Replace `pad` with `expand`.

**Before:**

```php
$options->setBorder(5, 'ffffff', BorderMethod::PAD);
$options = new Options(['border' => [5, 'ffffff', 'pad']]);
// ?border=5,ffffff,pad
```

**After:**

```php
$options->setBorder(5, 'ffffff', BorderMethod::EXPAND);
$options = new Options(['border' => [5, 'ffffff', 'expand']]);
// ?border=5,ffffff,expand
```

## Getter types

Existing integer dimensions still return integers. If you use the new value types, keep the returned value instead of assuming it is an integer.

**Before:**

```php
$options->setWidth(800);
$width = $options->getWidth(); // 800
// ?w=800
```

**After:**

```php
$options->setWidth('65p');
$width = $options->getWidth(); // '65p'

$options->setDevicePixelRatio(1.5);
$dpr = $options->getDevicePixelRatio(); // 1.5
// ?dpr=1.5&w=65p
```

This also applies to height, watermark dimensions, and watermark padding. `getWatermarkPosition()` can return coordinates as well as an enum; `getCrop()` can return a position string as well as a rectangle. Unset values return `null`.

## Classes that extend Options

Only needed if you override these methods. Match the new argument and return types.

**Before:**

```php
class ImageOptions extends Options
{
    public function setWidth(int $width): Options
    {
        return parent::setWidth($width);
    }

    public function getWidth(): ?int
    {
        return parent::getWidth();
    }
}
```

**After:**

```php
class ImageOptions extends Options
{
    public function setWidth(int|float|string $width): Options
    {
        return parent::setWidth($width);
    }

    public function getWidth(): int|float|string|null
    {
        return parent::getWidth();
    }
}
```

Check other overrides against `Options`, especially fit methods, dimension methods, and watermark methods.

Regenerate stored URLs with `UrlBuilder` after changing options. Signed URLs need a new signature too.
