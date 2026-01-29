<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');
        $paymentStatus = $request->get('payment_status');

        $subscriptions = Subscription::query()
            ->with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('id', 'like', "%{$q}%")
                    ->orWhere('payment_transaction_id', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('email', 'like', "%{$q}%")
                          ->orWhere('firstname', 'like', "%{$q}%")
                          ->orWhere('lastname', 'like', "%{$q}%")
                          ->orWhere('phone', 'like', "%{$q}%");
                    });
            })
            ->when(in_array($status, ['active', 'expired', 'cancelled'], true), fn ($query) => $query->where('status', $status))
            ->when(in_array($paymentStatus, ['pending', 'paid', 'failed', 'cancelled'], true), fn ($query) => $query->where('payment_status', $paymentStatus))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.subscriptions.index', compact('subscriptions', 'q', 'status', 'paymentStatus'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('type')->get();
        return view('admin.subscriptions.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_email' => 'required|email',
            'plan_type' => 'required|in:free_events,create_activities',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'amount' => 'nullable|numeric|min:0|max:999999999',
            'payment_status' => 'required|in:pending,paid,failed,cancelled',
            'status' => 'required|in:active,expired,cancelled',
            'payment_transaction_id' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
        ]);

        $user = User::where('email', $data['user_email'])->first();
        if (!$user) {
            return back()->withInput()->with('error', 'Utilisateur introuvable pour cet email.');
        }

        $plan = SubscriptionPlan::getByType($data['plan_type']);
        $start = isset($data['start_date']) ? Carbon::parse($data['start_date']) : now();
        $end = isset($data['end_date']) ? Carbon::parse($data['end_date']) : (clone $start)->addWeeks($plan ? (int) $plan->duration_weeks : 3);
        $amount = $data['amount'] ?? ($plan ? (float) $plan->amount : 2000);

        Subscription::create([
            'user_id' => $user->id,
            'plan_type' => $data['plan_type'],
            'start_date' => $start,
            'end_date' => $end,
            'amount' => $amount,
            'payment_status' => $data['payment_status'],
            'status' => $data['status'],
            'payment_transaction_id' => $data['payment_transaction_id'] ?? null,
            'payment_date' => isset($data['payment_date']) ? Carbon::parse($data['payment_date']) : null,
        ]);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Abonnement créé avec succès.');
    }

    public function show(Subscription $subscription)
    {
        $subscription->load('user');
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        $subscription->load('user');
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $data = $request->validate([
            'plan_type' => 'required|in:free_events,create_activities',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'amount' => 'required|numeric|min:0|max:999999999',
            'payment_status' => 'required|in:pending,paid,failed,cancelled',
            'status' => 'required|in:active,expired,cancelled',
            'payment_transaction_id' => 'nullable|string|max:255',
            'payment_url' => 'nullable|string|max:2000',
            'payment_date' => 'nullable|date',
        ]);

        $subscription->update([
            'plan_type' => $data['plan_type'],
            'start_date' => Carbon::parse($data['start_date']),
            'end_date' => Carbon::parse($data['end_date']),
            'amount' => $data['amount'],
            'payment_status' => $data['payment_status'],
            'status' => $data['status'],
            'payment_transaction_id' => $data['payment_transaction_id'] ?? null,
            'payment_url' => $data['payment_url'] ?? null,
            'payment_date' => isset($data['payment_date']) ? Carbon::parse($data['payment_date']) : null,
        ]);

        return redirect()->route('admin.subscriptions.edit', $subscription)->with('success', 'Abonnement mis à jour.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')->with('success', 'Abonnement supprimé.');
    }
}
