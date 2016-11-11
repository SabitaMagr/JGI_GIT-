<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/16
 * Time: 5:13 PM
 */
namespace Setup\Model;

use Application\Model\Model;

class AcademicUniversity extends Model {
    const TABLE_NAME = "HR_ACADEMIC_UNIVERSITY";
    const ACADEMIC_UNIVERSITY_ID = "ACADEMIC_UNIVERSITY_ID";
    const ACADEMIC_UNIVERSITY_CODE = "ACADEMIC_UNIVERSITY_CODE";
    const ACADEMIC_UNIVERSITY_NAME = "ACADEMIC_UNIVERSITY_NAME";
    const REMARKS = "REMARKS";
    const STATUS = "STATUS";
    const CREATED_DT = "CREATED_DT";
    const MODIFIED_DT = "MODIFIED_DT";

    public $academicUniversityId;
    public $academicUniversityCode;
    public $academicUniversityName;
    public $remarks;
    public $status;
    public $createdDt;
    public $modifiedDt;

    public $mappings = [
        'academicUniversityId'=>Self::ACADEMIC_UNIVERSITY_ID,
        'academicUniversityCode'=>Self::ACADEMIC_UNIVERSITY_CODE,
        'academicUniversityName'=>Self::ACADEMIC_UNIVERSITY_NAME,
        'remarks'=>Self::REMARKS,
        'status'=>Self::STATUS,
        'createdDt'=>Self::CREATED_DT,
        'modifiedDt'=>Self::MODIFIED_DT
    ];
}