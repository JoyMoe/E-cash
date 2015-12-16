<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchandiser extends Model
{
    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
