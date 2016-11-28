<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrDistricts
 *
 * @ORM\Table(name="HR_DISTRICTS")
 * @ORM\Entity
 */
class HrDistricts
{
    /**
     * @var integer
     *
     * @ORM\Column(name="DISTRICT_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $districtId;

    /**
     * @var string
     *
     * @ORM\Column(name="DISTRICT_NAME", type="string", length=20, nullable=false)
     */
    private $districtName;

    /**
     * @return int
     */
    public function getDistrictId()
    {
        return $this->districtId;
    }

    /**
     * @param int $districtId
     */
    public function setDistrictId($districtId)
    {
        $this->districtId = $districtId;
    }

    /**
     * @return string
     */
    public function getDistrictName()
    {
        return $this->districtName;
    }

    /**
     * @param string $districtName
     */
    public function setDistrictName($districtName)
    {
        $this->districtName = $districtName;
    }



}

