<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait ValidationMessages
{
    /**
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $failed = $validator->failed();
        $attributes = $this->attributes();
        $additionalRules = method_exists(self::class, 'additionalRules')
            ? $this->additionalRules()
            : [];

        $errors = [];

        foreach ($failed as $fieldName => $fieldParams) {
            $params = array_keys($fieldParams);
            $field = [
                'field' => $attributes[$fieldName] ?? $fieldName,
            ];

            foreach ($params as $param) {
                if (isset($additionalRules[$param])) {
                    $errors[] = __($additionalRules[$param], $field);
                } else {
                    switch ($param) {
                        case 'Required':
                            $errors[] = __('validation.required', $field);
                            break;
                        case 'Boolean':
                            $errors[] = __('validation.boolean', $field);
                            break;
                        case 'String':
                            $errors[] = __('validation.string', $field);
                            break;
                        case 'Numeric':
                            $errors[] = __('validation.numeric', $field);
                            break;
                        case 'Email':
                            $errors[] = __('validation.email', $field);
                            break;
                        case 'Unique':
                            $errors[] = __('validation.unique', $field);
                            break;
                        case 'Image':
                            $errors[] = __('validation.image', $field);
                            break;
                        case 'Mimes':
                            $errors[] = __('validation.mimes', $field);
                            break;
                        case 'DateFormat':
                            $errors[] = __('validation.date_format', $field);
                            break;
                        case 'In':
                            $errors[] = __('validation.in', $field);
                            break;
                        case 'Min':
                            $min = $fieldParams['Min'][0];
                            $mergedArray = array_merge($field, ['min' => $min]);
                            $errors[] = __('validation.min', $mergedArray);
                            break;
                        case 'Max':
                            $max = $fieldParams['Max'][0];
                            $mergedArray = array_merge($field, ['max' => $max]);
                            $errors[] = __('validation.max', $mergedArray);
                            break;
                        default:
                            if (!count($errors)) {
                                $errors[] = __('validation.unknown', $field);
                            }
                    }
                }
            }
        }

        throw new ValidationException($validator, $errors);
    }
}
