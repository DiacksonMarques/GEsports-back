<?php namespace App\Models;

use CodeIgniter\Model;

class MonthlyFeeModel extends Model{
    protected $table = 'monthly-fee';
    protected $primaryKey = 'id';
    protected $allowedFields = ['value', 'validity'];
    protected $validationRules = [
        'value' => 'required',
        'validity' => 'required',
    ];
}