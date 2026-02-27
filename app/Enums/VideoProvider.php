<?php

namespace App\Enums;

enum VideoProvider: string
{
    case YOUTUBE = 'youtube';
    case DAILYMOTION = 'dailymotion';
    case VIMEO = 'vimeo';
}
