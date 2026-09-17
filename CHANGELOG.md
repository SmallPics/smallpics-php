# Changelog

## 2.0.0 - Unreleased

> {warning} This update contains breaking changes. Read the [upgrade guide](https://github.com/SmallPics/smallpics-php/blob/main/migrating-v1-v2.md) before updating.

### Added

- Add `fp` for focal points.
- Add `zoom` for numeric and face zoom.
- Add `zoompad` for zoom padding.
- Add `face` for face selection by index.
- Add `debug` for visual debugging.
- Add `passthrough` for original SVGs.

### Updated

- Remove extra fit arguments.
- Return fit enums instead of crop tuples.
- Remove `cover-` from `CropPosition` values.
- Remove `fit` values: `cover`, `cover-{position}`, `crop-{position}`, `crop-{x}-{y}`, and `crop-{x}-{y}-{zoom}`.
- Remove `markfit` values: `cover`, `cover-{position}`, `crop-{position}`, `crop-{x}-{y}`, and `crop-{x}-{y}-{zoom}`.
- Remove `markx`; use `markpos` or `markpad`.
- Remove `marky`; use `markpos` or `markpad`.
- Support named and face positions in `crop`.
- Support relative and decimal values in `w` and `h`.
- Support `width:height` values in `ar`.
- Support decimal values in `dpr`.
- Support `p` units in `markw` and `markh`.
- Support coordinates and relative values in `markpos`.
- Support paired and relative values in `markpad`.
- Replace `pad` with `expand`; support `p` units in `border`.

## 1.2.0 - 2026-08-14

- Add `Format::JXL` variant
- Coerce `"jpeg"` to `Format::JPG`

## 1.1.0 - 2026-07-20

- Add support for `ar` param to set the desired aspect ratio of an image

## 1.0.3 - 2026-07-16

- Add `allOptions()` static method to the `Options` class

## 1.0.0 - 2025-11-12

- Initial release
