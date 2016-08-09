<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrPositions
 *
 * @ORM\Table(name="hr_positions")
 * @ORM\Entity
 */
class HrPositions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="POSITION_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $positionId;

    /**
     * @var string
     *
     * @ORM\Column(name="POSITION_CODE", type="string", length=20, nullable=false)
     */
    private $positionCode;

    /**
     * @var string
     *
     * @ORM\Column(name="POSITION_NAME", type="string", length=50, nullable=false)
     */
    private $positionName;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, nullable=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=10, nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_DT", type="datetime", nullable=false)
     */
    private $createdDt = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MODIFIED_DT", type="date", nullable=true)
     */
    private $modifiedDt;


}

