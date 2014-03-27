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

namespace GrahamCampbell\DigitalOceanAPI\Classes;

use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use GrahamCampbell\CoreAPI\Classes\CoreAPI;
use GrahamCampbell\DigitalOceanAPI\Exceptions\DigitalOceanAPIException;

/**
 * This is the digitalocean api class.
 *
 * @package    Laravel-DigitalOcean-API
 * @author     Graham Campbell
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-DigitalOcean-API/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-DigitalOcean-API
 */
class DigitalOceanAPI extends CoreAPI
{

    /**
     * The client id.
     *
     * @var string
     */
    protected $id;

    /**
     * The api key.
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new instance.
     *
     * @param  \Illuminate\Cache\CacheManager  $cache
     * @param  \Illuminate\Config\Repository   $config
     * @return void
     */
    public function __construct(CacheManager $cache, Repository $config)
    {
        parent::__construct($cache, $config);

        $this->id = $this->config['graham-campbell/digitalocean-api::id'];
        $this->key = $this->config['graham-campbell/digitalocean-api::key'];

        $this->setup($this->config['graham-campbell/digitalocean-api::baseurl']);
    }

    /**
     * Reset the base url.
     *
     * @return $this
     */
    public function resetBaseUrl()
    {
        return $this->setBaseUrl($this->config['graham-campbell/digitalocean-api::baseurl']);
    }

