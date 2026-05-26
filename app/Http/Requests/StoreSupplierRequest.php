<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow only authenticated users to create suppliers. Adjust as needed.
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'companyName' => 'required|string|max:255',
            'siret' => 'required|string|size:14',
            'email' => 'required|email|max:255',
            'phoneNumber' => 'required|string|max:50',
            'contactName' => 'required|string|max:255',
            'speciality' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'isValid' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'companyName.required' => "Le nom de l'entreprise est requis",
            'siret.required' => 'Le SIRET est requis',
            'siret.size' => 'Le SIRET doit contenir 14 chiffres',
            'email.required' => "L'adresse e-mail est requise",
            'email.email' => "L'adresse e-mail n'est pas valide",
            'phoneNumber.required' => 'Le numéro de téléphone est requis',
            'contactName.required' => 'Le nom du contact est requis',
        ];
    }
}
