<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    protected $fillable = ['plan_id','year','date',
        'first_position_id','second_position_id'];

    public function media()
    {
        return $this->belongsToMany(Media::class)
            ->withTimestamps();
    }

    public function firstPosition()
    {
        return $this->belongsTo(Position::class, 'first_position_id');
    }


    public function secondPosition()
    {
        return $this->belongsTo(Position::class, 'second_position_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
