<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProfileController extends BaseController
{
    public function show(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('profile', [
            'user' => $user,
            'roles' => $user->getRoles(),
            'departments' => $user->getDepartments(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $mailSent = false;
        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
        ]);

        // On garde l'ancien état pour comparer
        $before = [
            'email' => $user->getEmail(),
            'phone_number' => $user->getPhoneNumber(),
        ];

        $user->fill($validated);

        // Rien n'a changé => pas de save, pas de mail
        if (! $user->isDirty()) {
            return redirect()
                ->route('profile.show')
                ->with('success', 'Aucune modification détectée.');
        }

        $user->save();

        // Envoi d'un mail uniquement si on a une adresse email
        if (! empty($user->getEmail())) {
            $changes = [];
            foreach (['email', 'phone_number'] as $field) {
                if (($before[$field] ?? null) !== ($user->$field ?? null)) {
                    $changes[$field] = ['before' => $before[$field] ?? '', 'after' => $user->$field ?? ''];
                }
            }

            if (config('app.debug')) {
                error_log('envoi de l email à '.$user->getEmail());
            }

            // Mail simple (sans Mailable pour rester léger)
            Mail::raw($this->formatChangesMail($user, $changes), function ($message) use ($user) {
                $message->to($user->getEmail())
                    ->subject('Modification de votre profil - Suivi des colis IUTV');
            });

            $mailSent = true;
        }

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profil mis à jour.')
            ->with('mailSent', $mailSent);
    }

    private function formatChangesMail(User $user, array $changes): string
    {
        $lines = [];
        $lines[] = "Bonjour {$user->getFullName()},";
        $lines[] = '';
        $lines[] = 'Une modification a été effectuée sur votre profil (Suivi des colis IUTV).';
        $lines[] = '';
        $lines[] = 'Changements :';
        foreach ($changes as $field => $diff) {
            $label = match ($field) {
                'email' => 'Email',
                'phone_number' => 'Téléphone',
                default => $field,
            };
            $lines[] = "- {$label} : \"{$diff['before']}\" → \"{$diff['after']}\"";
        }
        $lines[] = '';
        $lines[] = 'Si vous n’êtes pas à l’origine de cette modification, contactez un responsable.';
        $lines[] = '';
        $lines[] = 'Cordialement,';
        $lines[] = 'Suivi des colis IUTV';

        return implode("\n", $lines);
    }
}
