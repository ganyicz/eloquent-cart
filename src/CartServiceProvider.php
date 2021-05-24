<?php

namespace Ganyicz\Cart;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CartServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('eloquent-cart')->hasConfigFile('cart');
    }

    public function bootingPackage()
    {
        $this->app->bind('cart', CartManager::class);
    }
}
