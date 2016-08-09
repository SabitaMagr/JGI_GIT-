<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrShifts
 *
 * @ORM\Table(name="hr_shifts")
 * @ORM\Entity
 */
class HrShifts
{
    /**
     * @var integer
     *
     * @ORM\Column(name="SHIFT_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $shiftId;

    /**
     * @var string
     *
     * @ORM\Column(name="SHIFT_CODE", type="string", length=20, nullable=false)
     */
    private $shiftCode;

    /**
     * @var string
     *
     * @ORM\Column(name="SHIFT_NAME", type="string", length=50, nullable=false)
     */
    private $shiftName;

    /**
     * @var string
     *
     * @ORM\Column(name="START_TIME", type="string", length=50, nullable=false)
     */
    private $startTime;

    /**
     * @var string
     *
     * @ORM\Column(name="END_TIME", type="string", length=50, nullable=false)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, nullable=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=20, nullable=false)
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

