<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteView;

class ViewController extends Controller
{
    public function increaseView()
    {
        $view = SiteView::first();
        $view->increment('views');

        return response()->json(['success' => true]);
    }

    public function getTotalViews()
    {
        return response()->json([
            'total_views' => SiteView::first()->views
        ]);
    }
}
