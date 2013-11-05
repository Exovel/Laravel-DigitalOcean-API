<?php namespace GrahamCampbell\DigitalOceanAPI\Classes;

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
 *
 * @package    Laravel-DigitalOcean-API
 * @author     Graham Campbell
 * @license    Apache License
 * @copyright  Copyright 2013 Graham Campbell
 * @link       https://github.com/GrahamCampbell/Laravel-DigitalOcean-API
 */

use GrahamCampbell\CoreAPI\Classes\CoreAPI;

class DigitalOceanAPI extends CoreAPI {

    protected $id;
    protected $key;

     public function __construct($app) {
        parent::__construct($app);

        $this->id = $this->app['config']['digitalocean-api::id'];
        $this->key = $this->app['config']['digitalocean-api::key'];

        $this->setup($this->app['config']['digitalocean-api::baseurl']);
    }

    public function resetBaseUrl() {
        $this->setBaseUrl($this->app['config']['digitalocean-api::baseurl']);
    }

    public function getId() {
        return $this->id;
    }

    public function setId($value) {
        $this->id = $value;
    }

    public function resetId() {
        $this->id = $this->app['config']['digitalocean-api::id'];
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($value) {
        $this->key = $value;
    }

    public function resetKey() {
        $this->key = $this->app['config']['digitalocean-api::key'];
    }

    // TODO: api methods

}
