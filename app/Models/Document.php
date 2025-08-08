<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\Traits\StateMachine;

class Document extends Model
{
    use HasFactory;
    use StateMachine;

    protected $fillable = [
        'title',
        'state'
    ];

    public static $states = [
        'draft' => ['submitted'],
        'submitted' => ['approved', 'rejected'],
        'approved' => [],
        'rejected' => [],
    ];

    protected $attributes = [
        'state' => 'draft',
    ];
}
