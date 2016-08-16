<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrDesignations
 *
 * @ORM\Table(name="HR_DESIGNATIONS")
 * @ORM\Entity
 */
class HrDesignations
{
    /**
     * @var integer
     *
     * @ORM\Column(name="DESIGNATION_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $designationId;

    /**
     * @var string
     *
     * @ORM\Column(name="DESIGNATION_CODE", type="string", length=20, nullable=false)
     */
    private $designationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="DESIGNATION_TITLE", type="string", length=50, nullable=false)
     */
    private $designationTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="BASIC_SALARY", type="string", length=20, nullable=false)
     */
    private $basicSalary;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=20, nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_DT", type="datetime", nullable=false)
     */
    private $createdDt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MODIFIED_DT", type="date", nullable=false)
     */
    private $modifiedDt;

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
     * @return string
     */
    public function getDesignationCode()
    {
        return $this->designationCode;
    }

    /**
     * @param string $designationCode
     */
    public function setDesignationCode($designationCode)
    {
        $this->designationCode = $designationCode;
    }

    /**
     * @return string
     */
    public function getDesignationTitle()
    {
        return $this->designationTitle;
    }

    /**
     * @param string $designationTitle
     */
    public function setDesignationTitle($designationTitle)
    {
        $this->designationTitle = $designationTitle;
    }

    /**
     * @return string
     */
    public function getBasicSalary()
    {
        return $this->basicSalary;
    }

    /**
     * @param string $basicSalary
     */
    public function setBasicSalary($basicSalary)
    {
        $this->basicSalary = $basicSalary;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
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

    /**
     * @return \DateTime
     */
    public function getCreatedDt()
    {
        return $this->createdDt;
    }

    /**
     * @param \DateTime $createdDt
     */
    public function setCreatedDt($createdDt)
    {
        $this->createdDt = $createdDt;
    }
}

