<?php



namespace App\Rules;



use Illuminate\Contracts\Validation\Rule;



class ValidWebsite implements Rule

{

    public function passes($attribute, $value)

    {

        return filter_var($value, FILTER_VALIDATE_URL);

    }



    public function message()

    {

        return 'The :attribute must be a valid and reachable URL.';

    }

}

