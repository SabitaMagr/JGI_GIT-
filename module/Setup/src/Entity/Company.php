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

    /**
     * @return int
     */
    public function getCompanycode()
    {
        return $this->companycode;
    }

    /**
     * @param int $companycode
     */
    public function setCompanycode($companycode)
    {
        $this->companycode = $companycode;
    }

    /**
     * @return string
     */
    public function getCompanyname()
    {
        return $this->companyname;
    }

    /**
     * @param string $companyname
     */
    public function setCompanyname($companyname)
    {
        $this->companyname = $companyname;
    }

    /**
     * @return string
     */
    public function getInnepali()
    {
        return $this->innepali;
    }

    /**
     * @param string $innepali
     */
    public function setInnepali($innepali)
    {
        $this->innepali = $innepali;
    }

    /**
     * @return string
     */
    public function getAddressfirst()
    {
        return $this->addressfirst;
    }

    /**
     * @param string $addressfirst
     */
    public function setAddressfirst($addressfirst)
    {
        $this->addressfirst = $addressfirst;
    }

    /**
     * @return string
     */
    public function getAddresssecond()
    {
        return $this->addresssecond;
    }

    /**
     * @param string $addresssecond
     */
    public function setAddresssecond($addresssecond)
    {
        $this->addresssecond = $addresssecond;
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param string $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @return string
     */
    public function getRegistrationno()
    {
        return $this->registrationno;
    }

    /**
     * @param string $registrationno
     */
    public function setRegistrationno($registrationno)
    {
        $this->registrationno = $registrationno;
    }

    /**
     * @return string
     */
    public function getVatno()
    {
        return $this->vatno;
    }

    /**
     * @param string $vatno
     */
    public function setVatno($vatno)
    {
        $this->vatno = $vatno;
    }

    /**
     * @return string
     */
    public function getSmtphost()
    {
        return $this->smtphost;
    }

    /**
     * @param string $smtphost
     */
    public function setSmtphost($smtphost)
    {
        $this->smtphost = $smtphost;
    }

    /**
     * @return string
     */
    public function getServerpath()
    {
        return $this->serverpath;
    }

    /**
     * @param string $serverpath
     */
    public function setServerpath($serverpath)
    {
        $this->serverpath = $serverpath;
    }

    /**
     * @return string
     */
    public function getFiscalstart()
    {
        return $this->fiscalstart;
    }

    /**
     * @param string $fiscalstart
     */
    public function setFiscalstart($fiscalstart)
    {
        $this->fiscalstart = $fiscalstart;
    }

    /**
     * @return string
     */
    public function getFiscalend()
    {
        return $this->fiscalend;
    }

    /**
     * @param string $fiscalend
     */
    public function setFiscalend($fiscalend)
    {
        $this->fiscalend = $fiscalend;
    }

    /**
     * @return string
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * @param string $starttime
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * @return string
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * @param string $endtime
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
    }

    /**
     * @return string
     */
    public function getGracestarttime()
    {
        return $this->gracestarttime;
    }

    /**
     * @param string $gracestarttime
     */
    public function setGracestarttime($gracestarttime)
    {
        $this->gracestarttime = $gracestarttime;
    }

    /**
     * @return string
     */
    public function getGraceendtime()
    {
        return $this->graceendtime;
    }

    /**
     * @param string $graceendtime
     */
    public function setGraceendtime($graceendtime)
    {
        $this->graceendtime = $graceendtime;
    }


}

