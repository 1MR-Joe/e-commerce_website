<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exceptions\ValidationException;
use http\Exception\RuntimeException;
use Valitron\Validator;

class CreateCategoryRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data):array {
        $v = new Validator($data);

        $v->rule('required', ['name']);
        $v->rule('regex', 'name', '/^[A-Za-z ]*$/');
        // ^[A-Za-z ]*$

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}