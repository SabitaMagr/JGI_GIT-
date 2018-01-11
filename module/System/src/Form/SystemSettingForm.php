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
     * @Annotation\Options({"value_options":{"O":"Oracle","M":"Mysql","N":"None"},"label":"Old Payslip Type"})
     * @Annotation\Attributes({ "id":"oldPayslipType"})
     */
    public $oldPayslipType;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-success","id":"btnSubmit"})
     */
    public $submit;

}
