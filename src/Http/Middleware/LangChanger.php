<?php

namespace WeirdoPanel\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LangChanger
{

    public function handle($request, Closure $next)
    {
        $lang = session()->has('weirdopanel_lang')
            ? session()->get('weirdopanel_lang')
            : (config('weirdo_panel.lang') ?? 'en') .'_panel';

        App::setLocale($lang);

        return $next($request);
    }

}
