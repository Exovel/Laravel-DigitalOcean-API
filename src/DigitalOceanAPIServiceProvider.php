<?php

/**
 * This file is part of Laravel DigitalOcean API by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\DigitalOceanAPI;

use Illuminate\Support\ServiceProvider;

/**
 * This is the digitalocean api service provider class.
 *
 * @package    Laravel-DigitalOcean-API
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-DigitalOcean-API/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-DigitalOcean-API
 */
class DigitalOceanAPIServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('graham-campbell/digitalocean-api', 'graham-campbell/digitalocean-api', __DIR__);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDigitalOceanAPI();
    }

    /**
     * Register the digitalocean api class.
     *
     * @return void
     */
    protected function registerDigitalOceanAPI()
    {
        $this->app->bindShared('digitaloceanapi', function ($app) {
            $cache = $app['cache'];
            $config = $app['config'];

            return new Classes\DigitalOceanAPI($cache, $config);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'digitaloceanapi'
        );
    }
}
