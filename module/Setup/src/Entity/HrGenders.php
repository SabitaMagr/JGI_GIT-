<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrGenders
 *
 * @ORM\Table(name="HR_GENDERS")
 * @ORM\Entity
 */
class HrGenders
{
    /**
     * @var integer
     *
     * @ORM\Column(name="GENDER_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $genderId;

    /**
     * @var string
     *
     * @ORM\Column(name="GENDER_CODE", type="string", length=1, nullable=false)
     */
    private $genderCode;

    /**
     * @var string
     *
     * @ORM\Column(name="GENDER_NAME", type="string", length=20, nullable=false)
     */
    private $genderName;

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
    public function getGenderId()
    {
        return $this->genderId;
    }

    /**
     * @param int $genderId
     */
    public function setGenderId($genderId)
    {
        $this->genderId = $genderId;
    }

    /**
     * @return string
     */
    public function getGenderCode()
    {
        return $this->genderCode;
    }

    /**
     * @param string $genderCode
     */
    public function setGenderCode($genderCode)
    {
        $this->genderCode = $genderCode;
    }

    /**
     * @return string
     */
    public function getGenderName()
    {
        return $this->genderName;
    }

    /**
     * @param string $genderName
     */
    public function setGenderName($genderName)
    {
        $this->genderName = $genderName;
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

