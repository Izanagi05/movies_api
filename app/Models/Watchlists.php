<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watchlists extends Model
{
    use HasFactory;
      protected $table = 'watchlists';
    protected $fillable = ['user_id', 'movie_api_id', 'status', 'rating'];
    public $timestamps = true;
}
