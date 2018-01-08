<?php

namespace System\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("system-setting")
 */
class SystemSettingForm {

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Allow System Attendance"})
     * @Annotation\Attributes({ "id":"allowSystemAttendance"})
     */
    public $allowSystemAttendance;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Late Check In Approval"})
     * @Annotation\Attributes({ "id":"needApprovalForLateCheckIn"})
     */
    public $needApprovalForLateCheckIn;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Allow Account Lock"})
     * @Annotation\Attributes({ "id":"allowAccountLock"})
     */
    public $allowAccountLock;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Options({"label":"Account Lock Try Chance"})
     * @Annotation\Attributes({ "id":"accountLockTryNumber", "class":" form-control","min":"0"})
     */
    public $accountLockTryNumber;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Options({"label":"Account Lock Try Second"})
     * @Annotation\Attributes({ "id":"accountLockTrySecond", "class":" form-control","min":"0"})
     */
    public $accountLockTrySecond;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Force Password Renew"})
     * @Annotation\Attributes({ "id":"forcePasswordRenew"})
     */
    public $forcePasswordRenew;

    /**
     * @Annotation\Type("Zend\Form\Element\Number")
     * @Annotation\Options({"label":"Force Password Renew In"})
     * @Annotation\Attributes({ "id":"forcePasswordRenewDay", "class":" form-control","min":"0"})
     */
    public $forcePasswordRenewDay;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Show Address Book"})
     * @Annotation\Attributes({ "id":"showAddressBook"})
     */
    public $showAddressBook;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"value_options":{"S":"Single","M":"Multiple"},"label":"Notice Type"})
     * @Annotation\Attributes({ "id":"noticeType"})
     */
    public $noticeType;

    /**
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"value_options":{"Y":"Yes","N":"No"},"label":"Link Travel To Synergy"})
     * @Annotation\Attributes({ "id":"linkTravelToSynergy"})
     */
    public $linkTravelToSynergy;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Form Code"})
     * @Annotation\Attributes({ "id":"formCode","class":"form-control"})
     */
    public $formCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Dr Account Code"})
     * @Annotation\Attributes({ "id":"formCode","class":"form-control"})
     */
    public $drAccCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Cr Account Code"})
     * @Annotation\Attributes({ "id":"formCode","class":"form-control"})
     */
    public $crAccCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Excess Cr Account Code"})
     * @Annotation\Attributes({ "id":"formCode","class":"form-control"})
     */
    public $excessCrAccCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Select")
     * @Annotation\Required({"required":"true"})
     * @Annotation\Options({"disable_inarray_validator":"true","label":"Less Dr Account Code"})
     * @Annotation\Attributes({ "id":"formCode","class":"form-control"})
     */
    public $lessDrAccCode;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success","id":"btnSubmit"})
     */
    public $submit;

}
