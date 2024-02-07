<?php namespace App\Models;

use CodeIgniter\Model;

class FrequencyModel extends Model{
    protected $table = 'frequency';
    protected $primaryKey = 'id';
    protected $allowedFields = ['training-day', 'description'];
    protected $validationRules = [
        'training-day' => 'required',
    ];
}