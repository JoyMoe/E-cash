<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['merchandiser_id', 'trade_no', 'subject', 'amount', 'items', 'returnUrl', 'notifyUrl'];

    public function merchandiser()
    {
        return $this->belongsTo('App\Merchandiser');
    }
}
