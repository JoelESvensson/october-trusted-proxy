<?php

namespace JoelESvensson\OcTrustedProxy;

use Fideloper\Proxy\TrustProxies;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return array(
            'name'        => 'Reverse Proxy',
            'description' => '',
            'author'      => 'Joel E. Svensson',
            'icon'        => 'icon-server',
            'homepage'    => ''
        );
    }

    public function register()
    {
        $this->app->singleton(
            TrustProxies::class,
            function ($app) {
                $config = $app->make('config');
                $proxies = env('TRUSTED_PROXIES');
                if ($proxies) {

                    /**
                     * Create array of IP:s from comma separated string
                     */
                    $proxies = collect(explode(',', $proxies))
                        ->map(function ($item) {
                            return trim($item);
                        })
                    ;
                } else {
                    $proxies = [];
                }

                $config->set('trustedproxy.proxies', $proxies);
                return new TrustProxies($config);
            }
        );
    }

    public function boot()
    {
        $this->app[\Illuminate\Contracts\Http\Kernel::class]
            ->prependMiddleware(TrustProxies::class)
        ;
    }
}
