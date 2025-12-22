<?php

namespace App\Providers;

use App\Models\User;
use App\Services\GeminiClient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GeminiClient::class, function () {
            return new GeminiClient(
                apiKey: (string) config('services.gemini.key', ''),
                model: (string) config('services.gemini.model', 'gemini-2.5-flash'),
                chunkModel: (string) config('services.gemini.chunk_model', 'gemini-2.0-flash-lite'),
                mergeModel: (string) config('services.gemini.merge_model', 'gemini-2.0-flash'),
                refineModel: (string) config('services.gemini.refine_model', 'gemini-2.5-flash-lite'),
                heavyModel: (string) config('services.gemini.heavy_model', 'gemini-2.5-pro'),
                chunkSize: (int) config('services.gemini.chunk_size', 12000),
                maxChunks: (int) config('services.gemini.max_chunks', 3),
                refinePasses: (int) config('services.gemini.refine_passes', 1),
                heavyMinChars: (int) config('services.gemini.heavy_min_chars', 120000),
                heavyMinRequirements: (int) config('services.gemini.heavy_min_requirements', 60),
                endpoint: (string) config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta'),
                timeoutSeconds: (int) config('services.gemini.timeout', 20),
                verify: config('services.gemini.verify', true),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-admin', function (User $user): bool {
            return $user->hasRole('admin');
        });
    }
}
