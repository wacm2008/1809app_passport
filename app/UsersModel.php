<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersModel extends Model
{
    protected $table='p_user';
    public $timestamps=false;
    protected $primaryKey='uid';
}
