<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Training;
use App\Models\Feed;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FeedController extends Controller
{

    public function index()
    {
        // Get public feeds
        $publicFeeds = Feed::where('isPrivate', false)
            ->where('status', 'publiée')
            ->with(['feedable'])
            ->latest()
            ->get();

        // Separate events and trainings
        $events = [];
        $trainings = [];

        foreach ($publicFeeds as $feed) {
            if ($feed->feedable_type === 'App\Models\Event') {
                $events[] = $feed->feedable;
            } elseif ($feed->feedable_type === 'App\Models\Training') {
                $trainings[] = $feed->feedable;
            }
        }

        // Convert to collections for pagination
        $events = collect($events);
        $trainings = collect($trainings);

        // Count upcoming activities (in the future)
        $upcomingCount = $publicFeeds->filter(function($feed) {
            $date = $feed->feedable_type === 'App\Models\Event'
                ? $feed->feedable->start_date
                : $feed->feedable->end_date;
            return Carbon::parse($date)->isFuture();
        })->count();

        // Count free activities
        $freeCount = $publicFeeds->filter(function($feed) {
            if ($feed->feedable_type === 'App\Models\Event') {
                return $feed->feedable->amount <= 0;
            } else {
                return !$feed->feedable->canPaid;
            }
        })->count();

        return view('welcome', [
            'events' => $events,
            'trainings' => $trainings,
            'upcomingCount' => $upcomingCount,
            'freeCount' => $freeCount

        ]);
    }



    public function create()
    {
        return view('feeds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:event,training',
        ]);

        if ($request->type === 'event') {
            $feedable = Event::create($request->only([
                'title', 'description', 'start_date', 'file', 'amount'
            ]));
        } else {
            $feedable = Training::create($request->only([
                'title', 'description', 'file', 'start_date',
                'end_date', 'location', 'place', 'amount', 'canPaid', 'link'
            ]));
        }

        // Create the feed
        $feed = new Feed([
            'isPrivate' => $request->isPrivate ?? false,
            'status' => 'active',
            'user_id' => auth()->id()
        ]);

        $feedable->feed()->save($feed);

        return redirect()->route('feeds.index')->with('success', 'Activity created successfully');
    }

    public function show($id)
    {
        $feed = Feed::with('feedable')->findOrFail($id);
        return view('campagnes.show', compact('feed'));
    }

    public function edit(Feed $feed)
    {
        $this->authorize('update', $feed);

        return view('feeds.edit', [
            'feed' => $feed->load('feedable')
        ]);
    }

    public function update(Request $request, Feed $feed)
    {
        $this->authorize('update', $feed);

        // Update the feedable model
        if ($feed->feedable_type === 'App\Models\Event') {
            $feed->feedable->update($request->only([
                'title', 'description', 'start_date', 'file', 'amount'
            ]));
        } else {
            $feed->feedable->update($request->only([
                'title', 'description', 'file', 'start_date',
                'end_date', 'location', 'place', 'amount', 'canPaid', 'link'
            ]));
        }

        // Update the feed
        $feed->update([
            'isPrivate' => $request->isPrivate ?? $feed->isPrivate,
            'status' => $request->status ?? $feed->status
        ]);

        return redirect()->route('feeds.show', $feed)->with('success', 'Activity updated successfully');
    }

    public function destroy(Feed $feed)
    {
        $this->authorize('delete', $feed);

        $feed->feedable->delete(); // Delete the polymorphic model
        $feed->delete(); // Delete the feed

        return redirect()->route('feeds.index')->with('success', 'Activity deleted successfully');
    }

    public function feedComment(Feed $feed)
    {
        // Implement comment functionality
    }

    public function addComment(Request $request, Feed $feed)
    {
        // Implement adding comments
    }
    public function registration(Request $request, Feed $feed)
    {
        $feedable = $feed->feedable; // Training ou Event

        $registration = new Registration([
            'user_id' => Auth::id(),
            'status' => Registration::STATUS_PENDING,
            'payment_status' => $feedable->amount > 0
                ? Registration::PAYMENT_PENDING
                : Registration::PAYMENT_COMPLETE,
            'notes' => $request->notes,
        ]);

        $feed->registrations()->save($registration);

        return redirect()->back()
            ->with('success', 'Inscription enregistrée avec succès!');
    }



}
