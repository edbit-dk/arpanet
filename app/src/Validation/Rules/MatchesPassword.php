<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class MatchesPassword extends AbstractRule {

    protected $pass;

    public function __construct($password) {
        $this->pass = $password;
    }

    public function validate($input): bool  
    {
        return password_verify($input, $this->pass);
    }

}