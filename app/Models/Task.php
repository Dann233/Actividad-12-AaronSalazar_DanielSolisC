<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'status'];

    protected $attributes = [
        'status' => 'pendiente',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
    ];
}
