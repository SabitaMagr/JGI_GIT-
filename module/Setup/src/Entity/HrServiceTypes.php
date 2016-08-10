<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrServiceTypes
 *
 * @ORM\Table(name="HR_SERVICE_TYPES")
 * @ORM\Entity
 */
class HrServiceTypes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="SERVICE_TYPE_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $serviceTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="SERVICE_TYPE_CODE", type="string", length=50, nullable=false)
     */
    private $serviceTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="SERVICE_TYPE_NAME", type="string", length=50, nullable=false)
     */
    private $serviceTypeName;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, nullable=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=50, nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_DT", type="datetime", nullable=false)
     */
    private $createdDt = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MODIFIED_DT", type="date", nullable=true)
     */
    private $modifiedDt;

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
     * @return string
     */
    public function getServiceTypeCode()
    {
        return $this->serviceTypeCode;
    }

    /**
     * @param string $serviceTypeCode
     */
    public function setServiceTypeCode($serviceTypeCode)
    {
        $this->serviceTypeCode = $serviceTypeCode;
    }

    /**
     * @return string
     */
    public function getServiceTypeName()
    {
        return $this->serviceTypeName;
    }

    /**
     * @param string $serviceTypeName
     */
    public function setServiceTypeName($serviceTypeName)
    {
        $this->serviceTypeName = $serviceTypeName;
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

