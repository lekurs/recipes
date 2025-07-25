<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class SwitchBack extends Component
{
    public function render()
    {
        $originalUserId = Session::get('original_user_id');
        $originalUser = null;

        if ($originalUserId) {
            $originalUser = User::find($originalUserId);
        }

        return view('livewire.switch-back', compact('originalUser'));
    }

    public function switchBack()
    {
        // Debug pour voir si la méthode est appelée
        logger('SwitchBack method called');

        $originalUserId = Session::get('original_user_id');
        logger('Original user ID:', ['id' => $originalUserId]);

        if (!$originalUserId) {
            session()->flash('error', 'Aucun utilisateur original trouvé');
            logger('No original user ID found');
            return;
        }

        $originalUser = User::find($originalUserId);
        logger('Original user found:', ['user' => $originalUser?->name]);

        if (!$originalUser) {
            session()->flash('error', 'Utilisateur original introuvable');
            logger('Original user not found in database');
            return;
        }

        // Supprimer la session et revenir à l'utilisateur original
        Session::forget('original_user_id');
        Auth::login($originalUser);

        session()->flash('success', "Retour à votre compte ({$originalUser->name})");
        logger('Successfully switched back to:', ['user' => $originalUser->name]);

        return redirect()->route('dashboard');
    }
}
