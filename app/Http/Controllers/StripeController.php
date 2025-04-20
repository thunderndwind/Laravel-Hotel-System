<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;

class StripeController extends Controller
{
    public function show()
    {
        return Inertia::render('StripeCheckout');
    }

    public function handle(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'stripeToken' => 'required|string',
                'amount' => 'required|numeric|min:1',
                'email' => 'required|email',
                'name' => 'required|string',
            ]);

            Stripe::setApiKey(config('services.stripe.secret'));

            // Create a customer
            $customer = Customer::create([
                'email' => $validated['email'],
                'name' => $validated['name'],
                'source' => $validated['stripeToken'],
            ]);

            // Create the charge
            $charge = Charge::create([
                'amount' => $validated['amount'], // Amount in cents
                'currency' => 'usd',
                'customer' => $customer->id,
                'description' => 'Hotel Room Booking',
                'metadata' => [
                    'customer_name' => $validated['name'],
                    'customer_email' => $validated['email'],
                ],
            ]);

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => 'Payment successful!',
                'data' => [
                    'charge_id' => $charge->id,
                    'amount' => $charge->amount / 100, // Convert from cents to dollars
                    'receipt_url' => $charge->receipt_url,
                ]
            ]);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
