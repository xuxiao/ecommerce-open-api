<?php

/*
 * This file is part of ibrand/EC-Open-Core.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iBrand\EC\Open\Core\Providers;

use iBrand\Component\Advert\AdvertServiceProvider;
use iBrand\Component\Discount\Contracts\AdjustmentContract;
use iBrand\Component\Discount\Providers\DiscountServiceProvider;
use iBrand\Component\Favorite\FavoriteServiceProvider;
use iBrand\Component\Order\Models\Adjustment;
use iBrand\Component\Order\Providers\OrderServiceProvider;
use iBrand\Component\Payment\Providers\PaymentServiceProvider;
use iBrand\Component\Product\ProductServiceProvider;
use iBrand\Component\User\Models\User as BaseUser;
use iBrand\Component\User\UserServiceProvider;
use iBrand\EC\Open\Core\Auth\User;
use iBrand\EC\Open\Core\Console\BuildAddress;
use iBrand\EC\Open\Core\Console\BuildCoupon;
use Illuminate\Support\ServiceProvider;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (config('ibrand.app.secure')) {
            \URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/app.php' => config_path('ibrand/app.php'),
            ]);
        }

        $this->commands([
            BuildAddress::class,
            BuildCoupon::class,
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/app.php', 'ibrand.app'
        );

        $this->registerComponent();

        $this->app->bind(BaseUser::class, User::class);
        $this->app->bind(AdjustmentContract::class, Adjustment::class);
    }

    protected function registerComponent()
    {
        $this->app->register(UserServiceProvider::class);
        $this->app->register(ProductServiceProvider::class);
        $this->app->register(DiscountServiceProvider::class);
        $this->app->register(\iBrand\Component\Category\ServiceProvider::class);
        $this->app->register(OrderServiceProvider::class);
        $this->app->register(\iBrand\Component\Address\ServiceProvider::class);
        $this->app->register(\iBrand\Component\Shipping\ShippingServiceProvider::class);
        $this->app->register(FavoriteServiceProvider::class);
        $this->app->register(AdvertServiceProvider::class);
        $this->app->register(PaymentServiceProvider::class);
    }
}
