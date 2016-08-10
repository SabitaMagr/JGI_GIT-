<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrShifts
 *
 * @ORM\Table(name="HR_SHIFTS")
 * @ORM\Entity
 */
class HrShifts
{
    /**
     * @var integer
     *
     * @ORM\Column(name="SHIFT_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $shiftId;

    /**
     * @var string
     *
     * @ORM\Column(name="SHIFT_CODE", type="string", length=20, nullable=false)
     */
    private $shiftCode;

    /**
     * @var string
     *
     * @ORM\Column(name="SHIFT_NAME", type="string", length=50, nullable=false)
     */
    private $shiftName;

    /**
     * @var string
     *
     * @ORM\Column(name="START_TIME", type="string", length=50, nullable=false)
     */
    private $startTime;

    /**
     * @var string
     *
     * @ORM\Column(name="END_TIME", type="string", length=50, nullable=false)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, nullable=false)
     */
    private $remarks;

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
     * @ORM\Column(name="MODIFIED_DT", type="date", nullable=true)
     */
    private $modifiedDt;

    /**
     * @return int
     */
    public function getShiftId()
    {
        return $this->shiftId;
    }

    /**
     * @param int $shiftId
     */
    public function setShiftId($shiftId)
    {
        $this->shiftId = $shiftId;
    }

    /**
     * @return string
     */
    public function getShiftCode()
    {
        return $this->shiftCode;
    }

    /**
     * @param string $shiftCode
     */
    public function setShiftCode($shiftCode)
    {
        $this->shiftCode = $shiftCode;
    }

    /**
     * @return string
     */
    public function getShiftName()
    {
        return $this->shiftName;
    }

    /**
     * @param string $shiftName
     */
    public function setShiftName($shiftName)
    {
        $this->shiftName = $shiftName;
    }

    /**
     * @return string
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param string $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
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
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param string $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
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

    public function getArrayCopy(){
        return get_object_vars($this);
    }


}

