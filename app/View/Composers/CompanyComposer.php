<?php

namespace App\View\Composers;

use App\Models\Company;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
class CompanyComposer
{
    public function compose(View $view)
    {
        $companyName = Cache::remember('company_name', 86400, function () {
           return Company::first()->name;
        });

        $view->with('companyName', $companyName);
    }
}
