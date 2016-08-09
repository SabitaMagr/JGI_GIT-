<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrPositions
 *
 * @ORM\Table(name="hr_positions")
 * @ORM\Entity
 */
class HrPositions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="POSITION_ID", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $positionId;

    /**
     * @var string
     *
     * @ORM\Column(name="POSITION_CODE", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $positionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="POSITION_NAME", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $positionName;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, precision=0, scale=0, nullable=false, unique=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=10, precision=0, scale=0, nullable=false, unique=false)
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
     * Get positionId
     *
     * @return integer
     */
    public function getPositionId()
    {
        return $this->positionId;
    }

    /**
     * Set positionCode
     *
     * @param string $positionCode
     *
     * @return HrPositions
     */
    public function setPositionCode($positionCode)
    {
        $this->positionCode = $positionCode;

        return $this;
    }

    /**
     * Get positionCode
     *
     * @return string
     */
    public function getPositionCode()
    {
        return $this->positionCode;
    }

    /**
     * Set positionName
     *
     * @param string $positionName
     *
     * @return HrPositions
     */
    public function setPositionName($positionName)
    {
        $this->positionName = $positionName;

        return $this;
    }

    /**
     * Get positionName
     *
     * @return string
     */
    public function getPositionName()
    {
        return $this->positionName;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return HrPositions
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
     * @return HrPositions
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
     * @return HrPositions
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
     * @return HrPositions
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

