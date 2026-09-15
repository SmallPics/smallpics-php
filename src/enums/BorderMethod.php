<?php

namespace smallpics\smallpics\enums;

enum BorderMethod: string
{
	case OVERLAY = 'overlay';
	case SHRINK = 'shrink';
	case EXPAND = 'expand';

	/** @deprecated Use EXPAND instead. */
	case PAD = 'pad';
}
