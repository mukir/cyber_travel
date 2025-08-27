<?php

namespace App\Enums;

enum UserRole: string
{
    case CLIENT = 'client';
    case SALES = 'sales';
    case ADMIN = 'admin';
}

