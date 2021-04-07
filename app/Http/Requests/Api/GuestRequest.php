<?php

namespace App\Http\Requests\Api;

use App\Models\Guest;
use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class GuestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        $method = strtoupper($this->getMethod());
        $route_name = Route::currentRouteName();

        if ($method === "POST") {


            if ($route_name === "api.front.guest.create") {
                $rules = [
                    "family_name" => [
                        "required",
                        "string",
                        "between:1,512",
                    ],
                    "given_name" => [
                        "required",
                        "string",
                        "between:1,512",
                    ],
                    "family_name_sort" => [
                        "required",
                        "string",
                        "between:1,512",
                    ],
                    "given_name_sort" => [
                        "required",
                        "string",
                        "between:1,512",
                    ],
                    "email" => [
                        "required",
                        "string",
                        "email:rfc",
                        function ($attribute, $value, $fail) {
                            $guest = Guest::where("email", $value)->get()->first();
                            if ($guest !== NULL) {
                                $fail(":attributeは既に､利用中です｡");
                            }
                        }
                    ],
                    "password" => [
                        "nullable",
                        "string",
                        "between:10,72"
                    ],
                    "phone_number" => [
                        "required",
                        new PhoneNumber(),
                    ],
                    "option" => [
                        "nullable",
                        "string",
                        "max:1024",
                    ]
                ];
            } else if ($route_name === "api.front.guest.update") {

                $rules = [
                    "guest_id" => [
                        "required",
                        "integer",
                        function ($attribute, $value, $fail) {
                            $guest = Guest::where("is_displayed", Config("const.binary_type.on"))
                            ->where("is_deleted", Config("const.binary_type.off"))
                            ->find($value);
                            // 指定したゲスト情報が存在するかどうか
                            if ($guest === NULL) {
                                $fail("指定した{$attribute}のゲスト情報が見つかりません｡");
                            }
                        },
                    ],
                    // "token" => [
                    //     "required",
                    //     "string",
                    //     Rule::exists("guests", "toke"),
                    // ],
                    "family_name" => [
                        "required",
                        "string",
                        "between:,512",
                    ],
                    "given_name" => [
                        "required",
                        "string",
                        "between:,512",
                    ],
                    "family_name_sort" => [
                        "required",
                        "string",
                        "between:,512",
                    ],
                    "given_name_sort" => [
                        "required",
                        "string",
                        "between:,512",
                    ],
                    // メールアドレスとパスワードは別APIでアップデートさせる
                    // "email" => [
                    //     "required",
                    //     "string",
                    //     "email:rfc",
                    // ],
                    // "password" => [
                    //     "required",
                    //     "string",
                    //     "between:10,72"
                    // ],
                    "phone_number" => [
                        "required",
                        new PhoneNumber(),
                    ],
                    "option" => [
                        "nullable",
                        "string",
                        "max:1024",
                    ],
                    "memo" => [
                        "nullable",
                        "string",
                        "max:1024",
                    ]
                ];
            }


        } else if ($method === "GET") {

            if ($route_name === "api.front.guest.detail") {

                $rules = [
                    "guest_id" => [
                        "required",
                        "integer",
                        function ($attribute, $value, $fail) {
                            $guest = Guest::where("is_displayed", Config("const.binary_type.on"))
                            ->where("is_deleted", Config("const.binary_type.off"))
                            ->find($value);

                            // ゲスト情報の存在チェック
                            if ($guest === NULL) {
                                $fail("指定した $attribute のゲスト情報が見つかりません｡");
                            }
                        }
                    ],
                    // "token" => [
                    //     "required",
                    //     "string",
                    //     Rule::exists("guests", "token")
                    // ]
                ];
            } else if ($route_name === "api.front.guest.index") {
                $rules = [];
            }
        }

        return $rules;
    }


    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }


    //エラー時HTMLページにリダイレクトされないようにオーバーライド
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                $validator->errors(),
                422
            )
        );
    }
}
