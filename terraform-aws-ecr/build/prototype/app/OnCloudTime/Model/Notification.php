<?php

namespace App\Reports\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customreport
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="App\Reports\Repository\NotificationRepository")
 */
class Notification extends Base
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=30, nullable=false)
     */
    protected $action;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255, nullable=true)
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datecreated", type="datetime", nullable=false)
     */
    protected $datecreated;


    public function toArray() {
        return ['id' => $this->id,
                'action' => $this->action,
        ];
    }

    public function toString() {
        return (string)$this->id;
    }


}
