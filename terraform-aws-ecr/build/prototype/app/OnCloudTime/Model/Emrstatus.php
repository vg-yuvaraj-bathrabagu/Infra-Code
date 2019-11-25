<?php

namespace App\Reports\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customreport
 *
 * @ORM\Table(name="EMRStatus")
 * @ORM\Entity(repositoryClass="App\Reports\Repository\EMRStatusRepository")
 */
class Emrstatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=30, nullable=false)
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="text", length=16777215, nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=200, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="string", length=255, nullable=false)
     */
    private $comments;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ts_begin", type="datetime", nullable=false)
     */
    private $ts_begin;

    /**
     * @var integer
     *
     * @ORM\Column(name="nodes", type="integer", nullable=false)
     */
    private $nodes;


}