    /**
     * Get the client id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the client id.
     *
     * @param  string  $id
     * @return $this
     */
    public function setId($id)
    {
        if (!is_string($id)) {
            $id = '';
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Reset the client id.
     *
     * @return $this
     */
    public function resetId()
    {
        return $this->setId($this->config['graham-campbell/digitalocean-api::id']);
    }

    /**
     * Get the api key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the api key.
     *
     * @param  string  $id
     * @return $this
     */
    public function setKey($key)
    {
        if (!is_string($key)) {
            $key = '';
        }

        $this->key = $key;

        return $this;
    }

    /**
     * Reset the api key.
     *
     * @return $this
     */
    public function resetKey()
    {
        return $this->setKey($this->config['graham-campbell/digitalocean-api::key']);
    }

    /**
     * Send a request.
     *
     * @param  string    $action
     * @param  array     $params
     * @param  array     $data
     * @param  bool|int  $cache
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function request($action, array $params, array $data, $cache = false)
    {
        foreach ($params as $key => $value) {
            $action = str_replace('{' . $key . '}', $value, $action);
        }

        $url = $this->baseurl . $action . '/?client_id=' . $this->id . '&api_key=' . $this->key;

        foreach ($data as $key => $value) {
            $url = $url . '&' . $key . '=' . $value;
        }

        $response = $this->get($url, null, array(), $cache);

        try {
            $body = $response->json();
        } catch (\Exception $e) {
            // ignore the exception
        }

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

    /*  Droplets */

    /**
     * List the droplets.
     *
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiListDroplets()
    {
        $url = 'droplets';

        $params = array();

        $data = array();

        $cache = 15;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Create a new droplet.
     *
     * @param  array  $droplet
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiCreateDroplet(array $droplet)
    {
        $url = 'droplets/new';

        $params = array();

        $data = array(
            'name' => $droplet['name'],
            'size_id' => $droplet['size_id'],
            'image_id' => $droplet['image_id'],
            'region_id' => $droplet['region_id']
        );

        if (array_key_exists('ssh_key_ids', $droplet)) {
            if (is_array($droplet['ssh_key_ids'])) {
                $data['ssh_key_ids'] = implode(',', $droplet['ssh_key_ids']);
            }
        }

        if (array_key_exists('private_networking', $droplet)) {
            if ($droplet['private_networking']) {
                $data['private_networking'] = 'true';
            } else {
                $data['private_networking'] = 'false';
            }
        }

        if (array_key_exists('backups_enabled', $droplet)) {
            if ($droplet['backups_enabled']) {
                $data['backups_enabled'] = 'true';
            } else {
                $data['backups_enabled'] = 'false';
            }
        }

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Get a droplet.
     *
     * @param  int  $droplet_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiGetDroplet($droplet_id)
    {
        $url = 'droplets/{droplet_id}';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = 5;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Reboot a droplet.
     *
     * @param  int  $droplet_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiRebootDroplet($droplet_id)
    {
        $url = 'droplets/{droplet_id}/reboot';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Power cycle a droplet.
     *
     * @param  int  $droplet_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiPowerCycleDroplet($droplet_id)
    {
        $url = 'droplets/{droplet_id}/power_cycle';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Shutdown a droplet.
     *
     * @param  int  $droplet_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiShutdownDroplet($droplet_id)
    {
        $url = 'droplets/{droplet_id}/shutdown';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Power off a droplet.
     *
     * @param  int  $droplet_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiPowerOffdroplet($droplet_id)
    {
        $url = 'droplets/{droplet_id}/power_off';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Power on a droplet.
     *
     * @param  int  $droplet_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiPowerOnDroplet($droplet_id)
    {
        $url = 'droplets/{droplet_id}/power_on';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Password reset a droplet.
     *
     * @param  int  $droplet_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiPasswordResetDroplet($droplet_id)
    {
        $url = 'droplets/{droplet_id}/password_reset';

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Resize a droplet.
     *
     * @param  int  $droplet_id
     * @param  int  $size_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiResizeDroplet($droplet_id, $size_id)
    {
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

    /**
     * Snapshot a droplet.
     *
     * @param  int     $droplet_id
     * @param  string  $name
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiSnapshotDroplet($droplet_id, $name = null)
    {
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

    /**
     * Restore a droplet.
     *
     * @param  int  $droplet_id
     * @param  int  $image_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiRestoreDroplet($droplet_id, $image_id)
    {
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

    /**
     * Rebuild a droplet.
     *
     * @param  int  $droplet_id
     * @param  int  $image_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiRebuildDroplet($droplet_id, $image_id)
    {
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

    /**
     * Backup a droplet.
     *
     * @param  int   $droplet_id
     * @param  bool  $value
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiBackupDroplet($droplet_id, $value = true)
    {
        if ($value) {
            $url = 'droplets/{droplet_id}/enable_backups';
        } else {
            $url = 'droplets/{droplet_id}/disable_backups';
        }

        $params = array(
            'droplet_id' => $droplet_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Rename a droplet.
     *
     * @param  int     $droplet_id
     * @param  string  $name
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiRenameDroplet($droplet_id, $name)
    {
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

    /**
     * Destroy a droplet.
     *
     * @param  int   $droplet_id
     * @param  bool  $scrub_data
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiDestroyDroplet($droplet_id, $scrub_data = false)
    {
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

    /* Regions */

    /**
     * Get a list of regions.
     *
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiListRegions()
    {
        $url = 'regions';

        $params = array();

        $data = array();

        $cache = 60;

        return $this->request($url, $params, $data, $cache);
    }

    /* Sizes */

    /**
     * Get a list of sizes.
     *
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiListSizes()
    {
        $url = 'sizes';

        $params = array();

        $data = array();

        $cache = 60;

        return $this->request($url, $params, $data, $cache);
    }

    /* Images */

    /**
     * Get a list of images.
     *
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiListImages()
    {
        $url = 'images';

        $params = array();
        
        $data = array();
        
        $cache = 60;
        
        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Get an image.
     *
     * @param  int  $image_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiGetImage($image_id)
    {
        $url = 'images/{image_id}';

        $params = array(
            'image_id' => $image_id
        );

        $data = array();

        $cache = 5;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Destroy an image.
     *
     * @param  int  $image_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiDestroyImage($image_id)
    {
        $url = 'images/{image_id}';

        $params = array(
            'image_id' => $image_id
        );

        $data = array();

        $cache = 5;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Transfer an image.
     *
     * @param  int  $image_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiTransferImage($image_id, $region_id)
    {
        $url = 'images/{image_id}/transfer';

        $params = array(
            'image_id' => $image_id
        );

        $data = array(
            'region_id' => $region_id
        );

        $cache = 5;

        return $this->request($url, $params, $data, $cache);
    }

    /* Ssh Keys */

    /**
     * Get a list of Ssh Keys.
     *
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiListSshKeys()
    {
        $url = 'ssh_keys';

        $params = array();
        
        $data = array();
        
        $cache = 60;
        
        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Add an Ssh key.
     *
     * @param  string  $name
     * @param  string  $ssh_pub_key
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiAddSshKey($name, $ssh_pub_key)
    {
        $url = 'ssh_keys/new';

        $params = array();

        $data = array(
            'name' => $name,
            'ssh_pub_key' => $ssh_pub_key
        );

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Get an Ssh Key.
     *
     * @param  int  $ssh_key_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiGetSshKey($ssh_key_id)
    {
        $url = 'ssh_keys/{ssh_key_id}';

        $params = array(
            'ssh_key_id' => $ssh_key_id
        );

        $data = array();

        $cache = 20;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Edit an Ssh Key.
     *
     * @param  int  $ssh_key_id
     * @param  string  $ssh_pub_key
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiEditSshKey($ssh_key_id, $ssh_pub_key)
    {
        $url = 'ssh_keys/{ssh_key_id}/edit';

        $params = array(
            'ssh_key_id' => $ssh_key_id
        );

        $data = array(
            'ssh_pub_key' => $ssh_pub_key
        );

        $cache = 20;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Destroy an Ssh Key.
     *
     * @param  int  $ssh_key_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiDestroySshKey($ssh_key_id)
    {
        $url = 'ssh_keys/{ssh_key_id}/destroy';

        $params = array(
            'ssh_key_id' => $ssh_key_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /* Domains */

    /**
     * Get a list of Domains.
     *
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiListDomains()
    {
        $url = 'domains';
        
        $params = array();
        
        $data = array();
        
        $cache = 60;
        
        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Add a Domain.
     *
     * @param  string  $name
     * @param  string  $ip_address
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiAddDomain($name, $ip_address)
    {
        $url = 'domains/new';

        $params = array();

        $data = array(
            'name' => $name,
            'ip_address' => $ip_address
        );

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Get a Domain.
     *
     * @param  int  $domain_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiGetDomain($domain_id)
    {
        $url = 'domains/{domain_id}';

        $params = array(
            'domain_id' => $domain_id
        );

        $data = array();

        $cache = 20;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Destroy an Domain.
     *
     * @param  int  $domain_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiDestroyDomain($domain_id)
    {
        $url = 'domains/{domain_id}/destroy';

        $params = array(
            'domain_id' => $domain_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Get Domain records.
     *
     * @param  int  $domain_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiGetDomainRecords($domain_id)
    {
        $url = 'domains/{domain_id}/records';

        $params = array(
            'domain_id' => $domain_id
        );

        $data = array();

        $cache = 20;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Add a Domain Record.
     *
     * @param  array  $domain_record
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiAddDomainRecord($domain_id, $domain_record)
    {
        $url = 'domains/{domain_id}/records/new';

        $params = array(
            'domain_id' => $domain_id
        );

        $data = array(
            'record_type' => $domain_record['record_type'],
            'data' => $domain_record['data']
        );

        if (array_key_exists('name', $domain_record)) {
            $data['name'] = $domain_record['name'];
        }

        if (array_key_exists('priority', $domain_record)) {
            $data['priority'] = $domain_record['priority'];
        }

        if (array_key_exists('port', $domain_record)) {
            $data['port'] = $domain_record['port'];
        }

        if (array_key_exists('weight', $domain_record)) {
            $data['weight'] = $domain_record['weight'];
        }

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Get a Domain Record.
     *
     * @param  int  $domain_id
     * @param  int  $record_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiGetDomainRecord($domain_id, $record_id)
    {
        $url = 'domains/{domain_id}/records';

        $params = array(
            'domain_id' => $domain_id,
            'record_id' => $record_id
        );

        $data = array();

        $cache = 20;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Add a Domain Record.
     *
     * @param  array  $domain_record
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiEditDomainRecord($domain_id, $record_id, $domain_record)
    {
        $url = 'domains/{domain_id}/records/{record_id}/edit';

        $params = array(
            'domain_id' => $domain_id,
            'record_id' => $record_id
        );

        $data = array(
            'record_type' => $domain_record['record_type'],
            'data' => $domain_record['data']
        );

        if (array_key_exists('name', $domain_record)) {
            $data['name'] = $domain_record['name'];
        }

        if (array_key_exists('priority', $domain_record)) {
            $data['priority'] = $domain_record['priority'];
        }

        if (array_key_exists('port', $domain_record)) {
            $data['port'] = $domain_record['port'];
        }

        if (array_key_exists('weight', $domain_record)) {
            $data['weight'] = $domain_record['weight'];
        }

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /**
     * Destroy a Domain Record.
     *
     * @param  int  $domain_id
     * @param  int  $record_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiDestroyDomainRecord($domain_id, $record_id)
    {
        $url = 'domains/{domain_id}/records/{record_id}/destroy';

        $params = array(
            'domain_id' => $domain_id,
            'record_id' => $record_id
        );

        $data = array();

        $cache = false;

        return $this->request($url, $params, $data, $cache);
    }

    /* Events */

    /**
     * Get an event.
     *
     * @param  int  $event_id
     * @return \GrahamCampbell\CoreAPI\Classes\APIResponse
     */
    public function apiGetEvent($event_id)
    {
        $url = 'events/{event_id}';

        $params = array(
            'event_id' => $event_id
        );

        $data = array();

        $cache = 20;

        return $this->request($url, $params, $data, $cache);
    }

}
