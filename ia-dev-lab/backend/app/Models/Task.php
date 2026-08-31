<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'done',
        'archived',
    ];

    protected function casts(): array
    {
        return [
            'done' => 'boolean',
            'archived' => 'boolean',
        ];
    }
}
