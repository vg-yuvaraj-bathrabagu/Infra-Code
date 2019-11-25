<?php

namespace App\Reports\Traits;

use App\Reports\Util\Container;

trait Context {

    public static function getContext() {
        return Container::getAppContext();
    }

}
