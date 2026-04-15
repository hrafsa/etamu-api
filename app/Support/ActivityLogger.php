<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public static function log(string $type, ?Model $subject = null, ?string $description = null, array $properties = [], ?Authenticatable $actor = null): ActivityLog
    {
        $actor = $actor ?: auth()->user();

        return ActivityLog::create([
            'user_id' => $actor?->getAuthIdentifier(),
            'type' => $type,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'properties' => $properties ?: null,
        ]);
    }
}

