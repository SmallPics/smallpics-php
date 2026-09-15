# Changelog

## 2.0.0 - Unreleased

### Added

- Add `fp` for focal points.
- Add `zoom` for numeric and face zoom.
- Add `zoompad` for zoom padding.
- Add `face` for face selection by index.
- Add `debug` for visual debugging.
- Add `passthrough` for original SVGs.

### Updated

- Deprecate `fit` values: `cover`, `cover-{position}`, `crop-{position}`, `crop-{x}-{y}`, and `crop-{x}-{y}-{zoom}`.
- Deprecate `markfit` values: `cover`, `cover-{position}`, `crop-{position}`, `crop-{x}-{y}`, and `crop-{x}-{y}-{zoom}`.
- Deprecate `markx`; use `markpos` or `markpad`.
- Deprecate `marky`; use `markpos` or `markpad`.
- Support named and face positions in `crop`.
- Support relative and decimal values in `w` and `h`.
- Support `width:height` values in `ar`.
- Support decimal values in `dpr`.
- Support `p` units in `markw` and `markh`.
- Support coordinates and relative values in `markpos`.
- Support paired and relative values in `markpad`.
- Support `expand` and `p` units in `border`; retain deprecated `pad`.

## 1.2.0 - 2026-08-14

- Add `Format::JXL` variant
- Coerce `"jpeg"` to `Format::JPG`

## 1.1.0 - 2026-07-20

- Add support for `ar` param to set the desired aspect ratio of an image

## 1.0.3 - 2026-07-16

- Add `allOptions()` static method to the `Options` class

## 1.0.0 - 2025-11-12

- Initial release
