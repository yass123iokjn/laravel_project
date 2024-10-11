<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Si l'authentification échoue, renvoyer l'utilisateur avec un message d'erreur
        if (!Auth::attempt($request->only('email', 'password'))) {
            return redirect()->back()->with('error', 'Utilisateur inexistant');
        }
    
        // Si l'authentification réussit, on régénère la session
        $request->session()->regenerate();
    
        // Redirection vers la page des formules après la connexion
        return redirect()->intended(route('formulas.index'));
    }
    

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
