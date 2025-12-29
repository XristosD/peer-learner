<?php

namespace App\Http\Requests;

use App\DTOs\NoteDataDTO;
use App\Http\Requests\Traits\HasTagDTOs;
use App\Rules\ValidateTagsArray;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    use HasTagDTOs;
    
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string',
            'details' => 'nullable|string',
            'tags' => [new ValidateTagsArray],
        ];
    }

    public function getNoteDataDTO(): NoteDataDTO
    {
        $data = new NoteDataDTO();

        $data = $data->setBody($this->input('body'));

        if ($this->filled('details')) {
            $data = $data->setDetails($this->input('details'));
        }

        return $data;
    }
}
