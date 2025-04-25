<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Definir la relación con usuarios
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
