<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Modules\Factcolombia1\Models\TenantService\Company;
use App\Models\Tenant\User;

class AuthenticateWithApiToken
{
    public function handle($request, Closure $next)
    {
        // Verifica si el token API está presente en la solicitud
        $token = $request->query('api_token');

        if (!empty($token)) {
            try {
                // Intenta desencriptar el token
                $decryptedToken = decrypt($token);

                // Buscar la compañía que tiene el api_token proporcionado
                $company = Company::where('api_token', $decryptedToken)->first();

                if ($company && $company->user_id) {
                    // Buscar el usuario asociado a la compañía
                    $user = User::find($company->user_id);

                    if ($user) {
                        // Inicia sesión con el usuario encontrado
                        Auth::login($user);
                        return $next($request);
                    }
                }
            } catch (\Exception $e) {
                // Manejo de la excepción si la desencriptación del token falla
                // No hagas nada aquí, deja que la ejecución continúe para intentar la autenticación de sesión
            }
        }

        // Si el usuario ya está autenticado por sesión, simplemente continua
        if (Auth::check()) {
            return $next($request);
        }

        // Si llegamos a este punto, no hay un token válido ni una sesión de usuario.
        // Redirige al usuario a la página de inicio de sesión.
        return redirect()->route('login')->withErrors(['msg' => 'Por favor, inicie sesión para continuar.']);
    }
}
