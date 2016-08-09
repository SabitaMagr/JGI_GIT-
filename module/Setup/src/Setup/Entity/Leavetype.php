<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Leavetype
 *
 * @ORM\Table(name="leaveType")
 * @ORM\Entity
 */
class Leavetype
{
    /**
     * @var integer
     *
     * @ORM\Column(name="leaveCode", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $leavecode;

    /**
     * @var string
     *
     * @ORM\Column(name="leaveName", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $leavename;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalLeave", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $totalleave;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, precision=0, scale=0, nullable=false, unique=false)
     */
    private $remarks;


    /**
     * Get leavecode
     *
     * @return integer
     */
    public function getLeavecode()
    {
        return $this->leavecode;
    }

    /**
     * Set leavename
     *
     * @param string $leavename
     *
     * @return Leavetype
     */
    public function setLeavename($leavename)
    {
        $this->leavename = $leavename;

        return $this;
    }

    /**
     * Get leavename
     *
     * @return string
     */
    public function getLeavename()
    {
        return $this->leavename;
    }

    /**
     * Set totalleave
     *
     * @param integer $totalleave
     *
     * @return Leavetype
     */
    public function setTotalleave($totalleave)
    {
        $this->totalleave = $totalleave;

        return $this;
    }

    /**
     * Get totalleave
     *
     * @return integer
     */
    public function getTotalleave()
    {
        return $this->totalleave;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return Leavetype
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
}

