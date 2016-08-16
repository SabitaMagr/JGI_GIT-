<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BloodGroups
 *
 * @ORM\Table(name="BLOOD_GROUPS")
 * @ORM\Entity
 */
class BloodGroups
{
    /**
     * @var integer
     *
     * @ORM\Column(name="BLOOD_GROUP_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $bloodGroupId;

    /**
     * @var string
     *
     * @ORM\Column(name="BLOOD_GROUP_CODE", type="string", length=3, nullable=false)
     */
    private $bloodGroupCode;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=1, nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_DT", type="date", nullable=false)
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
    public function getBloodGroupId()
    {
        return $this->bloodGroupId;
    }

    /**
     * @param int $bloodGroupId
     */
    public function setBloodGroupId($bloodGroupId)
    {
        $this->bloodGroupId = $bloodGroupId;
    }

    /**
     * @return string
     */
    public function getBloodGroupCode()
    {
        return $this->bloodGroupCode;
    }

    /**
     * @param string $bloodGroupCode
     */
    public function setBloodGroupCode($bloodGroupCode)
    {
        $this->bloodGroupCode = $bloodGroupCode;
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



    public function getArrayCopy()
    {
       return get_object_vars($this);
    }

}

