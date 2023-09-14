<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    
    //複数代入を可能にする(※createでnullになってエラー出る)
    protected $fillable = ['content','status',];

    //このタスクを保有するユーザ
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
