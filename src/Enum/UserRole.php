<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN           = 'Admin';
    case PROJECT_MANAGER = 'ProjectManager';
    case OPERATOR        = 'Operator';
    case AUDITOR         = 'Auditor';
}
