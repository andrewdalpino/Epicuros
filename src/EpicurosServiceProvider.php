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
     * @var  bool  $defer
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
                GenerateSharedKey::class,
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
        $key = config('epicuros.signing_key');

        if (config('epicuros.algorithm') === 'RS256' && is_file(storage_path($key))) {
            try {
                $key = file_get_contents(storage_path($key));
            } catch (\Exception $e) {
                throw new SigningKeyNotFoundException();
            }
        }

        $this->app->singleton(Epicuros::class, function () use ($key) {
            return new Epicuros(
                config('epicuros.issuer', 'Epicuros'),
                $key,
                config('epicuros.algorithm', 'RS256'),
                config('epicuros.token_expire', 60),
                config('epicuros.key_mappings', [])
            );
        });

        $this->app->alias(Epicuros::class, 'epicuros');
    }

    /**
     * @return array
     */
    public function provides() : array
    {
        return [
            Epicuros::class,
            'epicuros',
        ];
    }

    /**
     * @return bool
     */
    protected function isLumen() : bool
    {
        return str_contains($this->app->version(), 'Lumen');
    }
}
