<?php

namespace App\Http\Controllers;

use App\Models\Users;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Ilzrv\LaravelSteamAuth\Exceptions\Authentication\SteamResponseNotValidAuthenticationException;
use Ilzrv\LaravelSteamAuth\Exceptions\Validation\ValidationException;
use Ilzrv\LaravelSteamAuth\SteamAuthenticator;
use Ilzrv\LaravelSteamAuth\SteamUserDto;

class SteamController extends Controller
{
    public function auth(Request $request, Redirector $redirector, Client $client, HttpFactory $httpFactory, AuthManager $authManager): RedirectResponse
    {
        $steamAuthenticator = new SteamAuthenticator( new Uri($request->getUri()), $client, $httpFactory);

        try {
            $steamAuthenticator->auth();
        }
        catch (ValidationException|SteamResponseNotValidAuthenticationException) {
            return $redirector->to(
                $steamAuthenticator->buildAuthUrl()
            );
        }

        $steamUser = $steamAuthenticator->getSteamUser();
        $user = $this->firstOrCreate($steamUser);

        Session::put('user', $user);

        return redirect()->route('home');
    }

    private function firstOrCreate(SteamUserDto $steamUser)
    {
        $checkUser = Users::where('steamId', $steamUser->getSteamId())->first();

        if(isset($checkUser))
        {
            return $checkUser;
        }
        else
        {
            $new_user = new Users();
            $new_user->steamId = $steamUser->getSteamId();
            $new_user->username = $steamUser->getPersonaName();
            $new_user->avatar = $steamUser->getAvatarFull();
            $new_user->save();

            return $new_user;
        }
    }
}
