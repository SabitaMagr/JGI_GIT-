<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrPositions
 *
 * @ORM\Table(name="HR_POSITIONS")
 * @ORM\Entity
 */
class HrPositions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="POSITION_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $positionId;

    /**
     * @var string
     *
     * @ORM\Column(name="POSITION_CODE", type="string", length=20, nullable=false)
     */
    private $positionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="POSITION_NAME", type="string", length=50, nullable=false)
     */
    private $positionName;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, nullable=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=10, nullable=false)
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
     * @ORM\Column(name="MODIFIED_DT", type="date", nullable=true)
     */
    private $modifiedDt;

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

    /**
     * @return string
     */
    public function getPositionCode()
    {
        return $this->positionCode;
    }

    /**
     * @param string $positionCode
     */
    public function setPositionCode($positionCode)
    {
        $this->positionCode = $positionCode;
    }

    /**
     * @return string
     */
    public function getPositionName()
    {
        return $this->positionName;
    }

    /**
     * @param string $positionName
     */
    public function setPositionName($positionName)
    {
        $this->positionName = $positionName;
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

