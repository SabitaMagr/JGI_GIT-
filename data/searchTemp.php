

  'searchValues' => EntityHelper::getSearchData($this->adapter)

$this->headScript()
 ->appendFile($this->basePath('js/search.js'))

  document.searchValues =<?php echo json_encode($searchValues); ?>;

   

<div class="portlet light bg-inverse">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-paper-plane font-green-haze"></i>
                <span class="caption-subject bold font-green-haze uppercase"> Filter Employees</span>
            </div>
            <div class="tools">
                <a href="" class="collapse" data-original-title="" title=""> </a>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-sm-2">
                    <select class="form-control" name="company" id="companyId">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" name="branchId" id="branchId">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" name="department" id="departmentId">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" name="designation" id="designationId">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" name="position" id="positionId">
                    </select>
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-sm-2">
                    <select class="form-control" name="serviceType" id="serviceTypeId">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" name="serviceEventType" id="serviceEventTypeId">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" name="employee" id="employeeId">
                    </select>
                </div>
                <div class="col-sm-2 col-lg-offset-4">
                     <button ng-click="view()" id="viewEmployees" class="btn btn-default btn-sm pull-right">
                        Search
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>







                       