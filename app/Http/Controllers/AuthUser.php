<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthUser extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function isLogin()
    {
        // dd(session()->get('user')['user_id']);
        if ((session()->has('user'))) {
            $client = new \GuzzleHttp\Client();
            $url = config('app.auth') . '/api/getsession/' . session()->get('user')['session_id'];
            $response = $client->get($url);
            $data = $response->getBody()->getContents();
            if ($data == '"yes"') return true;
            else return false;
        } else return false;
    }

    public function setSession(Request $request)
    {
        $user_id = $request->user_id;
        $session_id = $request->session_id;
        session(['user' => [
            'user_id' => $user_id,
            'session_id' => $session_id,
        ]]);
        return  redirect()->route('shop.index');
    }

    public function destroySession(Request $request)
    {
        $url = null;
        if (session()->has('user')) {
            $user = session()->get('user');
            if (array_key_exists("url", $user))
                $url = $user['url'];
        } else $url = null;
        session()->forget('user');
        if ($url != null) return redirect()->away($url . '/api/destroysession');
        else return redirect()->route('landing-page');
    }
}
