<?php
namespace App\Reports\Traits;

use Doctrine\Common\Inflector\Inflector;

trait Accessor {
    public function __call($method, $args) {
        return $this->getOrSetProperty($method, $args);
    }

    public function fromArray(array $values) {
        foreach ($values as $property => $value) {
            if (property_exists($this, $property)) {
                $this->setProperty($property, $value);
            }
        }
    }

    public function setProperty($property, $value) {
        $this->$property = $value;
    }

    public function getProperty($property) {
        return $this->$property;
    }

    public function getMutatorTypeAndPropertyName($method) {
        $matches = [];
        $pattern = '/^((?:get)|(?:set)|(?:add)|(?:clear))(.+)$/';
        preg_match($pattern, $method, $matches);
        if (count($matches) === 0) {
            $message = [
                'Only Getters or Setters allowed.',
                "'$method' must start with one of ['get', 'set', 'add', 'clear']"
            ];

            throw new \Exception(join('', $message));
        }
        return [
            'type' => $matches[1],
            'property' => Inflector::camelize($matches[2])
        ];
    }

    public function getPropertyAndValueFrom($method, array $args = []) {
        $mutator = $this->getMutatorTypeAndPropertyName($method);
        $options = [];
        $value = null;
        $type = $mutator['type'];
        $property = $mutator['property'];
        if ($type === 'add' && !is_array($args[0])) {
            $property = Inflector::pluralize($property);

        }

        if ($type === 'clear') {
            $value = [];
        }
        elseif ($type === 'add') {
            $arr = [];
            if (!is_array($args[0])) {
                array_push($arr, $args[0]);
            }
            else {
                $arr = $args[0];
            }
            $options['array_merge'] = true;
            $options['array_unique'] = true;
            $value = $arr;
        }
        elseif ($type === 'set') {
            $value = $args[0];
        }
        else {
            $value = null;
            $options['getter'] = true;
        }

        $ret = [$property, $value, $options];

        return $ret;
    }

    public function getOrSetProperty($method, $args) {
        $propVal = $this->getPropertyAndValueFrom($method, $args);
        $property = $propVal[0];
        $val = $propVal[1];
        $options = $propVal[2];

        if (!property_exists($this, $property)) {
            throw new \Exception("No such property: $property");
        }

        if (isset($options['getter']) && $options['getter'] === true) {
            return $this->getProperty($property);
        }

        $this->setProperty($property, $val);

        return $this;
    }
}
?>
