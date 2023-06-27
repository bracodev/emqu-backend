<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TerminalStadistic extends Model
{
    use HasFactory;

    protected $table = 'terminals_statistics';

    protected $fillable = [
        'terminal_id',
        'logs',
        'transmitted',
        'received',
        'loss',
        'time',
        'status',
    ];

    protected function logs(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? explode('|', $value) : [],
        );
    }
}
