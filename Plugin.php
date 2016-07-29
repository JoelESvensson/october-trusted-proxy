<?php

namespace JoelESvensson\OcTrustedProxy;

use Fideloper\Proxy\TrustProxies;
use Illuminate\Contracts\Http\Kernel;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return array(
            'name'        => 'Trusted Proxy',
            'description' => '',
            'author'      => 'Joel E. Svensson',
            'icon'        => 'icon-server',
            'homepage'    => ''
        );
    }

    /**
     * @param string $ips
     * @return string|array
     */
    protected static function buildIpList($ips)
    {
        $ips = trim($ips);
        if ($ips === '*') {
            return '*';
        }

        /**
         * Create array of IP:s from comma separated string
         */
        return collect(explode(',', $ips))
            ->map(function ($ip) {
                return trim($ip);
            })
            ->toArray()
        ;
    }

    public function register()
    {
        $this->app->singleton(
            TrustProxies::class,
            function ($app) {
                $config = $app->make('config');
                $proxies = env('TRUSTED_PROXIES');
                if ($proxies) {
                    $proxies = static::buildIpList($proxies);
                } else {

                    /**
                     * Empty array means no trusted proxies
                     */
                    $proxies = [];
                }

                $config->set('trustedproxy.proxies', $proxies);
                return new TrustProxies($config);
            }
        );
    }

    public function boot()
    {
        $this->app->make(Kernel::class)->prependMiddleware(TrustProxies::class);
    }
}
