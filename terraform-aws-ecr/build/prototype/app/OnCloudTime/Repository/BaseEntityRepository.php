<?php
namespace App\Reports\Repository;

use Doctrine\ORM\EntityRepository;
use App\Reports\Traits\Utils;
use App\Reports\Model\Entity;
use Pimple\Container as Pimple;

class BaseEntityRepository extends EntityRepository {
    use Utils;

    protected $context;

    public function setContext(Pimple $context) {
        $this->context = $context;
    }

    private function addEntity(Entity $entity) {
        $em = $this->getEntityManager();
        $this->throwException($entity->isValid());
        if ($this->preSave($entity) === false){
            $this->throwException([
                new \Exception('PreRemove failed')
            ]);
        }

        if ($entity->getId() === null) {
            $em->persist($entity);
        }
    }

    public function save(Entity $entity) {
        $this->addEntity($entity);
        $em = $this->getEntityManager();
        $em->flush();
        return $entity->getId();
    }

    public function batchSave(array $entites) {
        foreach ($entites as $entity) {
            $this->addEntity($entity);
        }

        $em = $this->getEntityManager();
        $em->flush();
    }

    private function deleteEntity(Entity $entity) {
        // if (! $this->preRemove($entity)) {
        //     $this->throwException([
        //         new \Exception('PreRemove failed')
        //     ]);
        // }
        var_dump($entity);exit;
        $em = $this->getEntityManager();
        $em->merge($entity);
        $em->remove($entity);
    }

    public function remove(Entity $entity) {
        $this->deleteEntity($entity);
        $em = $this->getEntityManager();
        $em->flush();
    }

    public function batchRemove(array $entites) {
        foreach ($entites as $entity) {
            $this->deleteEntity($entity);
        }

        $em = $this->getEntityManager();
        $em->flush();
    }

    public function preRemove(Entity $entity) {
        return true;
    }

    public function preSave(Entity $entity) {
        return true;
    }

    public function getEntityCount() {
        $qb = $this->createQueryBuilder('e')->select('count(e.id)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function listEntity(
        $limit = 25, $from = 0, $orderBy = null, $orderDir = 'ASC'
    ) {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->setFirstResult($from)
            ->setMaxResults($limit);

        if ($orderBy) {
            $qb->orderBy(sprintf('e.%s', $orderBy), $orderDir);
        }

        return $qb->getQuery()->getResult();
    }

    public function listAllEntity($orderBy = null, $orderDir = 'ASC') {
        $qb = $this->createQueryBuilder('e')
            ->select('e');

        if ($orderBy) {
            $qb->orderBy(sprintf('e.%s', $orderBy), $orderDir);
        }

        return $qb->getQuery()->getResult();
    }

    public function objectsToArray($entities) {
        $data = [];
        foreach ($entities as $entity) {
            array_push($data, $entity->toArray());
        }

        return $data;
    }

    public function findThatStartsWith(
        $property, $value, $limit = 25, $from = 0,
        $orderBy = null, $orderDir = 'ASC'
    ) {
        if (empty($property) || empty($value)) {
            $this->throwException([
                new \Exception('invalid input for "findThatStartsWith"')
            ]);
        }

        $where = sprintf('e.%s like :%s', $property, $property);
        $qb = $this->createQueryBuilder('e')
            ->where($where)
            ->setParameter($property, "$value%")
            ->setFirstResult($from)
            ->setMaxResults($limit);

        if ($orderBy) {
            $qb->orderBy(sprintf('e.%s', $orderBy), $orderDir);
        }

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function createRecord(
        Entity $entity, \Closure $callback, \Closure $findBy = null
    ) {
        $this->rejectIfDuplicate($entity, $callback, $findBy);

        return $this->save($entity);
    }

    public function updateRecord(Entity $entity, \Closure $callback = null) {
        $this->rejectIfDuplicateExceptThis($entity, $callback);

        return $this->save($entity);
    }

    public function removeRecord(Entity $entity, \Closure $callback = null) {
        $callback = is_null($callback) ? function () {} : $callback;
        $entity  = $this->find($entity->getId());
        if (is_null($entity)) {
            call_user_func($callback);
            return;
        }
        $this->remove($entity);
    }

    public function removeRecordById($id, \Closure $callback = null) {
        $callback = is_null($callback) ? function () {} : $callback;
        $entity  = $this->find($id);
        if (is_null($entity)) {
            call_user_func($callback);
            return;
        }
        $this->remove($entity);
    }

    public function rejectIfDuplicate(
        Entity $entity, \Closure $callback = null, \Closure $findBy = null
    ) {
        $callback = is_null($callback) ? function () {} : $callback;
        $repo = $this;

        if (is_null($findBy)) {
            $findBy = function () use ($entity, $repo) {
                $name = $entity->getName();
                return $repo->findBy(['name' => $name]);
            };
        }

        $entities = call_user_func($findBy, [$entity]);
        if (count($entities) > 0) {
            call_user_func($callback);
            $this->throwException([
                new \Exception('Duplicate Entity')
            ]);
        }
    }

    public function rejectIfDuplicateExceptThis(
        Entity $entity, \Closure $callback = null
    ) {
        $callback = is_null($callback) ? function () {} : $callback;
        $name = $entity->getName();
        $isDuplicate = $this->getDuplicateResult($entity->getId(), $name);
        if ($isDuplicate) {
            call_user_func($callback);
            $this->throwException([
                new \Exception('Duplicate Entity')
            ]);
        }
    }

    public function getDuplicateResult($id, $properties = []) {
        $qb = $this->createQueryBuilder('e');
        $qb->select('count(e.id)')->where('e.id != :id');
        foreach ($$properties as $column => $value) {
            $qb->andwhere(sprintf('e.%s = :%s', $column, $column));
            $qb->setParameter($column, $value);
        }
        $result = $qb->getQuery()->getSingleScalarResult();

        return $result > 0;
    }
}
?>
