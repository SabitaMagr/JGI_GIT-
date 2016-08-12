<?php

namespace Setup\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrEmployees
 *
 * @ORM\Table(name="HR_EMPLOYEES", uniqueConstraints={@ORM\UniqueConstraint(name="EMAIL_OFFICIAL", columns={"EMAIL_OFFICIAL"}), @ORM\UniqueConstraint(name="EMAIL_PERSONAL", columns={"EMAIL_PERSONAL"})})
 * @ORM\Entity
 */
class HrEmployees
{
    /**
     * @var integer
     *
     * @ORM\Column(name="EMPLOYEE_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $employeeId;

    /**
     * @var string
     *
     * @ORM\Column(name="EMPLOYEE_CODE", type="string", length=20, nullable=false)
     */
    private $employeeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="FIRST_NAME", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="MIDDLE_NAME", type="string", length=255, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="LAST_NAME", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME_NEPALI", type="string", length=512, nullable=true)
     */
    private $nameNepali;

    /**
     * @var integer
     *
     * @ORM\Column(name="GENDER_ID", type="integer", nullable=false)
     */
    private $genderId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="BIRTH_DATE", type="date", nullable=false)
     */
    private $birthDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="BLOOD_GROUP_ID", type="integer", nullable=false)
     */
    private $bloodGroupId;

    /**
     * @var integer
     *
     * @ORM\Column(name="RELIGION_ID", type="integer", nullable=false)
     */
    private $religionId;

    /**
     * @var string
     *
     * @ORM\Column(name="SOCIAL_ACTIVITY", type="string", length=255, nullable=true)
     */
    private $socialActivity;

    /**
     * @var string
     *
     * @ORM\Column(name="TELEPHONE_NO", type="string", length=255, nullable=true)
     */
    private $telephoneNo;

    /**
     * @var string
     *
     * @ORM\Column(name="MOBILE_NO", type="string", length=255, nullable=false)
     */
    private $mobileNo;

    /**
     * @var string
     *
     * @ORM\Column(name="EXTENSION_NO", type="string", length=50, nullable=true)
     */
    private $extensionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL_OFFICIAL", type="string", length=255, nullable=true)
     */
    private $emailOfficial;

    /**
     * @var string
     *
     * @ORM\Column(name="EMAIL_PERSONAL", type="string", length=255, nullable=true)
     */
    private $emailPersonal;

    /**
     * @var string
     *
     * @ORM\Column(name="SOCIAL_NETWORK", type="string", length=255, nullable=true)
     */
    private $socialNetwork;

    /**
     * @var string
     *
     * @ORM\Column(name="EMERG_CONTACT_NAME", type="string", length=255, nullable=true)
     */
    private $emergContactName;

    /**
     * @var string
     *
     * @ORM\Column(name="EMERG_CONTACT_NO", type="string", length=255, nullable=true)
     */
    private $emergContactNo;

    /**
     * @var string
     *
     * @ORM\Column(name="EMERG_CONTACT_ADDRESS", type="string", length=255, nullable=true)
     */
    private $emergContactAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="EMERG_CONTACT_RELATIONSHIP", type="string", length=255, nullable=true)
     */
    private $emergContactRelationship;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDR_PERM_HOUSE_NO", type="string", length=10, nullable=true)
     */
    private $addrPermHouseNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="ADDR_PERM_WARD_NO", type="integer", nullable=true)
     */
    private $addrPermWardNo;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDR_PERM_STREET_ADDRESS", type="string", length=255, nullable=true)
     */
    private $addrPermStreetAddress;

    /**
     * @var integer
     *
     * @ORM\Column(name="ADDR_PERM_VDC_MUNICIPALITY_ID", type="integer", nullable=true)
     */
    private $addrPermVdcMunicipalityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ADDR_PERM_DISTRICT_ID", type="integer", nullable=true)
     */
    private $addrPermDistrictId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ADDR_PERM_ZONE_ID", type="integer", nullable=true)
     */
    private $addrPermZoneId;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDR_TEMP_HOUSE_NO", type="string", length=10, nullable=true)
     */
    private $addrTempHouseNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="ADDR_TEMP_WARD_NO", type="integer", nullable=true)
     */
    private $addrTempWardNo;

    /**
     * @var string
     *
     * @ORM\Column(name="ADDR_TEMP_STREET_ADDRESS", type="string", length=255, nullable=true)
     */
    private $addrTempStreetAddress;

    /**
     * @var integer
     *
     * @ORM\Column(name="ADDR_TEMP_VDC_MUNICIPALITY_ID", type="integer", nullable=true)
     */
    private $addrTempVdcMunicipalityId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ADDR_TEMP_DISTRICT_ID", type="integer", nullable=true)
     */
    private $addrTempDistrictId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ADDR_TEMP_ZONE_ID", type="integer", nullable=true)
     */
    private $addrTempZoneId;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_FATHER_NAME", type="string", length=255, nullable=true)
     */
    private $famFatherName;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_FATHER_OCCUPATION", type="string", length=255, nullable=true)
     */
    private $famFatherOccupation;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_MOTHER_NAME", type="string", length=255, nullable=true)
     */
    private $famMotherName;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_MOTHER_OCCUPATION", type="string", length=255, nullable=true)
     */
    private $famMotherOccupation;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_GRAND_FATHER_NAME", type="string", length=255, nullable=true)
     */
    private $famGrandFatherName;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_GRAND_MOTHER_NAME", type="string", length=255, nullable=true)
     */
    private $famGrandMotherName;

    /**
     * @var string
     *
     * @ORM\Column(name="MARITUAL_STATUS", type="string", length=1, nullable=false)
     */
    private $maritualStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_SPOUSE_NAME", type="string", length=255, nullable=true)
     */
    private $famSpouseName;

    /**
     * @var string
     *
     * @ORM\Column(name="FAM_SPOUSE_OCCUPATION", type="string", length=255, nullable=true)
     */
    private $famSpouseOccupation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FAM_SPOUSE_BIRTH_DATE", type="date", nullable=true)
     */
    private $famSpouseBirthDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FAM_SPOUSE_WEDDING_ANNIVERSARY", type="date", nullable=true)
     */
    private $famSpouseWeddingAnniversary;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_CARD_NO", type="string", length=15, nullable=true)
     */
    private $idCardNo;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_LBRF", type="string", length=255, nullable=true)
     */
    private $idLbrf;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_BAR_CODE", type="string", length=50, nullable=true)
     */
    private $idBarCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_PROVIDENT_FUND_NO", type="string", length=15, nullable=true)
     */
    private $idProvidentFundNo;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_DRIVING_LICENSE_NO", type="string", length=50, nullable=true)
     */
    private $idDrivingLicenseNo;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_DRIVING_LICENSE_TYPE", type="string", length=6, nullable=true)
     */
    private $idDrivingLicenseType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ID_DRIVING_LICENSE_EXPIRY", type="date", nullable=true)
     */
    private $idDrivingLicenseExpiry;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_THUMB_ID", type="string", length=50, nullable=true)
     */
    private $idThumbId;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_PAN_NO", type="string", length=50, nullable=true)
     */
    private $idPanNo;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_ACCOUNT_ID", type="string", length=50, nullable=true)
     */
    private $idAccountId;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_RETIREMENT_NO", type="string", length=15, nullable=true)
     */
    private $idRetirementNo;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_CITIZENSHIP_NO", type="string", length=50, nullable=true)
     */
    private $idCitizenshipNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ID_CITIZENSHIP_ISSUE_DATE", type="date", nullable=true)
     */
    private $idCitizenshipIssueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_CITIZENSHIP_ISSUE_PLACE", type="string", length=255, nullable=true)
     */
    private $idCitizenshipIssuePlace;

    /**
     * @var string
     *
     * @ORM\Column(name="ID_PASSPORT_NO", type="string", length=15, nullable=true)
     */
    private $idPassportNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ID_PASSPORT_EXPIRY", type="date", nullable=true)
     */
    private $idPassportExpiry;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="JOIN_DATE", type="date", nullable=true)
     */
    private $joinDate;

    /**
     * @var float
     *
     * @ORM\Column(name="SALARY", type="float", precision=10, scale=0, nullable=true)
     */
    private $salary;

    /**
     * @var float
     *
     * @ORM\Column(name="SALARY_PF", type="float", precision=10, scale=0, nullable=true)
     */
    private $salaryPf;

    /**
     * @return int
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param int $employeeId
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return string
     */
    public function getEmployeeCode()
    {
        return $this->employeeCode;
    }

    /**
     * @param string $employeeCode
     */
    public function setEmployeeCode($employeeCode)
    {
        $this->employeeCode = $employeeCode;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getNameNepali()
    {
        return $this->nameNepali;
    }

    /**
     * @param string $nameNepali
     */
    public function setNameNepali($nameNepali)
    {
        $this->nameNepali = $nameNepali;
    }

    /**
     * @return int
     */
    public function getGenderId()
    {
        return $this->genderId;
    }

    /**
     * @param int $genderId
     */
    public function setGenderId($genderId)
    {
        $this->genderId = $genderId;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return int
     */
    public function getBloodGroupId()
    {
        return $this->bloodGroupId;
    }

    /**
     * @param int $bloodGroupId
     */
    public function setBloodGroupId($bloodGroupId)
    {
        $this->bloodGroupId = $bloodGroupId;
    }

    /**
     * @return int
     */
    public function getReligionId()
    {
        return $this->religionId;
    }

    /**
     * @param int $religionId
     */
    public function setReligionId($religionId)
    {
        $this->religionId = $religionId;
    }

    /**
     * @return string
     */
    public function getSocialActivity()
    {
        return $this->socialActivity;
    }

    /**
     * @param string $socialActivity
     */
    public function setSocialActivity($socialActivity)
    {
        $this->socialActivity = $socialActivity;
    }

    /**
     * @return string
     */
    public function getTelephoneNo()
    {
        return $this->telephoneNo;
    }

    /**
     * @param string $telephoneNo
     */
    public function setTelephoneNo($telephoneNo)
    {
        $this->telephoneNo = $telephoneNo;
    }

    /**
     * @return string
     */
    public function getMobileNo()
    {
        return $this->mobileNo;
    }

    /**
     * @param string $mobileNo
     */
    public function setMobileNo($mobileNo)
    {
        $this->mobileNo = $mobileNo;
    }

    /**
     * @return string
     */
    public function getExtensionNo()
    {
        return $this->extensionNo;
    }

    /**
     * @param string $extensionNo
     */
    public function setExtensionNo($extensionNo)
    {
        $this->extensionNo = $extensionNo;
    }

    /**
     * @return string
     */
    public function getEmailOfficial()
    {
        return $this->emailOfficial;
    }

    /**
     * @param string $emailOfficial
     */
    public function setEmailOfficial($emailOfficial)
    {
        $this->emailOfficial = $emailOfficial;
    }

    /**
     * @return string
     */
    public function getEmailPersonal()
    {
        return $this->emailPersonal;
    }

    /**
     * @param string $emailPersonal
     */
    public function setEmailPersonal($emailPersonal)
    {
        $this->emailPersonal = $emailPersonal;
    }

    /**
     * @return string
     */
    public function getSocialNetwork()
    {
        return $this->socialNetwork;
    }

    /**
     * @param string $socialNetwork
     */
    public function setSocialNetwork($socialNetwork)
    {
        $this->socialNetwork = $socialNetwork;
    }

    /**
     * @return string
     */
    public function getEmergContactName()
    {
        return $this->emergContactName;
    }

    /**
     * @param string $emergContactName
     */
    public function setEmergContactName($emergContactName)
    {
        $this->emergContactName = $emergContactName;
    }

    /**
     * @return string
     */
    public function getEmergContactNo()
    {
        return $this->emergContactNo;
    }

    /**
     * @param string $emergContactNo
     */
    public function setEmergContactNo($emergContactNo)
    {
        $this->emergContactNo = $emergContactNo;
    }

    /**
     * @return string
     */
    public function getEmergContactAddress()
    {
        return $this->emergContactAddress;
    }

    /**
     * @param string $emergContactAddress
     */
    public function setEmergContactAddress($emergContactAddress)
    {
        $this->emergContactAddress = $emergContactAddress;
    }

    /**
     * @return string
     */
    public function getEmergContactRelationship()
    {
        return $this->emergContactRelationship;
    }

    /**
     * @param string $emergContactRelationship
     */
    public function setEmergContactRelationship($emergContactRelationship)
    {
        $this->emergContactRelationship = $emergContactRelationship;
    }

    /**
     * @return string
     */
    public function getAddrPermHouseNo()
    {
        return $this->addrPermHouseNo;
    }

    /**
     * @param string $addrPermHouseNo
     */
    public function setAddrPermHouseNo($addrPermHouseNo)
    {
        $this->addrPermHouseNo = $addrPermHouseNo;
    }

    /**
     * @return int
     */
    public function getAddrPermWardNo()
    {
        return $this->addrPermWardNo;
    }

    /**
     * @param int $addrPermWardNo
     */
    public function setAddrPermWardNo($addrPermWardNo)
    {
        $this->addrPermWardNo = $addrPermWardNo;
    }

    /**
     * @return string
     */
    public function getAddrPermStreetAddress()
    {
        return $this->addrPermStreetAddress;
    }

    /**
     * @param string $addrPermStreetAddress
     */
    public function setAddrPermStreetAddress($addrPermStreetAddress)
    {
        $this->addrPermStreetAddress = $addrPermStreetAddress;
    }

    /**
     * @return int
     */
    public function getAddrPermVdcMunicipalityId()
    {
        return $this->addrPermVdcMunicipalityId;
    }

    /**
     * @param int $addrPermVdcMunicipalityId
     */
    public function setAddrPermVdcMunicipalityId($addrPermVdcMunicipalityId)
    {
        $this->addrPermVdcMunicipalityId = $addrPermVdcMunicipalityId;
    }

    /**
     * @return int
     */
    public function getAddrPermDistrictId()
    {
        return $this->addrPermDistrictId;
    }

    /**
     * @param int $addrPermDistrictId
     */
    public function setAddrPermDistrictId($addrPermDistrictId)
    {
        $this->addrPermDistrictId = $addrPermDistrictId;
    }

    /**
     * @return int
     */
    public function getAddrPermZoneId()
    {
        return $this->addrPermZoneId;
    }

    /**
     * @param int $addrPermZoneId
     */
    public function setAddrPermZoneId($addrPermZoneId)
    {
        $this->addrPermZoneId = $addrPermZoneId;
    }

    /**
     * @return string
     */
    public function getAddrTempHouseNo()
    {
        return $this->addrTempHouseNo;
    }

    /**
     * @param string $addrTempHouseNo
     */
    public function setAddrTempHouseNo($addrTempHouseNo)
    {
        $this->addrTempHouseNo = $addrTempHouseNo;
    }

    /**
     * @return int
     */
    public function getAddrTempWardNo()
    {
        return $this->addrTempWardNo;
    }

    /**
     * @param int $addrTempWardNo
     */
    public function setAddrTempWardNo($addrTempWardNo)
    {
        $this->addrTempWardNo = $addrTempWardNo;
    }

    /**
     * @return string
     */
    public function getAddrTempStreetAddress()
    {
        return $this->addrTempStreetAddress;
    }

    /**
     * @param string $addrTempStreetAddress
     */
    public function setAddrTempStreetAddress($addrTempStreetAddress)
    {
        $this->addrTempStreetAddress = $addrTempStreetAddress;
    }

    /**
     * @return int
     */
    public function getAddrTempVdcMunicipalityId()
    {
        return $this->addrTempVdcMunicipalityId;
    }

    /**
     * @param int $addrTempVdcMunicipalityId
     */
    public function setAddrTempVdcMunicipalityId($addrTempVdcMunicipalityId)
    {
        $this->addrTempVdcMunicipalityId = $addrTempVdcMunicipalityId;
    }

    /**
     * @return int
     */
    public function getAddrTempDistrictId()
    {
        return $this->addrTempDistrictId;
    }

    /**
     * @param int $addrTempDistrictId
     */
    public function setAddrTempDistrictId($addrTempDistrictId)
    {
        $this->addrTempDistrictId = $addrTempDistrictId;
    }

    /**
     * @return int
     */
    public function getAddrTempZoneId()
    {
        return $this->addrTempZoneId;
    }

    /**
     * @param int $addrTempZoneId
     */
    public function setAddrTempZoneId($addrTempZoneId)
    {
        $this->addrTempZoneId = $addrTempZoneId;
    }

    /**
     * @return string
     */
    public function getFamFatherName()
    {
        return $this->famFatherName;
    }

    /**
     * @param string $famFatherName
     */
    public function setFamFatherName($famFatherName)
    {
        $this->famFatherName = $famFatherName;
    }

    /**
     * @return string
     */
    public function getFamFatherOccupation()
    {
        return $this->famFatherOccupation;
    }

    /**
     * @param string $famFatherOccupation
     */
    public function setFamFatherOccupation($famFatherOccupation)
    {
        $this->famFatherOccupation = $famFatherOccupation;
    }

    /**
     * @return string
     */
    public function getFamMotherName()
    {
        return $this->famMotherName;
    }

    /**
     * @param string $famMotherName
     */
    public function setFamMotherName($famMotherName)
    {
        $this->famMotherName = $famMotherName;
    }

    /**
     * @return string
     */
    public function getFamMotherOccupation()
    {
        return $this->famMotherOccupation;
    }

    /**
     * @param string $famMotherOccupation
     */
    public function setFamMotherOccupation($famMotherOccupation)
    {
        $this->famMotherOccupation = $famMotherOccupation;
    }

    /**
     * @return string
     */
    public function getFamGrandFatherName()
    {
        return $this->famGrandFatherName;
    }

    /**
     * @param string $famGrandFatherName
     */
    public function setFamGrandFatherName($famGrandFatherName)
    {
        $this->famGrandFatherName = $famGrandFatherName;
    }

    /**
     * @return string
     */
    public function getFamGrandMotherName()
    {
        return $this->famGrandMotherName;
    }

    /**
     * @param string $famGrandMotherName
     */
    public function setFamGrandMotherName($famGrandMotherName)
    {
        $this->famGrandMotherName = $famGrandMotherName;
    }

    /**
     * @return string
     */
    public function getMaritualStatus()
    {
        return $this->maritualStatus;
    }

    /**
     * @param string $maritualStatus
     */
    public function setMaritualStatus($maritualStatus)
    {
        $this->maritualStatus = $maritualStatus;
    }

    /**
     * @return string
     */
    public function getFamSpouseName()
    {
        return $this->famSpouseName;
    }

    /**
     * @param string $famSpouseName
     */
    public function setFamSpouseName($famSpouseName)
    {
        $this->famSpouseName = $famSpouseName;
    }

    /**
     * @return string
     */
    public function getFamSpouseOccupation()
    {
        return $this->famSpouseOccupation;
    }

    /**
     * @param string $famSpouseOccupation
     */
    public function setFamSpouseOccupation($famSpouseOccupation)
    {
        $this->famSpouseOccupation = $famSpouseOccupation;
    }

    /**
     * @return \DateTime
     */
    public function getFamSpouseBirthDate()
    {
        return $this->famSpouseBirthDate;
    }

    /**
     * @param \DateTime $famSpouseBirthDate
     */
    public function setFamSpouseBirthDate($famSpouseBirthDate)
    {
        $this->famSpouseBirthDate = $famSpouseBirthDate;
    }

    /**
     * @return \DateTime
     */
    public function getFamSpouseWeddingAnniversary()
    {
        return $this->famSpouseWeddingAnniversary;
    }

    /**
     * @param \DateTime $famSpouseWeddingAnniversary
     */
    public function setFamSpouseWeddingAnniversary($famSpouseWeddingAnniversary)
    {
        $this->famSpouseWeddingAnniversary = $famSpouseWeddingAnniversary;
    }

    /**
     * @return string
     */
    public function getIdCardNo()
    {
        return $this->idCardNo;
    }

    /**
     * @param string $idCardNo
     */
    public function setIdCardNo($idCardNo)
    {
        $this->idCardNo = $idCardNo;
    }

    /**
     * @return string
     */
    public function getIdLbrf()
    {
        return $this->idLbrf;
    }

    /**
     * @param string $idLbrf
     */
    public function setIdLbrf($idLbrf)
    {
        $this->idLbrf = $idLbrf;
    }

    /**
     * @return string
     */
    public function getIdBarCode()
    {
        return $this->idBarCode;
    }

    /**
     * @param string $idBarCode
     */
    public function setIdBarCode($idBarCode)
    {
        $this->idBarCode = $idBarCode;
    }

    /**
     * @return string
     */
    public function getIdProvidentFundNo()
    {
        return $this->idProvidentFundNo;
    }

    /**
     * @param string $idProvidentFundNo
     */
    public function setIdProvidentFundNo($idProvidentFundNo)
    {
        $this->idProvidentFundNo = $idProvidentFundNo;
    }

    /**
     * @return string
     */
    public function getIdDrivingLicenseNo()
    {
        return $this->idDrivingLicenseNo;
    }

    /**
     * @param string $idDrivingLicenseNo
     */
    public function setIdDrivingLicenseNo($idDrivingLicenseNo)
    {
        $this->idDrivingLicenseNo = $idDrivingLicenseNo;
    }

    /**
     * @return string
     */
    public function getIdDrivingLicenseType()
    {
        return $this->idDrivingLicenseType;
    }

    /**
     * @param string $idDrivingLicenseType
     */
    public function setIdDrivingLicenseType($idDrivingLicenseType)
    {
        $this->idDrivingLicenseType = $idDrivingLicenseType;
    }

    /**
     * @return \DateTime
     */
    public function getIdDrivingLicenseExpiry()
    {
        return $this->idDrivingLicenseExpiry;
    }

    /**
     * @param \DateTime $idDrivingLicenseExpiry
     */
    public function setIdDrivingLicenseExpiry($idDrivingLicenseExpiry)
    {
        $this->idDrivingLicenseExpiry = $idDrivingLicenseExpiry;
    }

    /**
     * @return string
     */
    public function getIdThumbId()
    {
        return $this->idThumbId;
    }

    /**
     * @param string $idThumbId
     */
    public function setIdThumbId($idThumbId)
    {
        $this->idThumbId = $idThumbId;
    }

    /**
     * @return string
     */
    public function getIdPanNo()
    {
        return $this->idPanNo;
    }

    /**
     * @param string $idPanNo
     */
    public function setIdPanNo($idPanNo)
    {
        $this->idPanNo = $idPanNo;
    }

    /**
     * @return string
     */
    public function getIdAccountId()
    {
        return $this->idAccountId;
    }

    /**
     * @param string $idAccountId
     */
    public function setIdAccountId($idAccountId)
    {
        $this->idAccountId = $idAccountId;
    }

    /**
     * @return string
     */
    public function getIdRetirementNo()
    {
        return $this->idRetirementNo;
    }

    /**
     * @param string $idRetirementNo
     */
    public function setIdRetirementNo($idRetirementNo)
    {
        $this->idRetirementNo = $idRetirementNo;
    }

    /**
     * @return string
     */
    public function getIdCitizenshipNo()
    {
        return $this->idCitizenshipNo;
    }

    /**
     * @param string $idCitizenshipNo
     */
    public function setIdCitizenshipNo($idCitizenshipNo)
    {
        $this->idCitizenshipNo = $idCitizenshipNo;
    }

    /**
     * @return \DateTime
     */
    public function getIdCitizenshipIssueDate()
    {
        return $this->idCitizenshipIssueDate;
    }

    /**
     * @param \DateTime $idCitizenshipIssueDate
     */
    public function setIdCitizenshipIssueDate($idCitizenshipIssueDate)
    {
        $this->idCitizenshipIssueDate = $idCitizenshipIssueDate;
    }

    /**
     * @return string
     */
    public function getIdCitizenshipIssuePlace()
    {
        return $this->idCitizenshipIssuePlace;
    }

    /**
     * @param string $idCitizenshipIssuePlace
     */
    public function setIdCitizenshipIssuePlace($idCitizenshipIssuePlace)
    {
        $this->idCitizenshipIssuePlace = $idCitizenshipIssuePlace;
    }

    /**
     * @return string
     */
    public function getIdPassportNo()
    {
        return $this->idPassportNo;
    }

    /**
     * @param string $idPassportNo
     */
    public function setIdPassportNo($idPassportNo)
    {
        $this->idPassportNo = $idPassportNo;
    }

    /**
     * @return \DateTime
     */
    public function getIdPassportExpiry()
    {
        return $this->idPassportExpiry;
    }

    /**
     * @param \DateTime $idPassportExpiry
     */
    public function setIdPassportExpiry($idPassportExpiry)
    {
        $this->idPassportExpiry = $idPassportExpiry;
    }

    /**
     * @return \DateTime
     */
    public function getJoinDate()
    {
        return $this->joinDate;
    }

    /**
     * @param \DateTime $joinDate
     */
    public function setJoinDate($joinDate)
    {
        $this->joinDate = $joinDate;
    }

    /**
     * @return float
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @param float $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
    }

    /**
     * @return float
     */
    public function getSalaryPf()
    {
        return $this->salaryPf;
    }

    /**
     * @param float $salaryPf
     */
    public function setSalaryPf($salaryPf)
    {
        $this->salaryPf = $salaryPf;
    }



    public function getArrayCopy(){
        return get_object_vars($this);

    }


}

