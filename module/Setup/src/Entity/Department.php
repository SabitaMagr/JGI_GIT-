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
     * @ORM\Column(name="departmentCode", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $departmentcode;

    /**
     * @var string
     *
     * @ORM\Column(name="departmentName", type="string", length=50, nullable=false)
     */
    private $departmentname;

    /**
     * @var string
     *
     * @ORM\Column(name="hodCode", type="string", length=30, nullable=false)
     */
    private $hodcode;

    /**
     * @var string
     *
     * @ORM\Column(name="parentDepartment", type="string", length=20, nullable=false)
     */
    private $parentdepartment;


}

