<?php
namespace App\Reports\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Employee
 *
 * @ORM\Table(name="employee", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"}), @ORM\UniqueConstraint(name="emailaddress", columns={"emailaddress"})}, indexes={@ORM\Index(name="account", columns={"account"}), @ORM\Index(name="changedpassword", columns={"changedpassword"}), @ORM\Index(name="employeetype", columns={"employeetype"}), @ORM\Index(name="gender", columns={"gender"}), @ORM\Index(name="invoicecategory", columns={"invoicecategory"}), @ORM\Index(name="isactive", columns={"isactive"}), @ORM\Index(name="isadmin", columns={"isadmin"}), @ORM\Index(name="status", columns={"status"}), @ORM\Index(name="EmpId", columns={"id"})})
 * @ORM\Entity(repositoryClass="App\Reports\Repository\EmployeeRepository")
 */
class Employee extends Base
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
     * @ORM\Column(name="employeetemplateid", type="bigint", nullable=true)
     */
    protected $employeetemplateid;

    /**
     * @var string
     *
     * @ORM\Column(name="salutation", type="string", length=25, nullable=true)
     */
    protected $salutation;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    protected $username = '';

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    protected $password = '';

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=false)
     */
    protected $firstname = '';

    /**
     * @var string
     *
     * @ORM\Column(name="middlename", type="string", length=255, nullable=true)
     */
    protected $middlename;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=false)
     */
    protected $lastname = '';

    /**
     * @var string
     *
     * @ORM\Column(name="initials", type="string", length=255, nullable=true)
     */
    protected $initials;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", nullable=true)
     */
    protected $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="jobtitle", type="string", length=255, nullable=false)
     */
    protected $jobtitle = '';

    /**
     * @var string
     *
     * @ORM\Column(name="blccode", type="string", length=255, nullable=true)
     */
    protected $blccode;

    /**
     * @var string
     *
     * @ORM\Column(name="jobcategory", type="string", length=255, nullable=true)
     */
    protected $jobcategory;

    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=255, nullable=true)
     */
    protected $department;

    /**
     * @var string
     *
     * @ORM\Column(name="joblocation", type="string", length=255, nullable=true)
     */
    protected $joblocation;

    /**
     * @var string
     *
     * @ORM\Column(name="employeetype", type="string", length=10, nullable=true)
     */
    protected $employeetype;

    /**
     * @var string
     *
     * @ORM\Column(name="rateperhour", type="string", length=10, nullable=false)
     */
    protected $rateperhour = '';

    /**
     * @var string
     *
     * @ORM\Column(name="account", type="string", length=10, nullable=false)
     */
    protected $account = '';

    /**
     * @var string
     *
     * @ORM\Column(name="emailaddress", type="string", length=255, nullable=false)
     */
    protected $emailaddress = '';

    /**
     * @var string
     *
     * @ORM\Column(name="personalemailaddress", type="string", length=255, nullable=true)
     */
    protected $personalemailaddress;

    /**
     * @var string
     *
     * @ORM\Column(name="isactive", type="string", nullable=false)
     */
    protected $isactive = 'Y';

    /**
     * @var string
     *
     * @ORM\Column(name="isadmin", type="string", nullable=false)
     */
    protected $isadmin = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="onvacation", type="string", nullable=false)
     */
    protected $onvacation = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="usertype", type="string", nullable=true)
     */
    protected $usertype = 'Normal';

    /**
     * @var string
     *
     * @ORM\Column(name="mobilenumber", type="string", length=255, nullable=true)
     */
    protected $mobilenumber;

    /**
     * @var string
     *
     * @ORM\Column(name="tsgbadgenumber", type="string", length=255, nullable=true)
     */
    protected $tsgbadgenumber;

    /**
     * @var string
     *
     * @ORM\Column(name="officeline1", type="string", length=50, nullable=true)
     */
    protected $officeline1;

    /**
     * @var string
     *
     * @ORM\Column(name="officeline2", type="string", length=50, nullable=true)
     */
    protected $officeline2;

    /**
     * @var string
     *
     * @ORM\Column(name="faxnumber", type="string", length=50, nullable=true)
     */
    protected $faxnumber;

    /**
     * @var string
     *
     * @ORM\Column(name="address1", type="string", length=255, nullable=true)
     */
    protected $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="address2", type="string", length=255, nullable=true)
     */
    protected $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="stateorprovince", type="string", length=255, nullable=true)
     */
    protected $stateorprovince;

    /**
     * @var string
     *
     * @ORM\Column(name="postalcode", type="string", length=50, nullable=true)
     */
    protected $postalcode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="companyname", type="string", length=255, nullable=true)
     */
    protected $companyname = '';

    /**
     * @var string
     *
     * @ORM\Column(name="companyaddress", type="string", length=255, nullable=true)
     */
    protected $companyaddress = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ssn", type="string", length=20, nullable=true)
     */
    protected $ssn;

    /**
     * @var string
     *
     * @ORM\Column(name="ein", type="string", length=20, nullable=true)
     */
    protected $ein;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="datetime", nullable=true)
     */
    protected $birthdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hiredate", type="datetime", nullable=true)
     */
    protected $hiredate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="employeestartdate", type="datetime", nullable=true)
     */
    protected $employeestartdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="employeeenddate", type="datetime", nullable=true)
     */
    protected $employeeenddate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="releasedate", type="datetime", nullable=true)
     */
    protected $releasedate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastreviewdate", type="datetime", nullable=true)
     */
    protected $lastreviewdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="nextreviewdate", type="datetime", nullable=true)
     */
    protected $nextreviewdate;

    /**
     * @var string
     *
     * @ORM\Column(name="supervisorname", type="string", length=255, nullable=true)
     */
    protected $supervisorname;

    /**
     * @var string
     *
     * @ORM\Column(name="supervisoremail", type="string", length=255, nullable=true)
     */
    protected $supervisoremail;

    /**
     * @var string
     *
     * @ORM\Column(name="supervisorofficeline", type="string", length=255, nullable=true)
     */
    protected $supervisorofficeline;

    /**
     * @var string
     *
     * @ORM\Column(name="payrollfrequency", type="string", length=50, nullable=true)
     */
    protected $payrollfrequency;

    /**
     * @var string
     *
     * @ORM\Column(name="paycode", type="string", length=10, nullable=true)
     */
    protected $paycode;

    /**
     * @var string
     *
     * @ORM\Column(name="regularrate", type="decimal", precision=10, scale=2, nullable=false)
     */
    protected $regularrate = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="regularratecode", type="string", length=10, nullable=true)
     */
    protected $regularratecode;

    /**
     * @var string
     *
     * @ORM\Column(name="maximumregularhours", type="decimal", precision=10, scale=1, nullable=false)
     */
    protected $maximumregularhours = '8.0';

    /**
     * @var string
     *
     * @ORM\Column(name="overtimeallowed", type="string", nullable=false)
     */
    protected $overtimeallowed = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="overtimerate", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $overtimerate;

    /**
     * @var string
     *
     * @ORM\Column(name="overtimeratecode", type="string", length=10, nullable=true)
     */
    protected $overtimeratecode;

    /**
     * @var string
     *
     * @ORM\Column(name="maximumovertimehoursperday", type="decimal", precision=10, scale=1, nullable=true)
     */
    protected $maximumovertimehoursperday;

    /**
     * @var string
     *
     * @ORM\Column(name="maximumhoursperday", type="decimal", precision=10, scale=0, nullable=false)
     */
    protected $maximumhoursperday = '';

    /**
     * @var string
     *
     * @ORM\Column(name="maximumhoursperweek", type="decimal", precision=10, scale=0, nullable=false)
     */
    protected $maximumhoursperweek = '';

    /**
     * @var string
     *
     * @ORM\Column(name="maximumovertimehours", type="decimal", precision=10, scale=1, nullable=true)
     */
    protected $maximumovertimehours;

    /**
     * @var string
     *
     * @ORM\Column(name="vacationdaysallowed", type="string", nullable=false)
     */
    protected $vacationdaysallowed = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="maximumsocialsecurity", type="decimal", precision=10, scale=0, nullable=true)
     */
    protected $maximumsocialsecurity;

    /**
     * @var string
     *
     * @ORM\Column(name="maximumvacationdays", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $maximumvacationdays;

    /**
     * @var string
     *
     * @ORM\Column(name="personaldaysallowed", type="string", nullable=false)
     */
    protected $personaldaysallowed = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="maximumpersonaldays", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $maximumpersonaldays;

    /**
     * @var string
     *
     * @ORM\Column(name="paidholidaysallowed", type="string", nullable=false)
     */
    protected $paidholidaysallowed = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="maximumpaidholidays", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $maximumpaidholidays;

    /**
     * @var string
     *
     * @ORM\Column(name="sickdaysallowed", type="string", nullable=false)
     */
    protected $sickdaysallowed = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="maximumsickdays", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $maximumsickdays;

    /**
     * @var string
     *
     * @ORM\Column(name="saturdayworkallowed", type="string", nullable=false)
     */
    protected $saturdayworkallowed = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="sundayworkallowed", type="string", nullable=false)
     */
    protected $sundayworkallowed = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="publicholidayworkallowed", type="string", nullable=false)
     */
    protected $publicholidayworkallowed = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="employeebenefitsbillable", type="string", nullable=false)
     */
    protected $employeebenefitsbillable = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="invoicecategory", type="string", length=50, nullable=true)
     */
    protected $invoicecategory;

    /**
     * @var string
     *
     * @ORM\Column(name="employeejobs", type="string", length=240, nullable=true)
     */
    protected $employeejobs;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;

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

    /**
     * @var string
     *
     * @ORM\Column(name="changedpassword", type="string", nullable=false)
     */
    protected $changedpassword = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="lastupdatedby", type="string", length=20, nullable=true)
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
     * @ORM\Column(name="securityquestion", type="string", length=255, nullable=true)
     */
    protected $securityquestion;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="string", length=255, nullable=true)
     */
    protected $answer;

    /**
     * @var string
     *
     * @ORM\Column(name="disabledaily", type="string", nullable=false)
     */
    protected $disabledaily = 'N';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="passwordexpirydate", type="datetime", nullable=true)
     */
    protected $passwordexpirydate;

    /**
     * @var string
     *
     * @ORM\Column(name="canviewemployeeresources", type="string", nullable=true)
     */
    protected $canviewemployeeresources = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="canviewpublicholidays", type="string", nullable=true)
     */
    protected $canviewpublicholidays = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="canviewpayrolldates", type="string", nullable=true)
     */
    protected $canviewpayrolldates = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="canviewtravelpolicy", type="string", nullable=true)
     */
    protected $canviewtravelpolicy = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="canview401kinformation", type="string", nullable=true)
     */
    protected $canview401kinformation = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="canviewreferenceinformation", type="string", nullable=true)
     */
    protected $canviewreferenceinformation = 'N';

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_firstname", type="string", length=255, nullable=true)
     */
    protected $ec1Firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_lastname", type="string", length=255, nullable=true)
     */
    protected $ec1Lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_streetaddress", type="string", length=255, nullable=true)
     */
    protected $ec1Streetaddress;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_city", type="string", length=255, nullable=true)
     */
    protected $ec1City;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_state", type="string", length=255, nullable=true)
     */
    protected $ec1State;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_zipcode", type="string", length=255, nullable=true)
     */
    protected $ec1Zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_homephone", type="string", length=255, nullable=true)
     */
    protected $ec1Homephone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_workphone", type="string", length=255, nullable=true)
     */
    protected $ec1Workphone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_cellphone", type="string", length=255, nullable=true)
     */
    protected $ec1Cellphone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec1_relationship", type="string", length=255, nullable=true)
     */
    protected $ec1Relationship;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_firstname", type="string", length=255, nullable=true)
     */
    protected $ec2Firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_lastname", type="string", length=255, nullable=true)
     */
    protected $ec2Lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_streetaddress", type="string", length=255, nullable=true)
     */
    protected $ec2Streetaddress;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_city", type="string", length=255, nullable=true)
     */
    protected $ec2City;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_state", type="string", length=255, nullable=true)
     */
    protected $ec2State;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_zipcode", type="string", length=255, nullable=true)
     */
    protected $ec2Zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_homephone", type="string", length=255, nullable=true)
     */
    protected $ec2Homephone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_workphone", type="string", length=255, nullable=true)
     */
    protected $ec2Workphone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_cellphone", type="string", length=255, nullable=true)
     */
    protected $ec2Cellphone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec2_relationship", type="string", length=255, nullable=true)
     */
    protected $ec2Relationship;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_firstname", type="string", length=255, nullable=true)
     */
    protected $ec3Firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_lastname", type="string", length=255, nullable=true)
     */
    protected $ec3Lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_streetaddress", type="string", length=255, nullable=true)
     */
    protected $ec3Streetaddress;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_city", type="string", length=255, nullable=true)
     */
    protected $ec3City;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_state", type="string", length=255, nullable=true)
     */
    protected $ec3State;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_zipcode", type="string", length=255, nullable=true)
     */
    protected $ec3Zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_homephone", type="string", length=255, nullable=true)
     */
    protected $ec3Homephone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_workphone", type="string", length=255, nullable=true)
     */
    protected $ec3Workphone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_cellphone", type="string", length=255, nullable=true)
     */
    protected $ec3Cellphone;

    /**
     * @var string
     *
     * @ORM\Column(name="ec3_relationship", type="string", length=255, nullable=true)
     */
    protected $ec3Relationship;

    /**
     * @var integer
     *
     * @ORM\Column(name="permissiontemplate", type="bigint", nullable=false)
     */
    protected $permissiontemplate = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="companyid", type="integer", nullable=false)
     */
    protected $companyid = '';

    /**
     * @var string
     *
     * @ORM\Column(name="categoryuser", type="string", nullable=false)
     */
    protected $categoryuser = 'HRMS';

    /**
     * @var integer
     *
     * @ORM\Column(name="uniqueid", type="integer", nullable=true)
     */
    protected $uniqueid;

    /**
     * @var integer
     *
     * @ORM\Column(name="role", type="integer", nullable=false)
     */
    protected $role = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="week_start", type="string", length=20, nullable=false)
     */
    protected $weekStart = '';

    /**
     * @var string
     *
     * @ORM\Column(name="hour_wage", type="string", length=8, nullable=false)
     */
    protected $hourWage = '';

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=250, nullable=false)
     */
    protected $photo = 'None';

    public function toArray() {
        return ['id' => $this->id
        ];
    }

    public function toString() {
        return (string)$this->id;
    }


}
