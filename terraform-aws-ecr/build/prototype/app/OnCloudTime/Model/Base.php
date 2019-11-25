<?php
namespace App\Reports\Model;

use App\Reports\Model\Entity;
use App\Reports\Traits\Accessor;


abstract class Base implements Entity {
    use Accessor;

    public function jsonSerialize() {
        return $this->toArray();
    }
}
?>
