<?php namespace App\Models;

use CodeIgniter\Model;

class PresenceModel extends Model{
    protected $table = 'presence';
    protected $primaryKey = 'id';
    protected $allowedFields = ['confirmed-presence', 'justification', 'athlete_id', 'frequency_id'];
    protected $validationRules = [
        'confirmed-presence' => 'required',
        'athlete_id' => 'required',
        'frequency_id' => 'required',
    ];
}