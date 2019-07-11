<?php

use munkireport\models\MRModel as Eloquent;

class Installhistory_model extends Eloquent
{
    protected $table = 'installhistory';

    protected $fillable = [
      'serial_number',
      'date',
      'displayName',
      'displayVersion',
      'packageIdentifiers',
      'processName',
    ];

    public $timestamps = false;
}
