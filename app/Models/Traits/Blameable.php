<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait Blameable
{
    public static function bootBlameable(): void
    {
        static::creating(function ($model) {
            $userId = Auth::id();

            if ($userId) {
                if (in_array('created_by_user_id', $model->getFillable())) {
                    $model->created_by_user_id = $model->created_by_user_id ?? $userId;
                }

                if (in_array('updated_by_user_id', $model->getFillable())) {
                    $model->updated_by_user_id = $model->updated_by_user_id ?? $userId;
                }
            }
        });

        static::updating(function ($model) {
            $userId = Auth::id();

            if ($userId && in_array('updated_by_user_id', $model->getFillable())) {
                $model->updated_by_user_id = $userId;
            }
        });
    }
}
