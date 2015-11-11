<?php
namespace C\Eloquent;

/**
 * Class Loader
 * overrides the original loader
 * in order to ensure the sqlite files
 * are touched ahead of time.
 *
 * @package C\Eloquent
 */
class Loader extends \C\Schema\Loader{

    /**
     * @var array
     */
    public $config;

    public function setCapsuleConfig ($config) {
        $this->config = $config;
    }

    /**
     * Initialize the file for an sqlite connection
     * @param $connections
     */
    public function initSqliteFiles ($connections) {
        foreach ($connections as $connection => $options) {
            if ($options["driver"]==='sqlite') {
                if ($options["database"]!==':memory:') {
                    $exists = file_exists($options['database']);
                    if (!$exists) {
                        $dir = dirname($options["database"]);
                        if (!is_dir($dir)) mkdir($dir, 0700, true);
                        touch($options["database"]);
                    }
                }
            }
        }
    }

    /**
     * Setup the sqlite file
     * when the db:init cli is invoked
     */
    public function loadSchemas(){
        $this->initSqliteFiles($this->config);
        parent::loadSchemas();
    }


}
