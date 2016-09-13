<?php
/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 8/31/16
 * Time: 11:51 AM
 */

namespace Setup\Form;

use Application\Model\Model;
use Zend\Form\Annotation;

class HrEmployeesFormTabThree extends  Model
{

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Card No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     * @Annotation\Attributes({ "id":"idCardNo", "class":"form-control" })
     */
    public $idCardNo;


    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Lb rf"})
     * @Annotation\Attributes({ "id":"idLbrf", "class":"form-control" })
     */
    public $idLbrf;


    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Id Bar code"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Attributes({ "id":"idBarCode", "class":"form-control" })
     */
    public $idBarCode;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Provident Fund No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     * @Annotation\Attributes({ "id":"idProvidentFundNo", "class":"form-control" })
     */
    public $idProvidentFundNo;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Attributes({ "id":"idDrivingLicenseNo", "class":"form-control" })
     */
    public $idDrivingLicenseNo;


    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License Type"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"6"}})
     * @Annotation\Attributes({ "id":"idDrivingLicenseType", "class":"form-control" })
     */
    public $idDrivingLicenseType;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Driving License Expiry"})
     * @Annotation\Attributes({ "id":"idDrivingLicenseExpiry", "class":"form-control" })
     */
    public $idDrivingLicenseExpiry;


    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Thumb Id"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Attributes({ "id":"idThumbId", "class":"form-control" })
     */
    public $idThumbId;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Pan No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Attributes({ "id":"idPanNo", "class":"form-control" })
     */
    public $idPanNo;


    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Account Id"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Attributes({ "id":"idAccountId", "class":"form-control" })
     */
    public $idAccountId;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"CIT No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     * @Annotation\Attributes({ "id":"idRetirementNo", "class":"form-control" })
     */
    public $idRetirementNo;


    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"50"}})
     * @Annotation\Attributes({ "id":"idCitizenshipNo", "class":"form-control" })
     */
    public $idCitizenshipNo;

    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Date"})
     * @Annotation\Attributes({ "id":"idCitizenshipIssueDate", "class":"form-control" })
     */
    public $idCitizenshipIssueDate;


    /**
     * @Annotation\Required(true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Citizenship Issue Place"})
     * @Annotation\Attributes({ "id":"idCitizenshipIssuePlace", "class":"form-control" })
     */
    public $idCitizenshipIssuePlace;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Passport No"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"15"}})
     * @Annotation\Attributes({ "id":"idPassportNo", "class":"form-control" })
     */
    public $idPassportNo;


    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required(false)
     * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
     * @Annotation\Options({"label":"Passport Expiry"})
     * @Annotation\Attributes({ "id":"idPassportExpiry", "class":"form-control" })
     */
    public $idPassportExpiry;

    public $mappings=[
        'idCardNo'=>'ID_CARD_NO',

        'idLbrf'=>'ID_LBRF',
        'idBarCode'=>'ID_BAR_CODE',
        'idProvidentFundNo'=>'ID_PROVIDENT_FUND_NO',
        'idDrivingLicenseNo'=>'ID_DRIVING_LICENCE_NO',
        'idDrivingLicenseType'=>'ID_DRIVING_LICENCE_TYPE',

        'idDrivingLicenseExpiry'=>'ID_DRIVING_LICENCE_EXPIRY',
        'idThumbId'=>'ID_THUMB_ID',
        'idPanNo'=>'ID_PAN_NO',
        'idAccountId'=>'ID_ACCOUNT_NO',
        'idRetirementNo'=>'ID_RETIREMENT_NO',

        'idCitizenshipNo'=>'ID_CITIZENSHIP_NO',
        'idCitizenshipIssueDate'=>'ID_CITIZENSHIP_ISSUE_DATE',
        'idCitizenshipIssuePlace'=>'ID_CITIZENSHIP_ISSUE_PLACE',
        'idPassportNo'=>'ID_PASSPORT_NO',
        'idPassportExpiry'=>'ID_PASSPORT_EXPIRY',
    ];
}