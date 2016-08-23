<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrZones
 *
 * @ORM\Table(name="HR_ZONES")
 * @ORM\Entity
 */
class HrZones
{
    /**
     * @var string
     *
     * @ORM\Column(name="ZONE_ID", type="string", nullable=false)
     * @ORM\Id
     */
    private $zoneId;

    /**
     * @var string
     *
     * @ORM\Column(name="ZONE_CODE", type="string", length=2, nullable=false)
     */
    private $zoneCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ZONE_NAME", type="string", length=150, nullable=false)
     */
    private $zoneName;

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
     * @return string
     */
    public function getZoneId()
    {
        return $this->zoneId;
    }

    /**
     * @param string $zoneId
     */
    public function setZoneId($zoneId)
    {
        $this->zoneId = $zoneId;
    }

    /**
     * @return string
     */
    public function getZoneCode()
    {
        return $this->zoneCode;
    }

    /**
     * @param string $zoneCode
     */
    public function setZoneCode($zoneCode)
    {
        $this->zoneCode = $zoneCode;
    }

    /**
     * @return string
     */
    public function getZoneName()
    {
        return $this->zoneName;
    }

    /**
     * @param string $zoneName
     */
    public function setZoneName($zoneName)
    {
        $this->zoneName = $zoneName;
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


}

