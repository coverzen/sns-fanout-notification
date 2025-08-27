<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification;

use Aws\Sns\SnsClient as SnsService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

final class SnsFanoutNotificationServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     * @return void
     */
    public function boot(): void
    {
        /* @phpstan-ignore argument.type */
        $this->app->bind(SnsFanout::class, fn () => new SnsFanout($this->app->make(SnsService::class)));

        $this->app->bind(SnsService::class, function () {
            /** @var array<string, mixed> $config */
            $config = [
                'version' => 'latest',
                /* @phpstan-ignore arrayUnpacking.nonIterable */
                ...Config::get('services.sns', []),
            ];

            if (Arr::get($config, 'key') && Arr::get($config, 'secret')) {
                Arr::set($config, 'credentials', Arr::only($config, ['key', 'secret', 'token']));
            }

            return new SnsService($config);
        });
    }

    /**
     * @return void
     */
    public function register(): void
    {
    }
}
