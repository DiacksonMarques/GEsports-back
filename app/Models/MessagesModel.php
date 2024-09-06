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

    public function fieldValidationMessageAthlete() {
        return [
            'enrolment' => [
                'required' => 'A matrícula é obrigátorio.'
            ],
            'person_id' => [
                'required' => 'A pessoa é obrigátorio.'
            ],
            'category_id' => [
                'required' => 'O naipe é obrigátorio.'
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
            'cpf' => [
                'required' => 'O CPF é obrigátorio.',
                'min_length' => 'O campo CPF deve ter pelo menos 11 caracteres.',
                'max_length' => 'O campo CPF não pode exceder 11 caracteres.',
                'is_unique' => 'O campo CPF já está cadastrado.'
            ],
            'rg' => [
                'required' => 'O RG é obrigátorio.',
                'min_length' => 'O campo RG deve ter pelo menos 5 caracteres.',
                'max_length' => 'O campo RG não pode exceder 14 caracteres.',
                'is_unique' => 'O campo RG já está cadastrado.'
            ],
            'issuingBody' => [
                'required' => 'O orgão emissor é obrigátorio.',
                'max_length' => 'O campo orgão emissor não pode exceder 5 caracteres.',
            ],
            'ufEmitter' => [
                'required' => 'O UF é obrigátorio.',
                'min_length' => 'O campo UF deve ter pelo menos 2 caracteres.',
                'max_length' => 'O campo UF não pode exceder 2 caracteres.',
            ],
            'cep' => [
                'required' => 'O CEP é obrigátorio.',
                'min_length' => 'O campo CEP deve ter pelo menos 8 caracteres.',
                'max_length' => 'O campo CEP não pode exceder 8 caracteres.',
            ],
            'adress' => [
                'required' => 'O endereço é obrigátorio.',
                'max_length' => 'O campo endereço não pode exceder 3 caracteres.',
            ],
            'neighborhood' => [
                'required' => 'O bairro é obrigátorio.',
                'max_length' => 'O campo bairro não pode exceder 3 caracteres.',
            ],
            'city' => [
                'required' => 'A cidade é obrigátorio.',
                'max_length' => 'O campo cidade não pode exceder 3 caracteres.',
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
                'max_length' => 'O campo DDD não pode exceder 2 caracteres.'
            ],
            'numberPhone' => [
                'required' => 'O telefone é obrigátorio.',
                'min_length' => 'O campo do telefone deve ter pelo menos 9 caracteres.',
                'max_length' => 'O campo telefone não pode exceder 9 caracteres.'
            ],
        ];
    }
}