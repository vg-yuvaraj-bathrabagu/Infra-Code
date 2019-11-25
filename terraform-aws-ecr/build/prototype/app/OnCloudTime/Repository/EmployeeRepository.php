<?php

namespace App\Reports\Repository;

use App\Reports\Util\Container;
use App\Reports\Traits\Context;

class EmployeeRepository extends BaseRepository {

    use context;

    public function getLogin($username, $password) {
        //echo'<pre>';print_r($params);exit;
        $em = $this->getEntityManager();
        $context = self::getContext();
        $repository = $context['getRepositoryName'];
        $qb = $em->createQueryBuilder();
        $qb->select('a.username, a.password')
            ->from('App\\Reports\\Model\\Employee', 'a')
            ->Where('a.username = :user')
            ->setParameter('user', $username)
            ->andWhere('a.password = :pass')
            ->setParameter('pass', $password);
        $query = $qb->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

    public function getAllUsers() {
        $em = $this->getEntityManager();
        $context = self::getContext();
        $repository = $context['getRepositoryName'];
        $qb = $em->createQueryBuilder();
        $qb->select('a')
            ->from('App\\Reports\\Model\\Employee', 'a');
        $query = $qb->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

}
