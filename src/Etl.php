<?php

namespace MikeWeb\CakeEtl;

use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;

class Etl implements EventDispatcherInterface {

    use EventDispatcherTrait;
}
