<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:180'], 'subject' => ['required', 'string', 'max:180'], 'message' => ['required', 'string', 'min:10', 'max:5000'], 'privacy_accepted' => ['accepted'], 'website' => ['nullable', 'string', 'max:255']];
    }

    public function attributes(): array
    {
        return ['privacy_accepted' => __('acordul privind prelucrarea datelor'), 'website' => 'website'];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('Te rugăm să completezi numele.'),
            'name.string' => __('Numele introdus nu este valid.'),
            'name.max' => __('Numele este prea lung.'),
            'email.required' => __('Te rugăm să completezi adresa de e-mail.'),
            'email.email' => __('Introdu o adresă de e-mail validă.'),
            'email.max' => __('Adresa de e-mail este prea lungă.'),
            'subject.required' => __('Te rugăm să completezi subiectul.'),
            'subject.string' => __('Subiectul introdus nu este valid.'),
            'subject.max' => __('Subiectul este prea lung.'),
            'message.required' => __('Te rugăm să scrii un mesaj.'),
            'message.string' => __('Mesajul introdus nu este valid.'),
            'message.min' => __('Mesajul pare prea scurt. Te rugăm să adaugi puțin mai mult context.'),
            'message.max' => __('Mesajul este prea lung.'),
            'privacy_accepted.accepted' => __('Trebuie să fii de acord cu prelucrarea datelor pentru a trimite mesajul.'),
        ];
    }
}
