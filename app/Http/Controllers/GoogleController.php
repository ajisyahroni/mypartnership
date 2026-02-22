<?php

namespace App\Http\Controllers;

use App\Models\ConfigWebsite;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // public function handleGoogleCallback(Request $request)
    // {
    //     $googleUser = Socialite::driver('google')->stateless()->user();
    //     $userExist = User::where('google_id', $googleUser->getId())->first();

    //     $email = $googleUser->getEmail();
    //     $username = explode('@', $email)[0];
    //     if (preg_match('/@ums\.ac\.id$/', $email)) {
    //         $user = \App\Models\User::updateOrCreate(
    //             [
    //                 'email' => $email,
    //             ],
    //             [
    //                 'uuid' => Str::uuid()->getHex(),
    //                 'name' => $googleUser->getName(),
    //                 'username' => $username,
    //                 'google_id' => $googleUser->getId(),
    //                 'remember_token' => $googleUser->token,
    //                 'avatar_google' => $googleUser->getAvatar(),
    //                 'password' => Hash::make(Str::random(16)),
    //                 'is_active' => '1',
    //                 'status_user' => 'user',
    //                 'email_verified_at' => date("Y-m-d H:i:s"),
    //             ],
    //         );

    //         if ($userExist == null) {
    //             $user->syncRoles(['user']);
    //         }

    //         Auth::login($user);

    //         $user->update(['last_login' => Carbon::now()]);
    //         session(['current_role' => $user->roles->first()->name]);
    //         $environment = ConfigWebsite::where('status', '1')->first()->keterangan;
    //         session(['environment' => $environment]);
    //         session(['menu' => 'mypartnership']);

    //         return redirect()->route('pilihMenu');
    //     } else {
    //         return redirect()->route('login')->with('error', 'Email anda tidak terdaftar sebagai Staff UMS.');
    //     }
    // }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $email = $googleUser->getEmail();

            if (!preg_match('/@ums\.ac\.id$/', $email)) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Email anda tidak terdaftar sebagai Staff UMS.');
            }

            $username = explode('@', $email)[0];

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'uuid'              => Str::uuid()->getHex(),
                    'email'             => $email,
                    'password'          => Hash::make(Str::random(16)),
                    'name'              => $googleUser->getName(),
                    'username'          => $username,
                    'google_id'         => $googleUser->getId(),
                    'remember_token'    => $googleUser->token,
                    'avatar_google'     => $googleUser->getAvatar(),
                    'is_active'         => 1,
                    'status_user'       => 'user',
                    'email_verified_at' => now(),
                ]);

                $user->syncRoles(['user']);
            }

            $user->update([
                'name'              => $googleUser->getName(),
                'username'          => $username,
                'google_id'         => $googleUser->getId(),
                'avatar_google'     => $googleUser->getAvatar(),
                'is_active'         => 1,
                'status_user'       => 'user',
                'email_verified_at' => now(),
                'remember_token'    => $googleUser->token,
            ]);

            Auth::login($user);

            $user->update([
                'last_login' => now(),
            ]);

            session([
                'current_role' => $user->roles->first()?->name,
                'menu'         => 'mypartnership',
            ]);

            $config = ConfigWebsite::where('status', '1')->first();
            session([
                'environment' => $config?->keterangan ?? 'prod',
            ]);

            return redirect()->route('pilihMenu');
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {

            Log::warning('Google OAuth Invalid State', [
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Sesi login Google kadaluarsa. Silakan coba login kembali.');
        } catch (\Throwable $e) {

            Log::error('Google Login Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Terjadi kesalahan saat login Google. Silakan hubungi admin.');
        }
    }
}
