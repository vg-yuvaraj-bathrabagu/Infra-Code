<?php

namespace App\Reports\Repository;

use App\Reports\Util\Container;
use App\Reports\Traits\Context;

class EMRStatusRepository extends BaseRepository {

    use context;

    public function getAllReports() {
        //echo'<pre>';print_r($params);exit;
        $em = $this->getEntityManager();
        $context = self::getContext();
        $repository = $context['getRepositoryName'];
        $qb = $em->createQueryBuilder();
        $qb->select('a')
            ->from('App\\Reports\\Model\\EMRStatus', 'a');
        $query = $qb->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

}
