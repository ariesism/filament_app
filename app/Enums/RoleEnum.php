<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Super_Admin = 'super_admin';
    case Admin = 'admin';
    case Editor = 'editor';
    case User = 'user';
}
