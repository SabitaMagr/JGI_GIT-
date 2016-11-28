<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrVdcMunicipality
 *
 * @ORM\Table(name="HR_VDC_MUNICIPALITY")
 * @ORM\Entity
 */
class HrVdcMunicipality
{
    /**
     * @var integer
     *
     * @ORM\Column(name="VDC_MUNICIPALITY_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $vdcMunicipalityId;

    /**
     * @var string
     *
     * @ORM\Column(name="VDC_MUNICIPALITY_NAME", type="string", length=20, nullable=false)
     */
    private $vdcMunicipalityName;

    /**
     * @return int
     */
    public function getVdcMunicipalityId()
    {
        return $this->vdcMunicipalityId;
    }

    /**
     * @param int $vdcMunicipalityId
     */
    public function setVdcMunicipalityId($vdcMunicipalityId)
    {
        $this->vdcMunicipalityId = $vdcMunicipalityId;
    }

    /**
     * @return string
     */
    public function getVdcMunicipalityName()
    {
        return $this->vdcMunicipalityName;
    }

    /**
     * @param string $vdcMunicipalityName
     */
    public function setVdcMunicipalityName($vdcMunicipalityName)
    {
        $this->vdcMunicipalityName = $vdcMunicipalityName;
    }



}

