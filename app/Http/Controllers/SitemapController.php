<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Feed;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Générer le sitemap XML
     */
    public function index()
    {
        $sitemap = Cache::remember('sitemap_xml', 3600, function () {
            $baseUrl = config('app.url');
            $feeds = Feed::where('status', 'active')
                ->where('isPrivate', false)
                ->with('feedable')
                ->orderBy('created_at', 'desc')
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
            $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

            // Page d'accueil
            $xml .= $this->urlElement($baseUrl, now(), 'daily', '1.0');

            // Pages statiques
            $staticPages = [
                ['url' => route('login'), 'priority' => '0.5'],
                ['url' => route('register'), 'priority' => '0.5'],
                ['url' => route('legal.terms'), 'priority' => '0.3'],
                ['url' => route('legal.privacy'), 'priority' => '0.3'],
                ['url' => route('legal.cookies'), 'priority' => '0.3'],
            ];

            foreach ($staticPages as $page) {
                $xml .= $this->urlElement($page['url'], now()->subDays(7), 'monthly', $page['priority']);
            }

            // Pages d'événements/formations
            foreach ($feeds as $feed) {
                $url = route('campaigns.show', $feed->id);
                $lastmod = $feed->updated_at ?? $feed->created_at;
                $xml .= $this->urlElement($url, $lastmod, 'weekly', '0.8', $feed);
            }

            $xml .= '</urlset>';

            return $xml;
        });

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Générer un élément URL pour le sitemap
     */
    private function urlElement($url, $lastmod, $changefreq, $priority, $feed = null)
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
        $xml .= "    <lastmod>" . $lastmod->format('Y-m-d') . "</lastmod>\n";
        $xml .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        $xml .= "    <priority>" . $priority . "</priority>\n";

        // Ajouter l'image si disponible
        if ($feed && $feed->feedable && $feed->feedable->file) {
            $imageUrl = asset('storage/' . $feed->feedable->file);
            $xml .= "    <image:image>\n";
            $xml .= "      <image:loc>" . htmlspecialchars($imageUrl) . "</image:loc>\n";
            $xml .= "      <image:title>" . htmlspecialchars($feed->feedable->title) . "</image:title>\n";
            $xml .= "    </image:image>\n";
        }

        $xml .= "  </url>\n";

        return $xml;
    }
}
