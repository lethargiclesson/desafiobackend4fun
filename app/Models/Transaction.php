<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    public function buyer(){
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function seller(){
        return $this->belongsTo(User::class, 'payee_id');
    }
}
