<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LoginAsQuick extends Component
{
    public $search = '';
    public $showDropdown = false;

    public function render()
    {
        $users = collect();

        if (strlen($this->search) >= 2) {
            $users = User::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->limit(5)
                ->get();
        }

        return view('livewire.login-as-quick', compact('users'));
    }

    public function loginAs($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            $this->addError('search', 'Utilisateur introuvable');
            return;
        }

        // Sauvegarder l'utilisateur actuel
        if (Auth::check() && !Session::has('original_user_id')) {
            Session::put('original_user_id', Auth::id());
        }

        Auth::login($user);

        // Dispatcher un event pour rafraîchir les autres composants
        $this->dispatch('user-switched', userName: $user->name);

        session()->flash('success', "Connecté en tant que {$user->name}");

        // Refresh de la page pour être sûr
        return redirect()->to(request()->header('referer', route('dashboard')));
    }

    public function switchBack()
    {
        $originalUserId = Session::get('original_user_id');

        if (!$originalUserId) {
            return;
        }

        $originalUser = User::find($originalUserId);

        if ($originalUser) {
            Session::forget('original_user_id');
            Auth::login($originalUser);

            // Dispatcher un event pour rafraîchir les autres composants
            $this->dispatch('user-switched', userName: $originalUser->name);

            session()->flash('success', "Retour à votre compte ({$originalUser->name})");

            // Refresh de la page
            return redirect()->to(request()->header('referer', route('dashboard')));
        }
    }
}
