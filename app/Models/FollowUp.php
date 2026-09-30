<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'finding_id', 'user_id', 'action', 'follow_up_date', 'photo_path', 'status',
    ];

    protected function casts(): array
    {
        return ['follow_up_date' => 'date'];
    }

    public function finding()
    {
        return $this->belongsTo(Finding::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
