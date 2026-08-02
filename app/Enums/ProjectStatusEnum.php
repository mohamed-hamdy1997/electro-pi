<?php

namespace App\Enums;

enum ProjectStatusEnum: string
{
    case ACTIVE    = 'active';
    case COMPLETED = 'completed';
    case ARCHIVED  = 'archived';
}
