<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class AdminSubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('type')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'amount' => 'required|numeric|min:0|max:999999999',
            'duration_weeks' => 'required|integer|min:1|max:52',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $plan->update($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan d\'abonnement mis à jour.');
    }
}
