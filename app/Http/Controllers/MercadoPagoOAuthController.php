<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MercadoPagoOAuthController extends Controller
{
    public function redirectToMercadoPago()
    {
        $query = http_build_query([
            'client_id' => env('MP_CLIENT_ID'),
            'response_type' => 'code',
            'platform_id' => 'mp',
            'redirect_uri' => env('MP_REDIRECT_URI'),
        ]);

        return redirect("https://auth.mercadopago.com/authorization?$query");
    }

    public function handleCallback(Request $request)
    {
        $code = $request->get('code');

        $response = Http::asForm()->post('https://api.mercadopago.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('MP_CLIENT_ID'),
            'client_secret' => env('MP_CLIENT_SECRET'),
            'code' => $code,
            'redirect_uri' => env('MP_REDIRECT_URI'),
        ]);

        $data = $response->json();

        if (!$response->successful()) {
            return response()->json(['error' => $data], 400);
        }

        // Guardamos en el usuario actual (usando Auth::user())
        $user = Auth::user();
        $user->mp_access_token = $data['access_token'];
        $user->mp_refresh_token = $data['refresh_token'];
        $user->mp_expires_in = $data['expires_in'];
        $user->mp_user_id = $data['user_id'];
        $user->save();

        return redirect('/dashboard')->with('success', '¡Cuenta de Mercado Pago vinculada correctamente!');
    }
    
    public function disconnect()
    {
        $user = Auth::user();
        $user->mp_access_token = null;
        $user->mp_refresh_token = null;
        $user->mp_expires_in = null;
        $user->mp_user_id = null;
        $user->save();

        return redirect('/dashboard')->with('success', 'Cuenta de Mercado Pago desvinculada correctamente.');
    }
}
