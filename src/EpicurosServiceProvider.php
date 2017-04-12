<?php

namespace AndrewDalpino\Epicuros;

use AndrewDalpino\Epicuros\Exceptions\SigningKeyNotFoundException;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;

class EpicurosServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred until requested by the container.
     *
     * @var  boolean  $defer
     */
    protected $defer = true;

    /**
     * Bootstrap the repository service.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRSAKeys::class,
            ]);
        }

        if (! $this->isLumen()) {
            $this->publishes([
                __DIR__ . '/config/epicuros.php' => config_path('epicuros.php'),
            ]);
        }
    }

    /**
     * Register the repository services.
     *
     * @return void
     */
    public function register()
    {
        try {
            if (config('epicuros.algorithm') === 'RS256') {
                $key = file_get_contents(storage_path(config('epicuros.signing_key')));
            } else {
                $key = config('epicuros.signing_key');
            }
        } catch (\Exception $e) {
            throw new SigningKeyNotFoundException();
        }

        $this->app->singleton(Epicuros::class, function () use ($key) {
            return new Epicuros(
                new GuzzleClient([
                    'headers' => config('epicuros.headers', []),
                ]),
                $key,
                config('epicuros.algorithm', 'RS256'),
                config('epicuros.jwt_expire', 60),
                config('epicuros.key_mappings', [])
            );
        });

        $this->app->alias(Epicuros::class, 'epicuros');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Epicuros::class,
            'epicuros',
        ];
    }

    /**
     * @return bool
     */
    protected function isLumen()
    {
        return str_contains($this->app->version(), 'Lumen');
    }
}
