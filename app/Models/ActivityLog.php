<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use BelongsToCampus;
}
