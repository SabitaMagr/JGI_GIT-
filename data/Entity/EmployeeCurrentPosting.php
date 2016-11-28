<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeCurrentPosting
 *
 * @ORM\Table(name="EMPLOYEE_CURRENT_POSTING")
 * @ORM\Entity
 */
class EmployeeCurrentPosting
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
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
     * @var integer
     *
     * @ORM\Column(name="SERVICE_TYPE_ID", type="integer", nullable=false)
     */
    private $serviceTypeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="BRANCH_ID", type="integer", nullable=false)
     */
    private $branchId;

    /**
     * @var integer
     *
     * @ORM\Column(name="DEPARTMENT_ID", type="integer", nullable=false)
     */
    private $departmentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="DESIGNATION_ID", type="integer", nullable=false)
     */
    private $designationId;

    /**
     * @var integer
     *
     * @ORM\Column(name="POSITION_ID", type="integer", nullable=false)
     */
    private $positionId;

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
    public function getBranchId()
    {
        return $this->branchId;
    }

    /**
     * @param int $branchId
     */
    public function setBranchId($branchId)
    {
        $this->branchId = $branchId;
    }

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
     * @return int
     */
    public function getDesignationId()
    {
        return $this->designationId;
    }

    /**
     * @param int $designationId
     */
    public function setDesignationId($designationId)
    {
        $this->designationId = $designationId;
    }

    /**
     * @return int
     */
    public function getPositionId()
    {
        return $this->positionId;
    }

    /**
     * @param int $positionId
     */
    public function setPositionId($positionId)
    {
        $this->positionId = $positionId;
    }
}

