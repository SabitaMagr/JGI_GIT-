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
     * @ORM\Column(name="companyCode", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $companycode;

    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    private $companyname;

    /**
     * @var string
     *
     * @ORM\Column(name="inNepali", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $innepali;

    /**
     * @var string
     *
     * @ORM\Column(name="addressFirst", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $addressfirst;

    /**
     * @var string
     *
     * @ORM\Column(name="addressSecond", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $addresssecond;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $web;

    /**
     * @var string
     *
     * @ORM\Column(name="registrationNo", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $registrationno;

    /**
     * @var string
     *
     * @ORM\Column(name="vatNo", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $vatno;

    /**
     * @var string
     *
     * @ORM\Column(name="smtpHost", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $smtphost;

    /**
     * @var string
     *
     * @ORM\Column(name="serverPath", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $serverpath;

    /**
     * @var string
     *
     * @ORM\Column(name="fiscalStart", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $fiscalstart;

    /**
     * @var string
     *
     * @ORM\Column(name="fiscalEnd", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $fiscalend;

    /**
     * @var string
     *
     * @ORM\Column(name="startTime", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $starttime;

    /**
     * @var string
     *
     * @ORM\Column(name="endTime", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $endtime;

    /**
     * @var string
     *
     * @ORM\Column(name="graceStartTime", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $gracestarttime;

    /**
     * @var string
     *
     * @ORM\Column(name="graceEndTime", type="string", length=20, precision=0, scale=0, nullable=false, unique=false)
     */
    private $graceendtime;


    /**
     * Get companycode
     *
     * @return integer
     */
    public function getCompanycode()
    {
        return $this->companycode;
    }

    /**
     * Set companyname
     *
     * @param string $companyname
     *
     * @return Company
     */
    public function setCompanyname($companyname)
    {
        $this->companyname = $companyname;

        return $this;
    }

    /**
     * Get companyname
     *
     * @return string
     */
    public function getCompanyname()
    {
        return $this->companyname;
    }

    /**
     * Set innepali
     *
     * @param string $innepali
     *
     * @return Company
     */
    public function setInnepali($innepali)
    {
        $this->innepali = $innepali;

        return $this;
    }

    /**
     * Get innepali
     *
     * @return string
     */
    public function getInnepali()
    {
        return $this->innepali;
    }

    /**
     * Set addressfirst
     *
     * @param string $addressfirst
     *
     * @return Company
     */
    public function setAddressfirst($addressfirst)
    {
        $this->addressfirst = $addressfirst;

        return $this;
    }

    /**
     * Get addressfirst
     *
     * @return string
     */
    public function getAddressfirst()
    {
        return $this->addressfirst;
    }

    /**
     * Set addresssecond
     *
     * @param string $addresssecond
     *
     * @return Company
     */
    public function setAddresssecond($addresssecond)
    {
        $this->addresssecond = $addresssecond;

        return $this;
    }

    /**
     * Get addresssecond
     *
     * @return string
     */
    public function getAddresssecond()
    {
        return $this->addresssecond;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Company
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Company
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Company
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set web
     *
     * @param string $web
     *
     * @return Company
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * Get web
     *
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Set registrationno
     *
     * @param string $registrationno
     *
     * @return Company
     */
    public function setRegistrationno($registrationno)
    {
        $this->registrationno = $registrationno;

        return $this;
    }

    /**
     * Get registrationno
     *
     * @return string
     */
    public function getRegistrationno()
    {
        return $this->registrationno;
    }

    /**
     * Set vatno
     *
     * @param string $vatno
     *
     * @return Company
     */
    public function setVatno($vatno)
    {
        $this->vatno = $vatno;

        return $this;
    }

    /**
     * Get vatno
     *
     * @return string
     */
    public function getVatno()
    {
        return $this->vatno;
    }

    /**
     * Set smtphost
     *
     * @param string $smtphost
     *
     * @return Company
     */
    public function setSmtphost($smtphost)
    {
        $this->smtphost = $smtphost;

        return $this;
    }

    /**
     * Get smtphost
     *
     * @return string
     */
    public function getSmtphost()
    {
        return $this->smtphost;
    }

    /**
     * Set serverpath
     *
     * @param string $serverpath
     *
     * @return Company
     */
    public function setServerpath($serverpath)
    {
        $this->serverpath = $serverpath;

        return $this;
    }

    /**
     * Get serverpath
     *
     * @return string
     */
    public function getServerpath()
    {
        return $this->serverpath;
    }

    /**
     * Set fiscalstart
     *
     * @param string $fiscalstart
     *
     * @return Company
     */
    public function setFiscalstart($fiscalstart)
    {
        $this->fiscalstart = $fiscalstart;

        return $this;
    }

    /**
     * Get fiscalstart
     *
     * @return string
     */
    public function getFiscalstart()
    {
        return $this->fiscalstart;
    }

    /**
     * Set fiscalend
     *
     * @param string $fiscalend
     *
     * @return Company
     */
    public function setFiscalend($fiscalend)
    {
        $this->fiscalend = $fiscalend;

        return $this;
    }

    /**
     * Get fiscalend
     *
     * @return string
     */
    public function getFiscalend()
    {
        return $this->fiscalend;
    }

    /**
     * Set starttime
     *
     * @param string $starttime
     *
     * @return Company
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;

        return $this;
    }

    /**
     * Get starttime
     *
     * @return string
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Set endtime
     *
     * @param string $endtime
     *
     * @return Company
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;

        return $this;
    }

    /**
     * Get endtime
     *
     * @return string
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Set gracestarttime
     *
     * @param string $gracestarttime
     *
     * @return Company
     */
    public function setGracestarttime($gracestarttime)
    {
        $this->gracestarttime = $gracestarttime;

        return $this;
    }

    /**
     * Get gracestarttime
     *
     * @return string
     */
    public function getGracestarttime()
    {
        return $this->gracestarttime;
    }

    /**
     * Set graceendtime
     *
     * @param string $graceendtime
     *
     * @return Company
     */
    public function setGraceendtime($graceendtime)
    {
        $this->graceendtime = $graceendtime;

        return $this;
    }

    /**
     * Get graceendtime
     *
     * @return string
     */
    public function getGraceendtime()
    {
        return $this->graceendtime;
    }
}

