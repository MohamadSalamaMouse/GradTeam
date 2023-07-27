<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Join extends Model
{
    use HasFactory;
    protected $primaryKey=['user_id','team_id'];
    public $incrementing=false;
    public  function user(){
        return $this->belongsTo(User::class);
    }
}
