<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roll extends Model
{
    use HasFactory;

    protected $fillable =[
        'dice1',
        'dice2',
        'won',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
