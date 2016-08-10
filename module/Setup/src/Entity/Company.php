<?php
namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="company")
 * @ORM\Entity
 */
class Company
{
    /**
     * @var integer
     *
     * @ORM\Column(name="companyCode", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $companycode;

    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string", length=50, nullable=false)
     */
    private $companyname;

    /**
     * @var string
     *
     * @ORM\Column(name="inNepali", type="string", length=20, nullable=false)
     */
    private $innepali;

    /**
     * @var string
     *
     * @ORM\Column(name="addressFirst", type="string", length=20, nullable=false)
     */
    private $addressfirst;

    /**
     * @var string
     *
     * @ORM\Column(name="addressSecond", type="string", length=20, nullable=false)
     */
    private $addresssecond;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=20, nullable=false)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=20, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=20, nullable=false)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=20, nullable=false)
     */
    private $web;

    /**
     * @var string
     *
     * @ORM\Column(name="registrationNo", type="string", length=20, nullable=false)
     */
    private $registrationno;

    /**
     * @var string
     *
     * @ORM\Column(name="vatNo", type="string", length=20, nullable=false)
     */
    private $vatno;

    /**
     * @var string
     *
     * @ORM\Column(name="smtpHost", type="string", length=20, nullable=false)
     */
    private $smtphost;

    /**
     * @var string
     *
     * @ORM\Column(name="serverPath", type="string", length=20, nullable=false)
     */
    private $serverpath;

    /**
     * @var string
     *
     * @ORM\Column(name="fiscalStart", type="string", length=20, nullable=false)
     */
    private $fiscalstart;

    /**
     * @var string
     *
     * @ORM\Column(name="fiscalEnd", type="string", length=20, nullable=false)
     */
    private $fiscalend;

    /**
     * @var string
     *
     * @ORM\Column(name="startTime", type="string", length=20, nullable=false)
     */
    private $starttime;

    /**
     * @var string
     *
     * @ORM\Column(name="endTime", type="string", length=20, nullable=false)
     */
    private $endtime;

    /**
     * @var string
     *
     * @ORM\Column(name="graceStartTime", type="string", length=20, nullable=false)
     */
    private $gracestarttime;

    /**
     * @var string
     *
     * @ORM\Column(name="graceEndTime", type="string", length=20, nullable=false)
     */
    private $graceendtime;


}

