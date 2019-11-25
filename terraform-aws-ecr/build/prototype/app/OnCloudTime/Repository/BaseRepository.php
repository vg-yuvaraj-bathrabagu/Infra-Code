<?php

namespace App\Reports\Repository;

use Doctrine\ORM\EntityRepository;
use App\Reports\Model\Entity;

class BaseRepository extends EntityRepository {

    public function save(Entity $entity) {

        $this->addEntity($entity);
        $em = $this->getEntityManager();
        $em->flush();
        return $entity->getId();
    }

    private function addEntity(Entity $entity) {
        $em = $this->getEntityManager();
        //$this->throwException($entity->isValid());
        if ($this->preSave($entity) === false){
            $this->throwException([
                new \Exception('PreRemove failed')
            ]);
        }


        if ($entity->getId() === null) {
            $em->persist($entity);
        } else {
            $em->merge($entity);
        }
    }

    public function preSave(Entity $entity) {
        return true;
    }

    private function deleteEntity(Entity $entity) {
        if (! $this->preRemove($entity)) {
            $this->throwException([
                new \Exception('PreRemove failed')
            ]);
        }

        $em = $this->getEntityManager();
        $em->remove($entity);
    }

    public function remove(Entity $entity) {
        $this->deleteEntity($entity);
        $em = $this->getEntityManager();
        $em->flush();
    }

    public function preRemove(Entity $entity) {
        return true;
    }

    public function objectsToArray($entities) {
        $data = [];
        foreach ($entities as $entity) {
            array_push($data, $entity->toArray());
        }

        return $data;
    }
}
