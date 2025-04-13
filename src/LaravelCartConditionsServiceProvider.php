<?php

namespace Soap\LaravelCartConditions;

use Gloudemans\Shoppingcart\Cart;
use Soap\LaravelCartConditions\Commands\CartConditionsListCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCartConditionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cart-conditions')
            ->hasConfigFile() // config/cart-conditions.php
            ->hasCommand(CartConditionsListCommand::class);
    }

    public function packageBooted()
    {
        // Bind the condition manager so that it can be easily resolved.
        $this->app->singleton('cartconditions', function () {
            return new ConditionManager;
        });

        // Extend the Cart class if it exists
        if (class_exists(Cart::class)) {
            Cart::macro('withConditions', function (array $conditions = []) {
                // Inject the condition manager from the container
                $conditionManager = app(ConditionManager::class);
                foreach ($conditions as $condition) {
                    $conditionManager->add($condition);
                }
                // Attach the condition manager to the cart instance
                /* @phpstan-ignore-next-line */
                $this->conditionManager = $conditionManager;

                return $this;
            });

            Cart::macro('addCondition', function (\Soap\LaravelCartConditions\CartCondition $condition) {
                // If there is no condition manager, create and inject one
                if (! property_exists($this, 'conditionManager') || ! $this->conditionManager) {
                    /* @phpstan-ignore-next-line */
                    $this->conditionManager = app(ConditionManager::class);
                }
                $this->conditionManager->add($condition);

                return $this;
            });

            Cart::macro('applyConditions', function (string $baseKey) {
                if (! property_exists($this, 'conditionManager') || ! $this->conditionManager) {
                    return $this->{$baseKey};
                }

                return $this->conditionManager->applyTo($this, $baseKey);
            });

            Cart::macro('totalWithConditions', function () {
                /* @phpstan-ignore-next-line */
                return $this->applyConditions('total');
            });
        }
    }
}
