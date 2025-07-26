<?php

namespace smallpics\smallpics\enums;

enum BorderMethod: string
{
	case OVERLAY = 'overlay';
	case SHRINK = 'shrink';
	case PAD = 'pad';
}
