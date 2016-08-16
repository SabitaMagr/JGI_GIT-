<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrDepartments
 *
 * @ORM\Table(name="HR_DEPARTMENTS")
 * @ORM\Entity
 */
class HrDepartments
{
    /**
     * @var integer
     *
     * @ORM\Column(name="DEPARTMENT_ID", type="integer", nullable=true)
     * @ORM\Id
     */
    protected $departmentId;

    /**
     * @var string
     *
     * @ORM\Column(name="DEPARTMENT_CODE", type="string", length=20, nullable=false)
     */
    protected $departmentCode;

    /**
     * @var string
     *
     * @ORM\Column(name="DEPARTMENT_NAME", type="string", length=50, nullable=false)
     */
    protected $departmentName;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="string", length=30, nullable=false)
     */
    protected $remarks;

    /**
     * @var integer
     *
     * @ORM\Column(name="PARENT_DEPARTMENT", type="integer", length=11, nullable=false)
     */
    protected $parentDepartment;

    /**
     * @var integer
     *
     * @ORM\Column(name="STATUS", type="string", nullable=false)
     */
    protected $status;

  
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MODIFIED_DT", type="date", nullable=true)
     */
    protected $modifiedDt;

    /**
     * @return int
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * @param int $departmentId
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     * @return string
     */
    public function getDepartmentCode()
    {
        return $this->departmentCode;
    }

    /**
     * @param string $departmentCode
     */
    public function setDepartmentCode($departmentCode)
    {
        $this->departmentCode = $departmentCode;
    }

    /**
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * @param string $departmentName
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     * @return string
     */
    public function getParentDepartment()
    {
        return $this->parentDepartment;
    }

    /**
     * @param string $parentDepartment
     */
    public function setParentDepartment($parentDepartment)
    {
        $this->parentDepartment = $parentDepartment;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return \DateTime
     */
    public function getModifiedDt()
    {
        return $this->modifiedDt;
    }

    /**
     * @param \DateTime $modifiedDt
     */
    public function setModifiedDt($modifiedDt)
    {
        $this->modifiedDt = $modifiedDt;
    }
}

