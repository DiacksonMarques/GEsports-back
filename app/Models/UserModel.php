<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'username', 'email', 'password', 'token', 'person_id','roles_id'];
}