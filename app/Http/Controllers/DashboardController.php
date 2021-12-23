<?php


namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, User $user)
    {

        $user = $request->user();
        $company = $user->currentCompany();

        // Dashboard Stats
        $customersCount = Customer::findByCompany($company->id)->count();
        $invoicesCount = Order::findByCompany($company->id)->count();

        // Due/Open Orders Invoices and Estimates
        $dueOrders = Order::findByCompany($company->id)->active()->where('OrderStatusID', '=', 1)->take(5)->latest()->get();

        // Financial Year Starts-Ends
        $financialYearStarts = $company->getSetting('financial_month_starts');
        $financialYearEnds = $company->getSetting('financial_month_ends');

        // Create Carbon Instances from Financial Year
        $dateStarts = Carbon::now()->month($financialYearStarts)->startOfMonth();
        $dateEnds = Carbon::now()->month($financialYearEnds)->endOfMonth();

        // if the date ends is smaller than date start, add one year to date ends
        if($dateEnds->lt($dateStarts)){
            $dateEnds->addYear(1)->endOfMonth();
        }

        // Create Period from given dates
        $period = CarbonPeriod::since($dateStarts)->months(1)->until($dateEnds);

        return view('dashboard', [
            'customersCount' => $customersCount,
            'ordersCount'    => $invoicesCount,
            'dueOrders'      => $dueOrders,
            'currency_code'  => $company->currency->code,
        ]);
    }

    public function sales()
    {
        return view('dashboards.salesrep');
    }


}
