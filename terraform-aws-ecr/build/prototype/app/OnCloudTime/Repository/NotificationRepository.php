<?php

namespace App\Reports\Repository;

use App\Reports\Util\Container;
use App\Reports\Traits\Context;

class NotificationRepository extends BaseRepository {

    use context;

    public function getAllNotification($user) {
        //echo'<pre>';print_r($params);exit;
        $em = $this->getEntityManager();
        $context = self::getContext();
        $repository = $context['getRepositoryName'];
        $qb = $em->createQueryBuilder();
        $qb->select('a')
            ->from('App\\Reports\\Model\\Notification', 'a')
            ->Where('a.user = :user')->setMaxResults(10)
            ->addOrderBy('a.datecreated', 'DESC')
            ->setParameter(':user', $user);
        $query = $qb->getQuery();

        return $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    }

}
