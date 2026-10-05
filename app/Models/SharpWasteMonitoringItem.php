<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharpWasteMonitoringItem extends Model
{
    protected $fillable = ['monitoring_id', 'statement', 'answer', 'notes', 'order'];

    public function monitoring()
    {
        return $this->belongsTo(SharpWasteMonitoring::class, 'monitoring_id');
    }
}
