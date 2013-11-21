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

    protected function request($action, $params, $data, $cache = false) {
        foreach ($params as $key => $value) {
            $action = str_replace('{'.$key.'}', $value, $action);
        }

        $url = $this->baseurl . $action . '/?client_id=' . $this->id . '&api_key=' . $this->key;

        foreach ($data as $key => $value) {
            $url= $url . '&' . $key . '=' . $value;
        }

        $response = $this->get($url, null, array(), $cache);

        try {
            $body = $response->getResponse()->json();
        } catch (\Exception $e) {}

        if (isset($body) && is_array($body)) {
            if ($body['status'] !== 'OK') {
                $e = DigitalOceanAPIException::factory($response->getRequest(), $response->getResponse());
                throw $e;
            } else {
                return $response;
            }
        } else {
            $e = DigitalOceanAPIException::factory($response->getRequest(), $response->getResponse());
            throw $e;
        }
    }

    public function api_droplets() {
        $url = 'droplets';

        $params = array();

        $data = array();

        $cache = 15;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_new_droplet($name, $size_id, $image_id, $region_id, $ssh_key_ids = null, $private_networking = false) {
        $url = 'droplets/new';

        $params = array();

        $data = array(
            'name'      => $name,
            'size_id'   => $size_id,
            'image_id'  => $image_id,
            'region_id' => $region_id
        );

        if ($ssh_key_ids !== null) {
            $data['ssh_key_ids'] = $ssh_key_ids;
        }

        if ($private_networking === true) {
            $data['private_networking'] = 'true';
        }

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_droplet($droplet_id) {
        $url = 'droplets/{droplet_id}';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = 5;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_reboot_droplet($droplet_id) {
        $url = 'droplets/{droplet_id}/reboot';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_power_cycle_droplet($droplet_id) {
        $url = 'droplets/{droplet_id}/power_cycle';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_shutdown_droplet($droplet_id) {
        $url = 'droplets/{droplet_id}/shutdown';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_power_off_droplet($droplet_id) {
        $url = 'droplets/{droplet_id}/power_off';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_power_on_droplet($droplet_id) {
        $url = 'droplets/{droplet_id}/power_on';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_password_reset_droplet($droplet_id) {
        $url = 'droplets/{droplet_id}/password_reset';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_resize_droplet($droplet_id, $size_id) {
        $url = 'droplets/{droplet_id}/resize';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array(
            'size_id' => $size_id
        );

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_snapshot_droplet($droplet_id, $name = null) {
        $url = 'droplets/{droplet_id}/snapshot';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        if ($name !== null) {
            $data['name'] = $name;
        }

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_restore_droplet($droplet_id, $image_id) {
        $url = 'droplets/{droplet_id}/restore';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array(
            'image_id' => $image_id
        );

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_rebuild_droplet($droplet_id, $image_id) {
        $url = 'droplets/{droplet_id}/rebuild';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array(
            'image_id' => $image_id
        );

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_enable_droplet_backups($droplet_id) {
        $url = 'droplets/{droplet_id}/enable_backups';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_disable_droplet_backups($droplet_id) {
        $url = 'droplets/{droplet_id}/disable_backups';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_rename_droplet($droplet_id, $name) {
        $url = 'droplets/{droplet_id}/rename';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array(
            'name' => $name
        );

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_destroy_droplet($droplet_id, $scrub_data = false) {
        $url = 'droplets/{droplet_id}/destroy';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        if ($scrub_data === true) {
            $data['scrub_data'] = 'true';
        }

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    public function api_regions() {
        $url = 'regions';

        $params = array();

        $data = array();

        $cache = 60;

        return $this->request($url, $params, $data, $cache);
    }
}
