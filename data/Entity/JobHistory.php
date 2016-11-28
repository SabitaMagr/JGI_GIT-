<?php

namespace Setup\Entity;



use Doctrine\ORM\Mapping as ORM;

/**
 * JobHistory
 *
 * @ORM\Table(name="JOB_HISTORY")
 * @ORM\Entity
 */
class JobHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="EMPLOYEE_ID", type="integer", nullable=false)
     */
    private $employeeId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="START_DATE", type="date", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="END_DATE", type="date", nullable=false)
     */
    private $endDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="SERVICE_TYPE_ID", type="integer", nullable=false)
     */
    private $serviceTypeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="FROM_BRANCH_ID", type="integer", nullable=false)
     */
    private $fromBranchId;

    /**
     * @var integer
     *
     * @ORM\Column(name="TO_BRANCH_ID", type="integer", nullable=false)
     */
    private $toBranchId;

    /**
     * @var integer
     *
     * @ORM\Column(name="FROM_DEPARTMENT_ID", type="integer", nullable=false)
     */
    private $fromDepartmentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="TO_DEPARTMENT_ID", type="integer", nullable=false)
     */
    private $toDepartmentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="FROM_DESIGNATION_ID", type="integer", nullable=false)
     */
    private $fromDesignationId;

    /**
     * @var integer
     *
     * @ORM\Column(name="TO_DESIGNATION_ID", type="integer", nullable=false)
     */
    private $toDesignationId;

    /**
     * @var integer
     *
     * @ORM\Column(name="FROM_POSITION_ID", type="integer", nullable=false)
     */
    private $fromPositionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="TO_POSITION_ID", type="integer", nullable=false)
     */
    private $toPositionId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param int $employeeId
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }  

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }
    

    /**
     * @return int
     */
    public function getServiceTypeId()
    {
        return $this->serviceTypeId;
    }

    /**
     * @param int $serviceTypeId
     */
    public function setServiceTypeId($serviceTypeId)
    {
        $this->serviceTypeId = $serviceTypeId;
    }

    /**
     * @return int
     */
    public function getToBranchId()
    {
        return $this->toBranchId;
    }

    /**
     * @param int $toBranchId
     */
    public function setToBranchId($toBranchId)
    {
        $this->toBranchId = $toBranchId;
    }

    /**
     * @return int
     */
    public function getFromBranchId()
    {
        return $this->fromBranchId;
    }

    /**
     * @param int $fromBranchId
     */
    public function setFromBranchId($fromBranchId)
    {
        $this->fromBranchId = $fromBranchId;
    }

    /**
     * @return int
     */
    public function getToDepartmentId()
    {
        return $this->toDepartmentId;
    }

    /**
     * @param int $toDepartmentId
     */
    public function setToDepartmentId($toDepartmentId)
    {
        $this->toDepartmentId = $toDepartmentId;
    }

    /**
     * @return int
     */
    public function getFromDepartmentId()
    {
        return $this->fromDepartmentId;
    }

    /**
     * @param int $fromDepartmentId
     */
    public function setFromDepartmentId($fromDepartmentId)
    {
        $this->fromDepartmentId = $fromDepartmentId;
    }

    /**
     * @return int
     */
    public function getFromDesignationId()
    {
        return $this->fromDesignationId;
    }

    /**
     * @param int $fromDesignationId
     */
    public function setFromDesignationId($fromDesignationId)
    {
        $this->fromDesignationId = $fromDesignationId;
    }

    /**
     * @return int
     */
    public function getFromPositionId()
    {
        return $this->fromPositionId;
    }

    /**
     * @param int $fromPositionId
     */
    public function setFromPositionId($fromPositionId)
    {
        $this->fromPositionId = $fromPositionId;
    }

    /**
     * @return int
     */
    public function getToDesignationId()
    {
        return $this->toDesignationId;
    }

    /**
     * @param int $toDesignationId
     */
    public function setToDesignationId($toDesignationId)
    {
        $this->toDesignationId = $toDesignationId;
    }

    /**
     * @return int
     */
    public function getToPositionId()
    {
        return $this->toPositionId;
    }

    /**
     * @param int $toPositionId
     */
    public function setToPositionId($toPositionId)
    {
        $this->toPositionId = $toPositionId;
    }
}

