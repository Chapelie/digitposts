<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Feed;
use App\Models\Registration;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreatorController extends Controller{
    public function index()
    {
        $user = Auth::user();

        // Récupérer les feeds de l'utilisateur
        $feeds = Feed::with(['feedable', 'user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Compter les campagnes (trainings + events)
        $trainingsCount = Training::whereHas('feed', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $eventsCount = Event::whereHas('feed', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $totalCampaigns = $trainingsCount + $eventsCount;

        // Compter les inscriptions
        $totalRegistrations = Registration::whereHas('feed', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        // Récupérer les campagnes à venir
        $upcomingTrainings = Training::whereHas('feed', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $upcomingEvents = Event::whereHas('feed', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $upcomingCampaigns = $upcomingTrainings->merge($upcomingEvents)
            ->sortBy('start_date')
            ->take(3);

        return view('dashboard.index', [
            'totalCampaigns' => $totalCampaigns,
            'totalRegistrations' => $totalRegistrations,
            'upcomingCampaigns' => $upcomingCampaigns,
            'feeds' => $feeds,
            'user' => $user
        ]);
    }


    public function campaignIndex()
    {
        $user = Auth::user();

        $campaigns = Feed::with(['feedable', 'user'])
            ->where('user_id', $user->id)
            ->get();

        return view('campagnes.index', compact('campaigns'));
    }

    public function campaignCreate(){
         return view('campagnes.create');
     }
     public function campaignStore(Request $request){
            $user = Auth::user();
             $request->validate([
                 'type' => 'required|in:event,training',
             ]);
         // On stocke le fichier dans un répertoire 'uploads' et on récupère le chemin
         $filePath = null;
         if ($request->hasFile('file')) {
             $filePath = $request->file('file')->store('feed/', 'public');
         }

         if ($request->type === 'event') {
             $data = $request->only(['title', 'description', 'start_date', 'amount']);
             $data['file'] = $filePath; // on ajoute manuellement le fichier
             $feedable = Event::create($data);
         } else {
             $data = $request->only([
                 'title', 'description', 'start_date', 'end_date',
                 'location', 'place', 'amount', 'canPaid', 'link'
             ]);
             $data['file'] = $filePath;
             $feedable = Training::create($data);
         }


         // Create the feed
             $feed = new Feed([
                 'isPrivate' => $request->isPrivate ?? false,
                 'status' => 'publiée',
                 'user_id' => $user->id
             ]);
             $feedable->feed()->save($feed);

             return redirect()->route('home')->with('success', 'Activity created successfully');
         }

 }
