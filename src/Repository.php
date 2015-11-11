<?php
namespace C\Eloquent;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Class EloquentRepository
 * provide eloquent capsule to
 * service data providers
 *
 * @package C\Repository
 */
abstract class Repository extends \C\Repository\Repository{

    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    public $capsule;

    /**
     * @param Capsule $capsule
     */
    public function setCapsule(Capsule $capsule) {
        $this->capsule = $capsule;
    }
}