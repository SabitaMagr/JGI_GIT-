<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Department
 *
 * @ORM\Table(name="department")
 * @ORM\Entity
 */
class Department
{
    /**
     * @var integer
     *
     * @ORM\Column(name="departmentCode", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $departmentcode;

    /**
     * @var string
     *
     * @ORM\Column(name="departmentName", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $departmentname;

    /**
     * @var string
     *
     * @ORM\Column(name="hodCode", type="string", length=30, precision=0, scale=0, nullable=false, unique=false)
     */
    private $hodcode;

    /**
     * @var string
     *
     * @ORM\Column(name="parentDepartment", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $parentdepartment;


    /**
     * Get departmentcode
     *
     * @return integer
     */
    public function getDepartmentcode()
    {
        return $this->departmentcode;
    }

    /**
     * Set departmentname
     *
     * @param string $departmentname
     *
     * @return Department
     */
    public function setDepartmentname($departmentname)
    {
        $this->departmentname = $departmentname;

        return $this;
    }

    /**
     * Get departmentname
     *
     * @return string
     */
    public function getDepartmentname()
    {
        return $this->departmentname;
    }

    /**
     * Set hodcode
     *
     * @param string $hodcode
     *
     * @return Department
     */
    public function setHodcode($hodcode)
    {
        $this->hodcode = $hodcode;

        return $this;
    }

    /**
     * Get hodcode
     *
     * @return string
     */
    public function getHodcode()
    {
        return $this->hodcode;
    }

    /**
     * Set parentdepartment
     *
     * @param string $parentdepartment
     *
     * @return Department
     */
    public function setParentdepartment($parentdepartment)
    {
        $this->parentdepartment = $parentdepartment;

        return $this;
    }

    /**
     * Get parentdepartment
     *
     * @return string
     */
    public function getParentdepartment()
    {
        return $this->parentdepartment;
    }
}

