<?php namespace App\Models;

use CodeIgniter\Model;

class AthleteModel extends Model{
    protected $table = 'athlete';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'wingspan',
        'horizontal-jump',
        'vertical-jump',
        'enrolment',
        'person_id',
        'category_id',
        'create_time',
    ];
    
    protected $validationRules = [
        'enrolment' => 'required',
        'person_id' => 'required',
        'category_id' => 'required',
    ];
}