<?php

namespace smallpics\smallpics\enums;

enum Fit: string
{
	/**
	 * Default. Resizes the image to fit within the width and height boundaries without
	 * cropping, distorting or altering the aspect ratio.
	 */
	case CONTAIN = 'contain';

	/**
	 * Resizes the image to fit within the width and height boundaries without cropping, distorting
	 * or altering the aspect ratio, and will also not increase the size of the image if it is smaller than the
	 * output size.
	 */
	case MAX = 'max';

	/**
	 * Resizes the image to fit within the width and height boundaries without cropping or
	 * distorting the image, and the remaining space is filled with the background color. The resulting
	 * image will match the constraining dimensions.
	 */
	case FILL = 'fill';

	/**
	 * Resizes the image to fit within the width and height boundaries without cropping but
	 * upscaling the image if it's smaller. The finished image will have remaining space on either width
	 * or height (except if the aspect ratio of the new image is the same as the old image). The
	 * remaining space will be filled with the background color. The resulting image will match the
	 * constraining dimensions.
	 */
	case FILL_MAX = 'fill-max';

	/**
	 * Stretches the image to fit the constraining dimensions exactly. The resulting image will
	 * fill the dimensions, and will not maintain the aspect ratio of the input image.
	 */
	case STRETCH = 'stretch';

	/**
	 * Resizes the image to fill the width and height boundaries and crops any excess image
	 * data. The resulting image will match the width and height constraints without distorting the
	 * image. See {@see CropPosition} for more.
	 */
	case COVER = 'cover';

	/**
	 * Resizes the image to fill the width and height boundaries and crops any excess image
	 * data. (Alias for {@see Fit::COVER})
	 */
	case CROP = 'crop';
}
