<?php

namespace App\Enums;

enum SocialProvider: string
{
    case GOOGLE = 'google';
    case FACEBOOK = 'facebook';
    case TWITTER = 'twitter';
    case APPLE = 'apple';
}
