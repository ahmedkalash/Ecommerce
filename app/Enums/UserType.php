<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';

    case SELLER = 'seller';

    case CUSTOMER = 'customer';

    case DELIVERY_BOY = 'delivery_boy';

    case STAFF = 'staff';
}
