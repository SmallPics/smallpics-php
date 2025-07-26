<?php

namespace smallpics\smallpics\enums;

enum Format: string
{
	case JPG = 'jpg';
	case PJPG = 'pjpg';
	case PNG = 'png';
	case GIF = 'gif';
	case WEBP = 'webp';
	case AVIF = 'avif';
}
