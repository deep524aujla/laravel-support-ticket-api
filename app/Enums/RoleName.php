<?php

namespace App\Enums;

enum RoleName: string
{
    case Admin = 'admin';
    case Agent = 'agent';
    case Customer = 'customer';
}
