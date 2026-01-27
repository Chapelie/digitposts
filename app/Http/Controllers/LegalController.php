<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Afficher les conditions d'utilisation
     */
    public function terms()
    {
        $seoData = [
            'seoTitle' => 'Conditions d\'utilisation - DigitPosts',
            'seoDescription' => 'Consultez les conditions d\'utilisation de la plateforme DigitPosts. Règles et modalités pour l\'utilisation de nos services de formations et événements.',
            'seoKeywords' => 'conditions d\'utilisation, termes, règles, DigitPosts, Burkina Faso',
            'seoUrl' => route('legal.terms'),
            'seoType' => 'article',
        ];
        return view('legal.terms', $seoData);
    }

    /**
     * Afficher la politique de confidentialité
     */
    public function privacy()
    {
        $seoData = [
            'seoTitle' => 'Politique de confidentialité - DigitPosts',
            'seoDescription' => 'Découvrez comment DigitPosts protège vos données personnelles. Notre politique de confidentialité détaille la collecte, l\'utilisation et la protection de vos informations.',
            'seoKeywords' => 'confidentialité, protection des données, RGPD, vie privée, DigitPosts',
            'seoUrl' => route('legal.privacy'),
            'seoType' => 'article',
        ];
        return view('legal.privacy', $seoData);
    }

    /**
     * Afficher la politique des cookies
     */
    public function cookies()
    {
        $seoData = [
            'seoTitle' => 'Politique des cookies - DigitPosts',
            'seoDescription' => 'Informations sur l\'utilisation des cookies sur DigitPosts. Découvrez quels cookies nous utilisons et comment les gérer.',
            'seoKeywords' => 'cookies, politique cookies, gestion cookies, DigitPosts',
            'seoUrl' => route('legal.cookies'),
            'seoType' => 'article',
        ];
        return view('legal.cookies', $seoData);
    }
}
