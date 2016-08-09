<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrServiceTypes
 *
 * @ORM\Table(name="hr_service_types")
 * @ORM\Entity
 */
class HrServiceTypes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="SERVICE_TYPE_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $serviceTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="SERVICE_TYPE_CODE", type="string", length=50, nullable=false)
     */
    private $serviceTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="SERVICE_TYPE_NAME", type="string", length=50, nullable=false)
     */
    private $serviceTypeName;

    /**
     * @var string
     *
     * @ORM\Column(name="REMARKS", type="text", length=65535, nullable=false)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="STATUS", type="string", length=50, nullable=false)
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

