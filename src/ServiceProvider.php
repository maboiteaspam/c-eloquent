<?php
namespace C\Eloquent;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

/**
 * Class ServiceProvider
 *
 * provides support of Eloquent database framework
 *
 * @package C\Eloquent
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        // set the default database connection credentials
        // it s a root-mysql-localhost
        $app['capsule.connection_defaults'] = array(
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => null,
            'username' => 'root',
            'password' => null,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => null,
            'logging' => false,
        );

        // Set capsule behavior and requirements
        $app['capsule.global'] = true;
        $app['capsule.eloquent'] = true;
        $app['capsule.container'] = $app->share(function() {
            return new Container;
        });

        $app['capsule.dispatcher'] = $app->share(function(Application $app) {
            return new Dispatcher($app['capsule.container']);
        });

        /*
        if (class_exists('Illuminate\Cache\CacheManager')) {
            $app['capsule.cache_manager'] = $app->share(function() use($app) {
                return new CacheManager($app['capsule.container']);
            });
        }
         */

        // Initialize the capsule provider
        $app['capsule'] = $app->share(function(Application $app) {

            $capsule = new Capsule($app['capsule.container']);

            $capsule->setEventDispatcher($app['capsule.dispatcher']);
            if (isset($app['capsule.cache_manager'])
                && isset($app['capsule.cache'])) {
//                $capsule->setCacheManager($app['capsule.cache_manager']);
                foreach ($app['capsule.cache'] as $key => $value) {
                    $app['capsule.container']
                        ->offsetGet('config')
                        ->offsetSet('cache.' . $key, $value);
                }
            }
            if ($app['capsule.global']) {
                $capsule->setAsGlobal();
            }
            if ($app['capsule.eloquent']) {
                $capsule->bootEloquent();
            }

            // you can use capsule.connection as the default,
            // or capsule.connections, to declare multiple connections
            if (!isset($app['capsule.connections'])) {
                $app['capsule.connections'] = array(
                    'default' => (isset($app['capsule.connection'])
                        ? $app['capsule.connection'] : array()),
                );
            }

            // add the connections to the capsule
            // use the defaults of capsule.connection_defaults
            foreach ($app['capsule.connections'] as $connection => $options) {
                $options = array_replace($app['capsule.connection_defaults'], $options);
                $capsule->addConnection($options, $connection);
            }

            // you can use capsule.use_connection to define the connection to use
            // otherwise it fallback to the environment name
            if (!isset($app['capsule.use_connection'])) {
                if (isset($app['env'])) $app['capsule.use_connection'] = $app['env'];
            }
            // always set a default connection
            if (!isset($app['capsule.connections']['default'])) {
                $capsule->addConnection(
                    $app['capsule.connections'][$app['capsule.use_connection']],
                    'default');
            }

            // configure the default connection to use
            if (isset($app['capsule.use_connection'])) {
                $capsule
                    ->getDatabaseManager()
                    ->setDefaultConnection($app['capsule.use_connection']);
            }

            return $capsule;

        });
    }
    public function boot(Application $app)
    {
        // a new tag computer is registered
        // to bind sql queries to the cache system
        if (isset($app['httpcache.tagger'])) {
            $tagger = $app['httpcache.tagger'];
            $capsule = $app['capsule'];
            /* @var $tagger \C\TagableResource\ResourceTagger */
            $tagger->addTagComputer('sql', function ($sql) use($capsule) {
                $k = $capsule->getConnection()->select($sql);
                return $k;
            });
        }

        // create sqlite database file prior eloquent boot
        $connection = null;
        if (isset($app['capsule.connection'])) {
            $connection = $app['capsule.connection'];
        }
        if (isset($app['capsule.use_connection'])) {
            $connection = $app['capsule.use_connection'];
        }
        if ($connection===null) {
            $connection = 'default';
        }
        if (isset($app['capsule.connections'])) {
            if (isset($app['capsule.connections'][$connection])) {
                $connection = $app['capsule.connections'][$connection];
                if ($connection["driver"]==='sqlite') {
                    if ($connection["database"]!==':memory:') {
                        $exists = file_exists($connection['database']);
                        if (!$exists) {
                            $dir = dirname($connection["database"]);
                            if (!is_dir($dir)) mkdir($dir, 0700, true);
                            touch($connection["database"]);
                        }
                    }
                }
            }
        }

        // enable query logging
        $app->before(function () use($app) {
            $connections = $app['capsule.connections'];
            $capsule = $app['capsule'];
            foreach ($connections as $connection => $options) {
                $options = array_replace($app['capsule.connection_defaults'], $options);
                if ($options['logging']) {
                    $capsule->connection($connection)->enableQueryLog();
                }
            }
        });
    }
}