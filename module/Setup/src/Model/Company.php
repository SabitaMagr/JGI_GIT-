<?php 
namespace Setup\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Hydrator\ObjectProperty")
 * @Annotation\Name("Company")
*/

class Company implements ModelInterface{
	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Company Code"})
	 * @Annotation\Attributes({ "id":"form-companyCode", "class":"form-companyCode form-control", "placeholder":"Company Code..."  })
	 */
	public $companyCode;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Company Name"})
	 * @Annotation\Attributes({ "id":"form-companyName", "class":"form-companyName form-control" })
	 */
	public $companyName;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"In Nepali"})
	 * @Annotation\Attributes({ "id":"form-inNepali", "class":"form-inNepali form-control" })
	 */
	public $inNepali;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Address(I)"})
	 * @Annotation\Attributes({ "id":"form-adderessFirst", "class":"form-adderessFirst form-control" })
	 */
	public $addressFirst;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Address(II)"})
	 * @Annotation\Attributes({ "id":"form-addressSecond", "class":"form-addressSecond form-control" })
	 */
	public $addressSecond;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"true"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Telephone"})
	 * @Annotation\Attributes({ "id":"form-telephone", "class":"form-telephone form-control","placeholder":"Enter contact number.." })
	 */
	public $telephone;

	/**
	 * @Annotion\Type("Zend\Form\Element\Email")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Email"})
	 * @Annotation\Attributes({ "id":"form-email", "class":"form-email form-control","placeholder":"Enter email address.." })
	 */
	public $email;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Fax"})
	 * @Annotation\Attributes({ "id":"form-fax", "class":"form-fax form-control","placeholder":"Enter fax number.." })
	 */
	public $fax;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Web"})
	 * @Annotation\Attributes({ "id":"form-web", "class":"form-web form-control","placeholder":"Enter website address..." })
	 */
	public $web;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Registration No"})
	 * @Annotation\Attributes({ "id":"form-registrationNo", "class":"form-registrationNo form-control" })
	 */
	public $registrationNo;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"VAT No"})
	 * @Annotation\Attributes({ "id":"form-vatNo", "class":"form-vatNo form-control" })
	 */
	public $vatNo;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"SMTP Host"})
	 * @Annotation\Attributes({ "id":"form-smtpHost", "class":"form-smtpHost form-control" })
	 */
	public $smtpHost;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Server Path"})
	 * @Annotation\Attributes({ "id":"form-serverPath", "class":"form-serverPath form-control"})
	 */
	public $serverPath;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Fiscal Start"})
	 * @Annotation\Attributes({ "id":"form-fiscalStart", "class":"form-fiscalStart form-control"})
	 */
	public $fiscalStart;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Fiscal End"})
	 * @Annotation\Attributes({ "id":"form-fiscalEnd", "class":"form-fiscalEnd form-control"})
	 */
	public $fiscalEnd;


	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Start Time"})
	 * @Annotation\Attributes({ "id":"form-startTime", "class":"form-startTime form-control"})
	 */
	public $startTime;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"End Time"})
	 * @Annotation\Attributes({ "id":"form-endTime", "class":"form-endTime form-control"})
	 */
	public $endTime;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Grace Start Time"})
	 * @Annotation\Attributes({ "id":"form-graceStartTime", "class":"form-graceStartTime form-control"})
	 */
	public $graceStartTime;

	/**
	 * @Annotion\Type("Zend\Form\Element\Text")
	 * @Annotation\Required({"required":"false"})
	 * @Annotation\Filter({"name":"StringTrim","name":"StripTags"})
	 * @Annotation\Options({"label":"Grace End Time"})
	 * @Annotation\Attributes({ "id":"form-graceEndTime", "class":"form-graceEndTime form-control"})
	 */
	public $graceEndTime;


	/**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit","class":"btn btn-primary pull-right"})
    */
    public $submit;


	public function exchangeArray(array $data)
	{
		$this->companyCode = !empty($data['companyCode']) ? $data['companyCode'] : Null;
		$this->companyName = !empty($data['companyName']) ? $data['companyName'] : Null;
		$this->inNepali = !empty($data['inNepali']) ? $data['inNepali'] : Null;
		$this->addressFirst  = !empty($data['addressFirst']) ? $data['addressFirst'] : Null;
		$this->addressSecond = !empty($data['addressSecond']) ? $data['addressSecond'] : Null;
		$this->telephone = !empty($data['telephone']) ? $data['telephone'] : Null;
		$this->email = !empty($data['email']) ? $data['email'] : Null;
		$this->fax = !empty($data['fax']) ? $data['fax'] : Null;
		$this->web = !empty($data['web']) ? $data['web'] : Null;
		$this->registrationNo = !empty($data['registrationNo']) ? $data['registrationNo'] : Null;
		$this->vatNo = !empty($data['vatNo']) ? $data['vatNo'] : Null;
		$this->smtpHost = !empty($data['smtpHost']) ? $data['smtpHost'] : Null;
		$this->serverPath = !empty($data['serverPath']) ? $data['serverPath'] : Null;
		$this->fiscalStart = !empty($data['fiscalStart']) ? $data['fiscalStart'] : Null;
		$this->fiscalEnd = !empty($data['fiscalEnd']) ? $data['fiscalEnd'] : Null;
		$this->startTime = !empty($data['startTime']) ? $data['startTime'] : Null;
		$this->endTime = !empty($data['endTime']) ? $data['endTime'] : Null;
		$this->graceStartTime = !empty($data['graceStartTime']) ? $data['graceStartTime'] : Null;
		$this->graceEndTime = !empty($data['graceEndTime']) ? $data['graceEndTime'] : Null;

	}

	public function getArrayCopy()
	{
		return [
			'companyCode'=>$this->companyCode,
			'companyName'=>$this->companyName,
			'inNepali'=>$this->inNepali,
			'addressFirst'=>$this->addressFirst,
			'addressSecond'=>$this->addressSecond,
			'telephone'=>$this->telephone,
			'email'=>$this->email,
			'fax'=>$this->fax,
			'web'=>$this->web,
			'registrationNo'=>$this->registrationNo,
			'vatNo'=>$this->vatNo,
			'smtpHost'=>$this->smtpHost,
			'serverPath'=>$this->serverPath,
			'fiscalStart'=>$this->fiscalStart,
			'fiscalEnd'=>$this->fiscalEnd,
			'startTime'=>$this->startTime,
			'endTime'=>$this->endTime,
			'graceStartTime'=>$this->graceStartTime,
			'graceEndTime'=>$this->graceEndTime,
		];
	}
}
