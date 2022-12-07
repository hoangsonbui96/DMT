<?php

namespace App\Rules;

use App\OvertimeWork;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class OvertimeDayCheck implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
//        if ( Hash::check($value, User::where('username', Auth::user()->username)->first()->password) )
//            return true;
//        else return false;
        $result = OvertimeWork::query()
            ->whereDate('STime', Carbon::parse($value)->toDateString())
            ->first();
        if($result) return false;
        else return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute error message.';
    }
}
