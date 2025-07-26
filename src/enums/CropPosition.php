<?php

namespace smallpics\smallpics\enums;

enum CropPosition: string
{
	case TOP_LEFT = 'cover-top-left';
	case TOP = 'cover-top';
	case TOP_RIGHT = 'cover-top-right';
	case LEFT = 'cover-left';
	case CENTER = 'cover-center';
	case RIGHT = 'cover-right';
	case BOTTOM_LEFT = 'cover-bottom-left';
	case BOTTOM = 'cover-bottom';
	case BOTTOM_RIGHT = 'cover-bottom-right';
}
