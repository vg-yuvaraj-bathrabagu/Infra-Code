<?php
namespace App\Reports\Model;


use Doctrine\ORM\Mapping as ORM;

/**
 * Employeerelationship
 *
 * @ORM\Table(name="employeerelationship", uniqueConstraints={@ORM\UniqueConstraint(name="uniquerelationship", columns={"employeeid", "contract", "ponumber", "taskcode", "blccode", "jobid", "relationshipstartdate", "relationshipenddate"})}, indexes={@ORM\Index(name="contract", columns={"contract"}), @ORM\Index(name="employee_jobid", columns={"jobid", "employeeid"}), @ORM\Index(name="EmpId", columns={"employeeid"}), @ORM\Index(name="EmpIdJobContract", columns={"employeeid", "jobnumber", "ponumber", "contract"}), @ORM\Index(name="IDX_TASKCODE", columns={"taskcode"})})
 * @ORM\Entity(repositoryClass="App\Reports\Repository\EmployeeRelationshipRepository")
 */
class Employeerelationship extends Base
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
     * @var integer
     *
     * @ORM\Column(name="employeeid", type="bigint", nullable=false)
     */
    protected $employeeid;

    /**
     * @var string
     *
     * @ORM\Column(name="contract", type="string", length=100, nullable=false)
     */
    protected $contract;

    /**
     * @var string
     *
     * @ORM\Column(name="ponumber", type="string", length=10, nullable=true)
     */
    protected $ponumber = '0000000000';

    /**
     * @var string
     *
     * @ORM\Column(name="taskcode", type="string", length=200, nullable=true)
     */
    protected $taskcode;

    /**
     * @var string
     *
     * @ORM\Column(name="blccode", type="string", length=15, nullable=true)
     */
    protected $blccode;

    /**
     * @var integer
     *
     * @ORM\Column(name="jobid", type="bigint", nullable=false)
     */
    protected $jobid;

    /**
     * @var string
     *
     * @ORM\Column(name="jobnumber", type="string", length=30, nullable=false)
     */
    protected $jobnumber = 'NA';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="relationshipstartdate", type="string", nullable=false)
     */
    protected $relationshipstartdate = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="relationshipenddate", type="string", nullable=false)
     */
    protected $relationshipenddate = '2038-01-01 00:00:00';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastupdatedby", type="bigint", nullable=true)
     */
    protected $lastupdatedby;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdatedate", type="datetime", nullable=true)
     */
    protected $lastupdatedate;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=16777215, nullable=true)
     */
    protected $comment;

    /**
     * @var integer
     *
     * @ORM\Column(name="createdby", type="bigint", nullable=false)
     */
    protected $createdby = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datecreated", type="string", nullable=false)
     */
    protected $datecreated = '0000-00-00 00:00:00';

    public function toArray() {
        return ['id' => $this->id
        ];
    }

    public function toString() {
        return (string)$this->id;
    }


}
