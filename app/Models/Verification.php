<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'finding_id', 'verifier_id', 'follow_up_id', 'verification_date',
        'result', 'notes', 'photo_path',
    ];

    protected function casts(): array
    {
        return ['verification_date' => 'date'];
    }

    public function finding()
    {
        return $this->belongsTo(Finding::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public function followUp()
    {
        return $this->belongsTo(FollowUp::class);
    }
}
