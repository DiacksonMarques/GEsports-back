<?php namespace App\Models;

use CodeIgniter\Model;

class AthleteModel extends Model{
    protected $table = 'athlete';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'wingspan',
        'horizontal-jump',
        'vertical-jump',
        'registration',
        'person_id',
        'create_time',
    ];
    
    protected $validationRules = [
        'wingspan' => 'required',
        'horizontal-jump' => 'required',
        'vertical-jump' => 'required',
        'registration' => 'required|max_length[15],is_unique'
    ];
}