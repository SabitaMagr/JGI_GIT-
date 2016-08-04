<?php
namespace Setup\Model;

use Zend\Form\Annotation;


/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Employee")
 */
class Employee implements  ModelInterface
{

    public $employeeCode;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"First Name:", "label_attributes":{"class":"sr-only"}})
     */
    public $employeeFirstName;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Middle Name:", "label_attributes":{"class":"sr-only"}})
     */
    public $employeeMiddleName;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"First Name:", "label_attributes":{"class":"sr-only"}})
     */
    public $employeeLastName;


    /*
     * */


    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Date of Birth:", "label_attributes":{"class":"sr-only"}})
     */
    public $dateOfBirth;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Gender:", "label_attributes":{"class":"sr-only"},"value_options":{"0":"Female","1":"Male","2":"Others"}})
     */
    public $gender;


    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"false"})
     * @Annotation\Filter({"name":"StripTags","name":"StringTrim"})
     * @Annotation\Options({"label":"Blood Group","value_options":{"A-":"A-","A+":"A+","B-":"B-","B+":"B+","AB-":"AB-","AB+":"AB+","O-":"O-","O+":"O+"}})
     */
    public $bloodGroup;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Nationality:", "label_attributes":{"class":"sr-only"}})
     */
    public $nationality;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Religion:", "label_attributes":{"class":"sr-only"}})
     */
    public $religion;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Phone No:", "label_attributes":{"class":"sr-only"}})
     */
    public $phoneNumber;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Mobile Number:", "label_attributes":{"class":"sr-only"}})
     */
    public $mobileNumber;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Personal Email:", "label_attributes":{"class":"sr-only"}})
     */
    public $personalEmail;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Language:", "label_attributes":{"class":"sr-only"}})
     */
    public $languages;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship No:", "label_attributes":{"class":"sr-only"}})
     */
    public $citizenshipNo;


    /*
     * */


    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Sign In"})
     */
    public $submit;

    public function exchangeArray(array $data)
    {
//        $this->employeeCode = !empty($data['employeeCode']) ? $data['employeeCode'] : null;
        $this->employeeFirstName = !empty($data['employeeFirstName']) ? $data['employeeFirstName'] : null;
        $this->employeeLastName = !empty($data['employeeLastName']) ? $data['employeeLastName'] : null;
        $this->employeeMiddleName = !empty($data['employeeMiddleName']) ? $data['employeeMiddleName'] : null;
        $this->dateOfBirth = !empty($data['dateOfBirth']) ? $data['dateOfBirth'] : null;
        $this->gender = !empty($data['gender']) ?(int) $data['gender'] : null;
        $this->bloodGroup = !empty($data['bloodGroup']) ? $data['bloodGroup'] : null;
        $this->nationality = !empty($data['nationality']) ? $data['nationality'] : null;
        $this->religion = !empty($data['religion']) ? $data['religion'] : null;
        $this->phoneNumber = !empty($data['phoneNumber']) ? $data['phoneNumber'] : null;
        $this->mobileNumber = !empty($data['mobileNumber']) ? $data['mobileNumber'] : null;
        $this->personalEmail = !empty($data['personalEmail']) ? $data['personalEmail'] : null;
        $this->languages = !empty($data['languages']) ? $data['languages'] : null;
        $this->citizenshipNo = !empty($data['citizenshipNo']) ? $data['citizenshipNo'] : null;


    }

    public function getArrayCopy()
    {
        return [
//            'employeeCode'     => $this->employeeCode,
            'employeeFirstName' => $this->employeeFirstName,
            'employeeMiddleName' => $this->employeeMiddleName,
            'employeeLastName' => $this->employeeLastName,
            'dateOfBirth' => $this->dateOfBirth,
            'gender' => $this->gender,
            'bloodGroup' => $this->bloodGroup,
            'nationality' => $this->nationality,
            'religion' => $this->religion,
            'phoneNumber' => $this->phoneNumber,
            'mobileNumber' => $this->mobileNumber,
            'personalEmail' => $this->personalEmail,
            'languages' => $this->languages,
            'citizenshipNo' => $this->citizenshipNo,
        ];
    }

}