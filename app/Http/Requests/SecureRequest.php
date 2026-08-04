<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class SecureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'required' => 'Field :attribute is required.',
            'email' => 'Field :attribute must be a valid email address.',
            'min' => 'Field :attribute must be at least :min characters.',
            'max' => 'Field :attribute must not exceed :max characters.',
            'numeric' => 'Field :attribute must be a number.',
            'integer' => 'Field :attribute must be an integer.',
            'string' => 'Field :attribute must be a string.',
            'date' => 'Field :attribute must be a valid date.',
            'regex' => 'Field :attribute format is invalid.',
            'unique' => 'Field :attribute already exists.',
            'exists' => 'Field :attribute does not exist.',
            'in' => 'Field :attribute must be one of: :values.',
            'between' => 'Field :attribute must be between :min and :max.',
            'alpha' => 'Field :attribute must contain only letters.',
            'alpha_num' => 'Field :attribute must contain only letters and numbers.',
            'alpha_dash' => 'Field :attribute must contain only letters, numbers, dashes and underscores.',
            'url' => 'Field :attribute must be a valid URL.',
            'ip' => 'Field :attribute must be a valid IP address.',
            'json' => 'Field :attribute must be a valid JSON string.',
            'file' => 'Field :attribute must be a file.',
            'image' => 'Field :attribute must be an image.',
            'mimes' => 'Field :attribute must be a file of type: :values.',
            'mimetypes' => 'Field :attribute must be a file of type: :values.',
            'size' => 'Field :attribute must be :size kilobytes.',
            'dimensions' => 'Field :attribute has invalid image dimensions.',
            'distinct' => 'Field :attribute has a duplicate value.',
            'different' => 'Field :attribute and :other must be different.',
            'same' => 'Field :attribute and :other must match.',
            'confirmed' => 'Field :attribute confirmation does not match.',
            'accepted' => 'Field :attribute must be accepted.',
            'boolean' => 'Field :attribute must be true or false.',
            'present' => 'Field :attribute must be present.',
            'nullable' => 'Field :attribute can be null.',
            'filled' => 'Field :attribute must not be empty.',
            'required_with' => 'Field :attribute is required when :values is present.',
            'required_without' => 'Field :attribute is required when :values is not present.',
            'required_if' => 'Field :attribute is required when :other is :value.',
            'required_unless' => 'Field :attribute is required unless :other is in :values.',
            'required_with_all' => 'Field :attribute is required when :values are present.',
            'required_without_all' => 'Field :attribute is required when none of :values are present.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Password Confirmation',
            'phone' => 'Phone Number',
            'address' => 'Address',
            'title' => 'Title',
            'description' => 'Description',
            'amount' => 'Amount',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'status' => 'Status',
            'type' => 'Type',
            'category' => 'Category',
            'date' => 'Date',
            'time' => 'Time',
            'file' => 'File',
            'image' => 'Image',
            'url' => 'URL',
            'website' => 'Website',
            'company' => 'Company',
            'position' => 'Position',
            'department' => 'Department',
            'notes' => 'Notes',
            'comments' => 'Comments',
            'message' => 'Message',
            'subject' => 'Subject',
            'content' => 'Content',
            'body' => 'Body',
            'summary' => 'Summary',
            'tags' => 'Tags',
            'keywords' => 'Keywords',
            'meta_description' => 'Meta Description',
            'meta_title' => 'Meta Title',
            'slug' => 'Slug',
            'order' => 'Order',
            'sort' => 'Sort',
            'limit' => 'Limit',
            'offset' => 'Offset',
            'page' => 'Page',
            'per_page' => 'Per Page',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional security validations
            $this->validateSecurity($validator);
        });
    }

    /**
     * Additional security validations
     */
    protected function validateSecurity($validator)
    {
        $data = $this->all();

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Check for SQL injection patterns
                if (preg_match('/\b(ALTER|CREATE|DELETE|DROP|EXEC(UTE)?|INSERT( +INTO)?|MERGE|SELECT|UPDATE|UNION( +ALL)?)\b/i', $value)) {
                    $validator->errors()->add($key, 'Invalid characters detected in ' . $key);
                }

                // Check for XSS patterns
                if (preg_match('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', $value)) {
                    $validator->errors()->add($key, 'Invalid characters detected in ' . $key);
                }

                // Check for command injection patterns
                if (preg_match('/[;&|`$(){}[\]\\]/', $value)) {
                    $validator->errors()->add($key, 'Invalid characters detected in ' . $key);
                }

                // Check for path traversal patterns
                if (preg_match('/\.\.\//', $value) || preg_match('/\.\.\\\\/', $value)) {
                    $validator->errors()->add($key, 'Invalid characters detected in ' . $key);
                }
            }
        }
    }

    /**
     * Get sanitized input data
     */
    public function getSanitizedInput(): array
    {
        $data = $this->all();
        return $this->sanitizeArray($data);
    }

    /**
     * Recursively sanitize array data
     */
    private function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->sanitizeString($value);
            }
        }

        return $data;
    }

    /**
     * Sanitize string input
     */
    private function sanitizeString(string $input): string
    {
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);

        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);

        // Trim whitespace
        $input = trim($input);

        // Limit length to prevent buffer overflow attacks
        if (strlen($input) > 10000) {
            $input = substr($input, 0, 10000);
        }

        return $input;
    }
}
