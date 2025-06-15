<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['title', 'video_type', 'url', 'length', 'module_id'];
    public function module() {
        return $this->belongsTo(Module::class);
    }
    
}
