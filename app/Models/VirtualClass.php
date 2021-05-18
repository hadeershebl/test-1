<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualClass extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'virtual_classes';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
