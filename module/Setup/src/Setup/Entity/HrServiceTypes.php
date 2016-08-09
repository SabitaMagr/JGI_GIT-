<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrServiceTypes
 *
 * @ORM\Table(name="hr_service_types")
 * @ORM\Entity
 */
class HrServiceTypes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="SERVICE_TYPE_ID", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $serviceTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="SERVICE_TYPE_CODE", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $serviceTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="SERVICE_TYPE_NAME", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $serviceTypeName;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, precision=0, scale=0, nullable=false, unique=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_DT", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $createdDt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MODIFIED_DT", type="date", precision=0, scale=0, nullable=true, unique=false)
     */
    private $modifiedDt;


    /**
     * Get serviceTypeId
     *
     * @return integer
     */
    public function getServiceTypeId()
    {
        return $this->serviceTypeId;
    }

    /**
     * Set serviceTypeCode
     *
     * @param string $serviceTypeCode
     *
     * @return HrServiceTypes
     */
    public function setServiceTypeCode($serviceTypeCode)
    {
        $this->serviceTypeCode = $serviceTypeCode;

        return $this;
    }

    /**
     * Get serviceTypeCode
     *
     * @return string
     */
    public function getServiceTypeCode()
    {
        return $this->serviceTypeCode;
    }

    /**
     * Set serviceTypeName
     *
     * @param string $serviceTypeName
     *
     * @return HrServiceTypes
     */
    public function setServiceTypeName($serviceTypeName)
    {
        $this->serviceTypeName = $serviceTypeName;

        return $this;
    }

    /**
     * Get serviceTypeName
     *
     * @return string
     */
    public function getServiceTypeName()
    {
        return $this->serviceTypeName;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return HrServiceTypes
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return HrServiceTypes
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdDt
     *
     * @param \DateTime $createdDt
     *
     * @return HrServiceTypes
     */
    public function setCreatedDt($createdDt)
    {
        $this->createdDt = $createdDt;

        return $this;
    }

    /**
     * Get createdDt
     *
     * @return \DateTime
     */
    public function getCreatedDt()
    {
        return $this->createdDt;
    }

    /**
     * Set modifiedDt
     *
     * @param \DateTime $modifiedDt
     *
     * @return HrServiceTypes
     */
    public function setModifiedDt($modifiedDt)
    {
        $this->modifiedDt = $modifiedDt;

        return $this;
    }

    /**
     * Get modifiedDt
     *
     * @return \DateTime
     */
    public function getModifiedDt()
    {
        return $this->modifiedDt;
    }
}

