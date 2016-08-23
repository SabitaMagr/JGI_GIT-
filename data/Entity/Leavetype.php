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
     * @ORM\Column(name="leaveCode", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $leavecode;

    /**
     * @var string
     *
     * @ORM\Column(name="leaveName", type="string", length=50, nullable=false)
     */
    private $leavename;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalLeave", type="integer", nullable=false)
     */
    private $totalleave;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=false)
     */
    private $remarks;


}

