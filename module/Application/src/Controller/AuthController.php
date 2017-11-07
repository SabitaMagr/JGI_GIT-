<?php

namespace Application\Controller;

use Application\Helper\Helper;
use Application\Model\HrisAuthStorage;
use Application\Model\User;
use Application\Model\UserLog;
use Application\Repository\LoginRepository;
use Application\Repository\MonthRepository;
use Application\Repository\UserLogRepository;
use AttendanceManagement\Repository\AttendanceDetailRepository;
use Setup\Repository\EmployeeRepository;
use System\Repository\RolePermissionRepository;
use System\Repository\RoleSetupRepository;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController {

    protected $form;
    protected $storage;
    protected $authservice;
    protected $adapter;

    public function __construct(AuthenticationService $authService, AdapterInterface $adapter) {
        $this->authservice = $authService;
        $this->storage = $authService->getStorage();
        $this->adapter = $adapter;
    }

    public function setEventManager(EventManagerInterface $events) {
        parent::setEventManager($events);
        $controller = $this;
        $events->attach('dispatch', function ($e) use ($controller) {
            $controller->layout('layout/login');
        }, 100);
    }

    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()
                    ->get('AuthService');
        }
        return $this->authservice;
    }

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()
                    ->get(HrisAuthStorage::class);
        }
        return $this->storage;
    }

    public function getForm() {
        if (!$this->form) {
            $user = new User();
            $builder = new AnnotationBuilder();
            $this->form = $builder->createForm($user);
        }

        return $this->form;
    }

    public function loginAction() {
        //to make register attendance by default checked on login page:: condition start
        $type = $this->params()->fromRoute('type');
        if ($type !== null) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();
        }
        //end

        if ($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('dashboard');
        }
        $form = $this->getForm();
        return new ViewModel([
            'form' => $form,
            'type' => $type,
            'messages' => $this->flashmessenger()->getMessages()
        ]);
    }

    public function authenticateAction() {
        $form = $this->getForm();
        $redirect = 'login';
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                /*
                 * password expiration check | comment this code if this feature is not needed
                 */
//                $needPwdChange = $this->checkPasswordExpire($request->getPost('username'));
//                if ($needPwdChange) {
//                    return $this->redirect()->toRoute('updatePwd', ['action' => 'changePwd', 'un' => $request->getPost('username')]);
//                }
                /*
                 * end of password expiration check
                 */
                /*
                 * user authentication
                 */
                $this->getAuthService()->getAdapter()
                        ->setIdentity($request->getPost('username'))
                        ->setCredential($request->getPost('password'));
                $result = $this->getAuthService()->authenticate();
                foreach ($result->getMessages() as $message) {
                    $this->flashmessenger()->addMessage($message);
                }
                $redirect = 'login';
                if ($result->isValid()) {
                    if (isset($_COOKIE[$request->getPost('username')])) {
                        setcookie($request->getPost('username'), '', 1, "/");
                    }
                    //after authentication success get the user specific details
                    $resultRow = $this->getAuthService()->getAdapter()->getResultRowObject();

                    if ($resultRow->IS_LOCKED == 'Y') {
                        $this->flashmessenger()->clearCurrentMessages();
                        $this->flashmessenger()->addMessage('The account ' . $resultRow->USER_NAME . ' has been locked Please contact the Admin');
                        $this->getAuthService()->clearIdentity();
                        return $this->redirect()->toRoute($redirect);
                    }

                    $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);

                    $employeeId = $resultRow->EMPLOYEE_ID;
                    $attendanceDetailRepo = new AttendanceDetailRepository($this->adapter);
                    $todayAttendance = $attendanceDetailRepo->fetchByEmpIdAttendanceDT($employeeId, 'TRUNC(SYSDATE)');
                    $inTime = $todayAttendance['IN_TIME'];
                    $attendanceType = ($inTime) ? "OUT" : "IN";
                    $allowRegisterAttendance = ($todayAttendance['TRAVEL_ID'] == null && $todayAttendance['LEAVE_ID'] == null && $todayAttendance['TRAINING_ID'] == null && $todayAttendance['HOLIDAY_ID'] == null) ? true : false;

                    $employeeRepo = new EmployeeRepository($this->adapter);
                    $employeeDetail = $employeeRepo->employeeDetailSession($resultRow->EMPLOYEE_ID);

                    $monthRepo = new MonthRepository($this->adapter);
                    $fiscalYear = $monthRepo->getCurrentFiscalYear();

                    $repository = new RolePermissionRepository($this->adapter);
                    $rawMenus = $repository->fetchAllMenuByRoleId($resultRow->ROLE_ID);
                    $menus = Helper::extractDbData($rawMenus);

                    $roleRepo = new RoleSetupRepository($this->adapter);
                    $acl = $roleRepo->fetchById($resultRow->ROLE_ID);

                    $this->getAuthService()->getStorage()->write([
                        "user_name" => $request->getPost('username'),
                        "user_id" => $resultRow->USER_ID,
                        "employee_id" => $resultRow->EMPLOYEE_ID,
                        "role_id" => $resultRow->ROLE_ID,
                        "employee_detail" => $employeeDetail,
                        "fiscal_year" => $fiscalYear,
                        "menus" => $menus,
                        'register_attendance' => $attendanceType,
                        'allow_register_attendance' => $allowRegisterAttendance,
                        'acl' => (array) $acl
                    ]);


                    // to add user log details in HRIS_USER_LOG
                    $this->setUserLog($this->adapter, $request->getServer('REMOTE_ADDR'), $resultRow->USER_ID);

                    /*
                     * 
                     */
                    if (1 == $request->getPost('rememberme')) {
                        $this->getSessionStorage()
                                ->setRememberMe(1);
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    /*
                     * 
                     */

                    $redirect = 'dashboard';
                } else {
                    $loginRepo = new LoginRepository($this->adapter);
                    $userValid = $loginRepo->fetchByUserName($request->getPost('username'));


                    if (!$userValid) {
                        $this->flashmessenger()->clearCurrentMessages();
                        $this->flashmessenger()->addMessage('UserName ' . $request->getPost('username') . ' is not a valid username');
                        $this->getAuthService()->clearIdentity();
                        return $this->redirect()->toRoute($redirect);
                    }

                    if ($userValid['IS_LOCKED'] == 'Y') {
                        $this->flashmessenger()->clearCurrentMessages();
                        $this->flashmessenger()->addMessage('UserName ' . $request->getPost('username') . ' is has been locked please contact admin');
                        $this->getAuthService()->clearIdentity();
                        return $this->redirect()->toRoute($redirect);
                    }

                    $cookie_name = $request->getPost('username');
                    if (!isset($_COOKIE[$cookie_name])) {
                        $cookie_value = 2;
                        setcookie($cookie_name, $cookie_value, time() + 3600, "/");
                    } else {
                        if ($_COOKIE[$cookie_name] < 5) {
                            $newCookieValue = $_COOKIE[$cookie_name] + 1;
                            $atteptLeft = 6 - $newCookieValue;
                            setcookie($cookie_name, $newCookieValue, time() + 3600, "/");
                            $this->flashmessenger()->clearCurrentMessages();
                            $this->flashmessenger()->addMessage('incorrect username and password for ' . $cookie_name . ' after ' . $atteptLeft . ' unsucessfull attempt account will be locked');
                            $this->getAuthService()->clearIdentity();
                        } else {
                            $loginRepo->updateByUserName($request->getPost('username'));
                            setcookie($cookie_name, '', 1, "/");
                            $this->flashmessenger()->clearCurrentMessages();
                            $this->flashmessenger()->addMessage('the account ' . $cookie_name . ' has been locked Please contact the Admin');
                            $this->getAuthService()->clearIdentity();
                        }
                    }
                }
            }
        }
        return $this->redirect()->toRoute($redirect);
    }

    private function setUserLog(AdapterInterface $adapter, $clientIp, $userId) {
        $userLogRepo = new UserLogRepository($adapter);

        $userLog = new UserLog();
        $userLog->loginIp = $clientIp;
        $userLog->userId = $userId;

        $userLogRepo->add($userLog);
    }

    public function logoutAction() {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('login');
    }

    public function checkPasswordExpire($userName) {
        $maxPasswordDays = 45;
        $loginRepo = new LoginRepository($this->adapter);
        $result = $loginRepo->checkPasswordExpire($userName);
        $createdDays = $result['CREATED_DAYS'];
        $modifiedDays = $result['MODIFIED_DAYS'];
        $isLocked = $result['IS_LOCKED'];

        if ($modifiedDays == null) {
            $passwordDays = $createdDays;
        } else {
            $passwordDays = $modifiedDays;
        }

        if ($isLocked == 'Y') {
            return false;
        }

        if ($passwordDays > $maxPasswordDays) {
            return true;
        } else {
            return false;
        }
    }

    public function changePwdAction() {
        $userName = $this->params()->fromRoute('un');
        $message = [];

        $request = $this->getRequest();
        $loginRepo = new LoginRepository($this->adapter);
        if ($request->isPost()) {
            $postData = $request->getPost();
            $userName = $postData['username'];
            $oldPassword = $postData['oldpassword'];
            $newPassword = $postData['password'];
            $userOldPassword = $loginRepo->getPwdByUserName($userName);

            if ($oldPassword != $userOldPassword) {
                array_push($message, 'old password is not correct');
            } elseif ($oldPassword == $userOldPassword && $userOldPassword == $newPassword) {
                array_push($message, 'new password cannot be same as old password');
            } else {
                $loginRepo->updatePwdByUserName($userName, $newPassword);
                $this->flashmessenger()->addMessage('please login with updated password');
                return $this->redirect()->toRoute('login');
            }
        }

        return new ViewModel([
            'userName' => $userName,
            'messages' => $message
        ]);
    }

}
