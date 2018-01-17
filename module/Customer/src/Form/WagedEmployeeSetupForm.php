<?php

namespace Customer\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("WagedEmployeeSetupForm")
 */

class WagedEmployeeSetupForm {
    
    public $employeeId;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Employee Code"})
     * @Annotation\Attributes({ "id":"form-employeeCode", "class":"form-employeeCode form-control" })
     */
    
       public $employeeCode;
       
        /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"First Name"})
     * @Annotation\Attributes({ "id":"firstName", "class":"form-control" })
     */
    
      public  $firstName;
      
      /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Middle Name"})
     * @Annotation\Attributes({ "id":"middleName", "class":"form-control" })
     */
    
       public $middleName;
       
      /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Last Name"})
     * @Annotation\Attributes({ "id":"lastName", "class":"form-control" })
     */
    
      public  $lastName;
      
      /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Full Name"})
     * @Annotation\Attributes({ "id":"fullName", "class":"form-control" })
     */
    
      public  $fullName;
      
      
      /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(true)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Gender"})
     * @Annotation\Attributes({ "id":"genderId","class":"form-control"})
     */
    
       public $genderId;
       
       /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Blood Group"})
     * @Annotation\Attributes({ "id":"bloodGroupId","class":"full-width select2-offscreen","data-init-plugin":"select2","tabindex":"-1"})
     */
    
       public $bloodGroupId;
       
       /**
     * @Annotation\Type("Application\Custom\FormElement\Telephone")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Telephone No"})
     * @Annotation\Attributes({ "id":"telephoneNo", "placeholder":"xxx-xxxxxxx", "pattern":"^\(?\d{2,3}\)?[- ]?\d{7}$", "class":"form-control","title"="Enter your mobile number(xx-xxxxxxx)"})
     */
    
       public $telephoneNo;
       
       /**
     * @Annotation\Type("Application\Custom\FormElement\Mobile")
     * @Annotation\Required(true)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Mobile No"})
     * @Annotation\Attributes({ "id":"MobileNo", "placeholder":"xxx-xxx-xxxx", "class":"form-control" , "pattern"="^\(?\d{3}\)?[- ]?\d{3}[- ]?\d{4}$", "title"="Enter your mobile number(xxx-xxx-xxxx)"})
     */
    
       public $mobileNo;
       
       /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Email"})
     * @Annotation\Attributes({ "id":"email", "class":"form-control" })
     */
    
       public $email;
       
       /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Attributes({ "id":"idCitizenshipNo", "class":"form-control" })
     */
    
       public $idCitizenshipNo;
       
       /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Date"})
     * @Annotation\Attributes({"class":"form-control","id":"idCitizenshipIssueDate" })
     */
    
      public  $idCitizenshipIssueDate;
      
      
      /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Place"})
     * @Annotation\Attributes({ "id":"idCitizenshipIssuePlace", "class":"form-control" })
     */
    
      public  $idCitizenshipIssuePlace;
      
       /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Per Zone"})
     * @Annotation\Attributes({ "id":"addrPermZoneId","class":"form-control"})
     */
    
      public  $addrPermZoneId;
      
      /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":" Per District"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"addrPermDistrictId","class":"form-control"})
     */
    
       public $addrPermDistrictId;
       
       /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Required(false)
     * @Annotation\Options({"disable_inarray_validator":"true","label":" Temp Zone"})
     * @Annotation\Attributes({ "id":"addrTempZoneId","class":"form-control"})
     */
    
      public  $addrTempZoneId;
      
      /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":" Temp District"})
     * @Annotation\Required(false)
     * @Annotation\Attributes({ "id":"addrTempDistrictId","class":"form-control"})
     */
    
       public $addrTempDistrictId;
       
       /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success"})
     */
    public $submit;
    
}
