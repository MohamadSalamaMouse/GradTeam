<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    public $fillable = [
        'name',
        'description',
        'Num_of_Members',
    ];
   public function members(){
       return $this->hasMany(User::class);
   }

}
