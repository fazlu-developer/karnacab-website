<?php

namespace App\Http\Controllers;

use App\Services\CmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(CmsService $cms): View
    {
        return view('home', [
            'page' => $cms->home(),
            'cms' => $cms->site(),
        ]);
    }

    public function show(CmsService $cms, string $slug): View|RedirectResponse
    {
        if ($slug === 'faq') {
            return redirect()->route('support', 301);
        }

        $page = $cms->page($slug);
        if ($page === null) {
            abort(404);
        }

        return view('pages.show', [
            'page' => $page,
            'cms' => $cms->site(),
        ]);
    }
}
