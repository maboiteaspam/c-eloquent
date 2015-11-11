<?php
namespace C\Eloquent;

use C\Schema\ISchema;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Class EloquentSchema
 * provide eloquent schema setup capability
 *
 * @package C\Eloquent\Schema
 */
abstract class Schema implements ISchema{

    /**
     * @var Capsule
     */
    public $capsule;

    /**
     * @param Capsule $capsule
     */
    public function __construct(Capsule $capsule) {
        $this->capsule = $capsule;
    }
}
