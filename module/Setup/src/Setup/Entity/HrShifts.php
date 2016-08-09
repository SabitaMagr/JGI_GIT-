<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrShifts
 *
 * @ORM\Table(name="hr_shifts")
 * @ORM\Entity
 */
class HrShifts
{
    /**
     * @var integer
     *
     * @ORM\Column(name="SHIFT_ID", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $shiftId;

    /**
     * @var string
     *
     * @ORM\Column(name="SHIFT_CODE", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $shiftCode;

    /**
     * @var string
     *
     * @ORM\Column(name="SHIFT_NAME", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $shiftName;

    /**
     * @var string
     *
     * @ORM\Column(name="START_TIME", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $startTime;

    /**
     * @var string
     *
     * @ORM\Column(name="END_TIME", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, precision=0, scale=0, nullable=false, unique=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
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
     * Get shiftId
     *
     * @return integer
     */
    public function getShiftId()
    {
        return $this->shiftId;
    }

    /**
     * Set shiftCode
     *
     * @param string $shiftCode
     *
     * @return HrShifts
     */
    public function setShiftCode($shiftCode)
    {
        $this->shiftCode = $shiftCode;

        return $this;
    }

    /**
     * Get shiftCode
     *
     * @return string
     */
    public function getShiftCode()
    {
        return $this->shiftCode;
    }

    /**
     * Set shiftName
     *
     * @param string $shiftName
     *
     * @return HrShifts
     */
    public function setShiftName($shiftName)
    {
        $this->shiftName = $shiftName;

        return $this;
    }

    /**
     * Get shiftName
     *
     * @return string
     */
    public function getShiftName()
    {
        return $this->shiftName;
    }

    /**
     * Set startTime
     *
     * @param string $startTime
     *
     * @return HrShifts
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return string
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param string $endTime
     *
     * @return HrShifts
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return string
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return HrShifts
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
     * @return HrShifts
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
     * @return HrShifts
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
     * @return HrShifts
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

