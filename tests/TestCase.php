<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification\Tests;

use Coverzen\Components\SnsFanoutNotification\SnsFanoutNotificationServiceProvider;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Abstract Class TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app.faker_locale', 'it_IT');
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app): array
    {
        return [
            SnsFanoutNotificationServiceProvider::class,
        ];
    }
}
