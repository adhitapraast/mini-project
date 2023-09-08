<?php

namespace App\Enums;


enum OrderEnum :string {
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}