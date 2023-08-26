<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Support\Facades\Session;

class AuthenticateQwasar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $client = new Client();

        // Login
        $crawler = $client->request('GET', 'https://casapp.us.qwasar.io/login?service=https%3A%2F%2Fupskill.us.qwasar.io%2Fusers%2Fservice');
        $form = $crawler->selectButton('Login')->form();
        $form['username'] = 'jas.884@mail.ru';
        $form['password'] = 'jasur2171517';
        $crawler = $client->submit($form);
        
        $sessionData = [
            'cookieJar' => $client->getCookieJar(),
            'uri' => $client->getRequest()->getUri(),
        ];
        Session::put('qwasarClient', $sessionData);

        return $next($request);
    }
}