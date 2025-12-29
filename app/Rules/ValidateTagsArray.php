<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidateTagsArray implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail($this->message(), null);
            return;
        }

        foreach ($value as $index => $tag) {
            if (!is_array($tag)) {
                $fail($this->message(), null);
                return;
            }

            // Check for valid keys
            $validKeys = ['ulid', 'title', 'order'];
            $invalidKeys = array_diff(array_keys($tag), $validKeys);
            if (!empty($invalidKeys)) {
                $fail($this->message(), null);
                return;
            }
            
            // If order is present, validate it
            if (!isset($tag['order'])) {
                $fail($this->message(), null);
                return;
            }

            if (!is_int($tag['order'])) {
                $fail($this->message(), null);
                return;
            }

            $hasUlid = !empty($tag['ulid']);
            $hasTitle = !empty($tag['title']);

            // Either ulid or title must be present
            if (!$hasUlid && !$hasTitle) {
                $fail($this->message(), null);
                return;
            }

            // If ulid is present, validate it
            if ($hasUlid) {
                if (!is_string($tag['ulid'])) {
                    $fail($this->message(), null);
                    return;
                }

                // Check if the ulid exists in the tags table
                if (!DB::table('tags')->where('ulid', $tag['ulid'])->exists()) {
                    $fail($this->message(), null);
                    return;
                }
            }

            // If title is present, validate it
            if ($hasTitle) {
                if (!is_string($tag['title'])) {
                    $fail($this->message(), null);
                    return;
                }
            }
        }
    }

    protected function message(): string
    {
        return 'The tags could not get processed.';
    }
}
