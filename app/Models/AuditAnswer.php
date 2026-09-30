<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditAnswer extends Model
{
    protected $fillable = ['audit_id', 'question_id', 'answer', 'score', 'notes'];

    protected function casts(): array
    {
        return ['score' => 'decimal:2'];
    }

    public const ANSWER_YA     = 'ya';
    public const ANSWER_TIDAK  = 'tidak';
    public const ANSWER_NA     = 'na';

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function question()
    {
        return $this->belongsTo(AuditQuestion::class, 'question_id');
    }
}
