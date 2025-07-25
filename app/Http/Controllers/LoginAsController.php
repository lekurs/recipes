<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginAsController extends Controller
{
    /**
     * Se connecter en tant qu'un autre utilisateur
     */
    public function loginAs(User $user)
    {
        // Sauvegarder l'utilisateur actuel pour pouvoir revenir
        if (Auth::check() && !Session::has('original_user_id')) {
            Session::put('original_user_id', Auth::id());
        }

        // Se connecter en tant que l'utilisateur cible
        Auth::login($user);

        // Force refresh de la page pour mettre à jour tous les composants
        return redirect()->to(request()->header('referer', route('dashboard')))->with('success', "Connecté en tant que {$user->name}");
    }

    /**
     * Revenir à l'utilisateur original
     */
    public function switchBack()
    {
        logger('SwitchBack method called');

        $originalUserId = Session::get('original_user_id');
        logger('Original user ID from session: ' . $originalUserId);

        if (!$originalUserId) {
            logger('No original user ID found in session');
            return redirect()->to(request()->header('referer', route('dashboard')))->with('error', 'Aucun utilisateur original trouvé');
        }

        $originalUser = User::find($originalUserId);
        logger('Original user found: ' . ($originalUser ? $originalUser->name : 'NOT FOUND'));

        if (!$originalUser) {
            logger('Original user not found in database');
            return redirect()->to(request()->header('referer', route('dashboard')))->with('error', 'Utilisateur original introuvable');
        }

        // Supprimer la session et revenir à l'utilisateur original
        Session::forget('original_user_id');
        Auth::login($originalUser);

        logger('Successfully switched back to: ' . $originalUser->name);
        // Force refresh complet
        return redirect()->to(request()->header('referer', route('dashboard')))->with('success', "Retour à votre compte ({$originalUser->name})");
    }

    /**
     * Lister tous les utilisateurs pour le switch
     */
    public function users()
    {
        $users = User::orderBy('name')->get();
        return view('admin.login-as', compact('users'));
    }
}
