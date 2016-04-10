<?php
/**
 * Created by PhpStorm.
 * User: sixuanliu
 * Date: 4/9/16
 * Time: 9:57 PM
 */

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class FormPostRequest extends FormRequest
{


    public function rules()
    {
        return [
            'title' => 'min:3',
            'image' => 'mimes:jpeg,bmp,png'
        ];
    }

    public function authorize()
    {
        return true;
    }

}