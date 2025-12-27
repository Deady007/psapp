<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class SettingsController extends Controller
{
    public function application(): View
    {
        return view('settings.application');
    }
}
