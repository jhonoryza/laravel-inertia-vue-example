<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->registerLengthAwarePaginator();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}

    protected function registerLengthAwarePaginator()
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {

            return new class(...array_values($values)) extends LengthAwarePaginator
            {
                /**
                 * Get the instance as an array.
                 *
                 * @return array
                 */
                public function toArray()
                {
                    return [
                        'current_page' => $this->currentPage(),
                        'data' => $this->items->toArray(),
                        'first_page_url' => $this->url(1),
                        'from' => $this->firstItem(),
                        'last_page' => $this->lastPage(),
                        'last_page_url' => $this->url($this->lastPage()),
                        'links' => $this->onEachSide(2)->linkCollection()->toArray(),
                        'next_page_url' => $this->nextPageUrl(),
                        'path' => $this->path(),
                        'per_page' => $this->perPage(),
                        'prev_page_url' => $this->previousPageUrl(),
                        'to' => $this->lastItem(),
                        'total' => $this->total(),
                    ];
                }
            };
        });
    }
}
