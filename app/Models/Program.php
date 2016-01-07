<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = ['name'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function budget()
    {
        return $this->hasOne(ProgramBudget::class);
    }
}
