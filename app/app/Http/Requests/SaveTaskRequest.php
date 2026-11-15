<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SaveTaskRequest extends FormRequest {
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array {
        return ['title' => ['required','string','max:150'], 'description' => ['nullable','string','max:5000'],
            'due_date' => ['required','date_format:Y-m-d'], 'priority' => ['required', Rule::in(['low','medium','high'])],
            'status' => ['required', Rule::in(['pending','in_progress','done'])]];
    }
}
