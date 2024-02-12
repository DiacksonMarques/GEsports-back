<?php namespace App\Models;

class MessagesModel {
    public function fieldValidationMessageSchool() {
        return [
            'name' => [
                'required' => 'O nome é obrigátorio.',
                'min_length' => 'O campo do nome deve ter pelo menos 5 caracteres.'
            ]
        ];
    }

    public function fieldValidationMessagePerson() {
        return [
            'height' => [
                'required' => 'A altura é obrigátoria.',
            ],
            'weight' => [
                'required' => 'O peso é obrigátorio.',
            ],
            'name' => [
                'required' => 'O nome é obrigátorio.',
                'min_length' => 'O campo do nome deve ter pelo menos 5 caracteres.'
            ],
            'birthDate' => [
                'required' => 'A data de nascimento é obrigátoria.',
                'min_length' => 'O campo da data de nascimento deve ter pelo menos 8 caracteres.'
            ],
            'gender' => [
                'required' => 'A categoria é obrigátoria.',
            ],
            'naturalness' => [
                'required' => 'A naturalidade é obrigátoria.',
                'min_length' => 'O campo da naturalidade deve ter pelo menos 3 caracteres.'
            ],
            'ddPhone' => [
                'required' => 'O DDD é obrigátorio.',
                'min_length' => 'O campo do DDD deve ter pelo menos 2 caracteres.',
                'max_length' => 'O campo do DDD deve ter no máximo 2 caracteres.'
            ],
            'numberPhone' => [
                'required' => 'O telefone é obrigátorio.',
                'min_length' => 'O campo do telefone deve ter pelo menos 9 caracteres.',
                'max_length' => 'O campo do telefone deve ter no máximo 9 caracteres.'
            ],
        ];
    }
}