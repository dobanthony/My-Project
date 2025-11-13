<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 👈 import this trait

class Announcement extends Model
{
    use SoftDeletes; // 👈 enables soft deletes

    protected $fillable = ['title', 'content'];
}
