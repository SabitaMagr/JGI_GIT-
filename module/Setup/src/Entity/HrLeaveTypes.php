<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrLeaveTypes
 *
 * @ORM\Table(name="HR_LEAVE_TYPES")
 * @ORM\Entity
 */
class HrLeaveTypes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="LEAVE_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $leaveId;

    /**
     * @var string
     *
     * @ORM\Column(name="LEAVE_CODE", type="string", length=20, nullable=false)
     */
    private $leaveCode;

    /**
     * @var string
     *
     * @ORM\Column(name="LEAVE_NAME", type="string", length=50, nullable=false)
     */
    private $leaveName;

    /**
     * @var integer
     *
     * @ORM\Column(name="TOTAL_LEAVE", type="integer", nullable=false)
     */
    private $totalLeave;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, nullable=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=11, nullable=false)
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
    public function getLeaveId()
    {
        return $this->leaveId;
    }

    /**
     * @param int $leaveId
     */
    public function setLeaveId($leaveId)
    {
        $this->leaveId = $leaveId;
    }

    /**
     * @return string
     */
    public function getLeaveCode()
    {
        return $this->leaveCode;
    }

    /**
     * @param string $leaveCode
     */
    public function setLeaveCode($leaveCode)
    {
        $this->leaveCode = $leaveCode;
    }

    /**
     * @return string
     */
    public function getLeaveName()
    {
        return $this->leaveName;
    }

    /**
     * @param string $leaveName
     */
    public function setLeaveName($leaveName)
    {
        $this->leaveName = $leaveName;
    }

    /**
     * @return int
     */
    public function getTotalLeave()
    {
        return $this->totalLeave;
    }

    /**
     * @param int $totalLeave
     */
    public function setTotalLeave($totalLeave)
    {
        $this->totalLeave = $totalLeave;
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

    public function getArrayCopy(){
        return get_object_vars($this);
    }

}

