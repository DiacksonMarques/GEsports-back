<?php namespace App\Models;

use CodeIgniter\Model;

class SchoolModel extends Model{
    protected $table = 'school';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name'];
    protected $validationRules = [
        'name' => 'required|min_length[5]',
    ]; 
}