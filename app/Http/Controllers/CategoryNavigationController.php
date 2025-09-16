<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryNavigationController extends Controller
{
    /**
     * Map categories to their top-level parent categories
     */
    public static function getCategorizedNavigation(): array
    {
        $categories = Category::active()
            ->withCount(['products' => function ($query) {
                $query->active()->available();
            }])
            ->having('products_count', '>', 0)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return [
            'all' => self::getAllCategories($categories),
            'games' => self::getGameCategories($categories),
            'subscriptions' => self::getSubscriptionCategories($categories),
            'app_stores' => self::getAppStoreCategories($categories),
            'binance' => self::getBinanceCategories($categories),
        ];
    }

    private static function getAllCategories(Collection $categories): Collection
    {
        // Limit to 8 categories for the "All" dropdown
        return $categories->take(8);
    }

    private static function getGameCategories(Collection $categories): Collection
    {
        $gameKeywords = [
            'PUBG', 'Fortnite', 'MineCraft', 'Roblox', 'League Of Legends',
            'Valorant', 'Genshin Impact', 'Call Of Duty', 'Steam', 'PlayStation',
            'PSN', 'XBOX', 'Games', 'Mobile Legends', 'FreeFire', 'Delta Force',
            'Blood Strike', 'Dead by Daylight', 'Eggy Party', 'EVE Echoes',
            'Harry Potter', 'The Lord of the rings', 'Vikingard', 'Blizzard',
            'EA FC Mobile', 'Overwatch', 'Riot Games', 'LC Coins', 'CD Keys',
            'Game Power', 'Conquer Online', 'Identity V', 'Knives out', 'UNDAWN',
            'Travian Legends', 'Heroes Evolved', 'Badlanders', 'Infinite Lagrange',
            'Guild Wars', 'Game Stop', 'Legends of Runeterra', 'Teamfight Tactics',
            'Viking Rise', 'Candy Crush', 'Conquerors', 'Runescape', 'Onmyoji Arena',
            'Dooms Days', 'Eudemons', 'Climbing Sand Dune', 'Arena Breakout',
            'LifeAfter', 'Castle Clash', 'MU Origin', 'Nida Al Harb', 'META QUEST',
            'ChallengeX', 'Marvel Rivals', 'Ace Racer', 'Black Desert', 'Dragon Trail',
            'Kung Fu Saga', 'Legend Online', 'Nintendo','Pubg'
        ];

        return $categories->filter(function ($category) use ($gameKeywords) {
            foreach ($gameKeywords as $keyword) {
                if (stripos($category->name, $keyword) !== false) {
                    return true;
                }
            }
            return false;
        })->take(8); // Limit to 8 categories
    }

    private static function getSubscriptionCategories(Collection $categories): Collection
    {
        $subscriptionKeywords = [
            'NetFlix', 'Spotify', 'Shahid', 'OSN+', 'STARZPLAY', 'Hulu',
            'Amazon Prime', 'Yango Play', 'WATCH IT', 'Shemaroo', 'Spacetoon',
            'STC TV', 'Smashi.TV', 'Twitch', 'Anghami', 'Wajeez', 'Deezer',
            'Video Subscriptions', 'Music Subscriptions', 'iQIYI', 'VIP',
            'TV+', 'EyesPro', 'Noorplay', 'almentor', 'Adobe', 'Microsoft Office'
        ];

        return $categories->filter(function ($category) use ($subscriptionKeywords) {
            foreach ($subscriptionKeywords as $keyword) {
                if (stripos($category->name, $keyword) !== false) {
                    return true;
                }
            }
            return false;
        })->take(8); // Limit to 8 categories
    }

    private static function getAppStoreCategories(Collection $categories): Collection
    {
        $appStoreKeywords = [
            'Google Play', 'Apple Gift Card', 'iTunes', 'Huawei App Gallery',
            'App Store'
        ];

        return $categories->filter(function ($category) use ($appStoreKeywords) {
            foreach ($appStoreKeywords as $keyword) {
                if (stripos($category->name, $keyword) !== false) {
                    return true;
                }
            }
            return false;
        })->take(8); // Limit to 8 categories
    }

    private static function getBinanceCategories(Collection $categories): Collection
    {
        $binanceKeywords = [
            'Binance', 'Bitcoin', 'Crypto', 'WhiteBIT', 'Azteco'
        ];

        return $categories->filter(function ($category) use ($binanceKeywords) {
            foreach ($binanceKeywords as $keyword) {
                if (stripos($category->name, $keyword) !== false) {
                    return true;
                }
            }
            return false;
        })->take(8); // Limit to 8 categories
    }
}
