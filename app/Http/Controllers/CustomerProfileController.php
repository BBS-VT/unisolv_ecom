<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerProfileController extends Controller
{

    /**
     * Show the customer's profile.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        $customer = Customer::where('id', $user->customer_id)->first();

        return view('customers.profile.show', compact('user', 'customer'));
    }

    /**
     * Show the form for editing the customer's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit()
    {
        $user = Auth::user();
        $customer = Customer::where('id', $user->customer_id)->first();

        return view('customers.profile.edit', compact('user', 'customer'));
    }

    public function update()
    {
        $user = Auth::user();
        $customer = Customer::where('id', $user->customer_id)->first();

        $validator = Validator::make($request->all(), [
            'CustomerName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'delivery_address_line1' => 'nullable|string|max:255',
            'delivery_address_line2' => 'nullable|string|max:255',
            'delivery_city' => 'nullable|string|max:100',
            'delivery_postal_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update user information
        $user->PreferredName = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        // Update customer information
        if ($customer) {
            $customer->PhoneNumber = $request->input('phone');
            $customer->DeliveryAddressLine1 = $request->input('delivery_address_line1');
            $customer->DeliveryAddressLine2 = $request->input('delivery_address_line2');
            $customer->DeliveryCity = $request->input('delivery_city');
            $customer->DeliveryPostalCode = $request->input('delivery_postal_code');
            $customer->save();
        }

        return redirect()->route('customer.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the customer's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword()
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if current password is correct
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        // Update password
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('customer.profile')
            ->with('success', 'Password updated successfully.');
    }
}
