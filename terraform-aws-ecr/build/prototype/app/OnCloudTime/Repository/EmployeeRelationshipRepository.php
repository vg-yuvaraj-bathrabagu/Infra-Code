<?php

namespace App\Reports\Repository;

use App\Reports\Util\Container;
use App\Reports\Traits\Context;

class EmployeeRelationshipRepository extends BaseRepository {
    use context;

    public function getPermissions() {
        $em = $this->getEntityManager();
        $context = self::getContext();
        $repository = $context['getRepositoryName'];
        $qb = $em->createQueryBuilder();
        $qb->select('a')
            ->from('App\\Reports\\Model\\Employeerelationship', 'a');
            $qb->join('App\\Reports\\Model\\Employee', 'ae', \Doctrine\ORM\Query\Expr\Join::WITH, 'a.employeeid = ae.id');
        $query = $qb->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }
}
