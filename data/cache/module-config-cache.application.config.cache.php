<?php
return array (
  'service_manager' => 
  array (
    'abstract_factories' => 
    array (
      0 => 'Zend\\Navigation\\Service\\NavigationAbstractServiceFactory',
      1 => 'Zend\\Db\\Adapter\\AdapterAbstractServiceFactory',
      2 => 'Zend\\Session\\Service\\ContainerAbstractServiceFactory',
      3 => 'Zend\\Log\\LoggerAbstractServiceFactory',
      4 => 'Zend\\Form\\FormAbstractServiceFactory',
      5 => 'Zend\\Cache\\Service\\StorageCacheAbstractServiceFactory',
    ),
    'aliases' => 
    array (
      'navigation' => 'Zend\\Navigation\\Navigation',
      'Zend\\Db\\Adapter\\Adapter' => 'Zend\\Db\\Adapter\\AdapterInterface',
      'Di' => 'DependencyInjector',
      'Zend\\Di\\LocatorInterface' => 'DependencyInjector',
      'Zend\\Session\\SessionManager' => 'Zend\\Session\\ManagerInterface',
      'MvcTranslator' => 'Zend\\Mvc\\I18n\\Translator',
      'Zend\\Form\\Annotation\\FormAnnotationBuilder' => 'FormAnnotationBuilder',
      'Zend\\Form\\Annotation\\AnnotationBuilder' => 'FormAnnotationBuilder',
      'Zend\\Form\\FormElementManager' => 'FormElementManager',
      'HttpRouter' => 'Zend\\Router\\Http\\TreeRouteStack',
      'router' => 'Zend\\Router\\RouteStackInterface',
      'Router' => 'Zend\\Router\\RouteStackInterface',
      'RoutePluginManager' => 'Zend\\Router\\RoutePluginManager',
      'console' => 'ConsoleAdapter',
      'Console' => 'ConsoleAdapter',
      'ConsoleDefaultRenderingStrategy' => 'Zend\\Mvc\\Console\\View\\DefaultRenderingStrategy',
      'ConsoleRenderer' => 'Zend\\Mvc\\Console\\View\\Renderer',
    ),
    'delegators' => 
    array (
      'ViewHelperManager' => 
      array (
        0 => 'Zend\\Navigation\\View\\ViewHelperManagerDelegatorFactory',
        1 => 'Zend\\Mvc\\Console\\Service\\ConsoleViewHelperManagerDelegatorFactory',
      ),
      'HttpRouter' => 
      array (
        0 => 'Zend\\Mvc\\I18n\\Router\\HttpRouterDelegatorFactory',
      ),
      'Zend\\Router\\Http\\TreeRouteStack' => 
      array (
        0 => 'Zend\\Mvc\\I18n\\Router\\HttpRouterDelegatorFactory',
      ),
      'ControllerManager' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ControllerManagerDelegatorFactory',
      ),
      'Request' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ConsoleRequestDelegatorFactory',
      ),
      'Response' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ConsoleResponseDelegatorFactory',
      ),
      'Zend\\Router\\RouteStackInterface' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Router\\ConsoleRouterDelegatorFactory',
      ),
      'Zend\\Mvc\\SendResponseListener' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ConsoleResponseSenderDelegatorFactory',
      ),
      'ViewManager' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ViewManagerDelegatorFactory',
      ),
    ),
    'factories' => 
    array (
      'Zend\\Navigation\\Navigation' => 'Zend\\Navigation\\Service\\DefaultNavigationFactory',
      'Zend\\Db\\Adapter\\AdapterInterface' => 'Zend\\Db\\Adapter\\AdapterServiceFactory',
      'DependencyInjector' => 'Zend\\ServiceManager\\Di\\DiFactory',
      'DiAbstractServiceFactory' => 'Zend\\ServiceManager\\Di\\DiAbstractServiceFactoryFactory',
      'DiServiceInitializer' => 'Zend\\ServiceManager\\Di\\DiServiceInitializerFactory',
      'DiStrictAbstractServiceFactory' => 'Zend\\ServiceManager\\Di\\DiStrictAbstractServiceFactoryFactory',
      'Zend\\Session\\Config\\ConfigInterface' => 'Zend\\Session\\Service\\SessionConfigFactory',
      'Zend\\Session\\ManagerInterface' => 'Zend\\Session\\Service\\SessionManagerFactory',
      'Zend\\Session\\Storage\\StorageInterface' => 'Zend\\Session\\Service\\StorageFactory',
      'Zend\\Mvc\\I18n\\Translator' => 'Zend\\Mvc\\I18n\\TranslatorFactory',
      'Zend\\Log\\Logger' => 'Zend\\Log\\LoggerServiceFactory',
      'LogFilterManager' => 'Zend\\Log\\FilterPluginManagerFactory',
      'LogFormatterManager' => 'Zend\\Log\\FormatterPluginManagerFactory',
      'LogProcessorManager' => 'Zend\\Log\\ProcessorPluginManagerFactory',
      'LogWriterManager' => 'Zend\\Log\\WriterPluginManagerFactory',
      'FormAnnotationBuilder' => 'Zend\\Form\\Annotation\\AnnotationBuilderFactory',
      'FormElementManager' => 'Zend\\Form\\FormElementManagerFactory',
      'Zend\\Cache\\PatternPluginManager' => 'Zend\\Cache\\Service\\PatternPluginManagerFactory',
      'Zend\\Cache\\Storage\\AdapterPluginManager' => 'Zend\\Cache\\Service\\StorageAdapterPluginManagerFactory',
      'Zend\\Cache\\Storage\\PluginManager' => 'Zend\\Cache\\Service\\StoragePluginManagerFactory',
      'Zend\\Router\\Http\\TreeRouteStack' => 'Zend\\Router\\Http\\HttpRouterFactory',
      'Zend\\Router\\RoutePluginManager' => 'Zend\\Router\\RoutePluginManagerFactory',
      'Zend\\Router\\RouteStackInterface' => 'Zend\\Router\\RouterFactory',
      'ValidatorManager' => 'Zend\\Validator\\ValidatorPluginManagerFactory',
      'ConsoleAdapter' => 'Zend\\Mvc\\Console\\Service\\ConsoleAdapterFactory',
      'ConsoleExceptionStrategy' => 'Zend\\Mvc\\Console\\Service\\ConsoleExceptionStrategyFactory',
      'ConsoleRouteNotFoundStrategy' => 'Zend\\Mvc\\Console\\Service\\ConsoleRouteNotFoundStrategyFactory',
      'ConsoleRouter' => 'Zend\\Mvc\\Console\\Router\\ConsoleRouterFactory',
      'ConsoleViewManager' => 'Zend\\Mvc\\Console\\Service\\ConsoleViewManagerFactory',
      'Zend\\Mvc\\Console\\View\\DefaultRenderingStrategy' => 'Zend\\Mvc\\Console\\Service\\DefaultRenderingStrategyFactory',
      'Zend\\Mvc\\Console\\View\\Renderer' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'navigation-menu' => 'Application\\Navigation\\NavigationFactory',
      'Zend\\Db\\Adapter\\Adapter' => 'Zend\\Db\\Adapter\\AdapterServiceFactory',
    ),
  ),
  'controller_plugins' => 
  array (
    'aliases' => 
    array (
      'prg' => 'Zend\\Mvc\\Plugin\\Prg\\PostRedirectGet',
      'PostRedirectGet' => 'Zend\\Mvc\\Plugin\\Prg\\PostRedirectGet',
      'postRedirectGet' => 'Zend\\Mvc\\Plugin\\Prg\\PostRedirectGet',
      'postredirectget' => 'Zend\\Mvc\\Plugin\\Prg\\PostRedirectGet',
      'Zend\\Mvc\\Controller\\Plugin\\PostRedirectGet' => 'Zend\\Mvc\\Plugin\\Prg\\PostRedirectGet',
      'identity' => 'Zend\\Mvc\\Plugin\\Identity\\Identity',
      'Identity' => 'Zend\\Mvc\\Plugin\\Identity\\Identity',
      'Zend\\Mvc\\Controller\\Plugin\\Identity' => 'Zend\\Mvc\\Plugin\\Identity\\Identity',
      'flashmessenger' => 'Zend\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger',
      'flashMessenger' => 'Zend\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger',
      'FlashMessenger' => 'Zend\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger',
      'Zend\\Mvc\\Controller\\Plugin\\FlashMessenger' => 'Zend\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger',
      'fileprg' => 'Zend\\Mvc\\Plugin\\FilePrg\\FilePostRedirectGet',
      'FilePostRedirectGet' => 'Zend\\Mvc\\Plugin\\FilePrg\\FilePostRedirectGet',
      'filePostRedirectGet' => 'Zend\\Mvc\\Plugin\\FilePrg\\FilePostRedirectGet',
      'filepostredirectget' => 'Zend\\Mvc\\Plugin\\FilePrg\\FilePostRedirectGet',
      'Zend\\Mvc\\Controller\\Plugin\\FilePostRedirectGet' => 'Zend\\Mvc\\Plugin\\FilePrg\\FilePostRedirectGet',
      'CreateConsoleNotFoundModel' => 'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
      'createConsoleNotFoundModel' => 'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
      'createconsolenotfoundmodel' => 'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
      'Zend\\Mvc\\Controller\\Plugin\\CreateConsoleNotFoundModel::class' => 'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
    ),
    'factories' => 
    array (
      'Zend\\Mvc\\Plugin\\Prg\\PostRedirectGet' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Mvc\\Plugin\\Identity\\Identity' => 'Zend\\Mvc\\Plugin\\Identity\\IdentityFactory',
      'Zend\\Mvc\\Plugin\\FlashMessenger\\FlashMessenger' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Mvc\\Plugin\\FilePrg\\FilePostRedirectGet' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
    ),
  ),
  'view_helpers' => 
  array (
    'aliases' => 
    array (
      'form' => 'Zend\\Form\\View\\Helper\\Form',
      'Form' => 'Zend\\Form\\View\\Helper\\Form',
      'formbutton' => 'Zend\\Form\\View\\Helper\\FormButton',
      'form_button' => 'Zend\\Form\\View\\Helper\\FormButton',
      'formButton' => 'Zend\\Form\\View\\Helper\\FormButton',
      'FormButton' => 'Zend\\Form\\View\\Helper\\FormButton',
      'formcaptcha' => 'Zend\\Form\\View\\Helper\\FormCaptcha',
      'form_captcha' => 'Zend\\Form\\View\\Helper\\FormCaptcha',
      'formCaptcha' => 'Zend\\Form\\View\\Helper\\FormCaptcha',
      'FormCaptcha' => 'Zend\\Form\\View\\Helper\\FormCaptcha',
      'captchadumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'captcha_dumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'captcha/dumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'CaptchaDumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'captchaDumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'formcaptchadumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'form_captcha_dumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'formCaptchaDumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'FormCaptchaDumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'captchafiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'captcha/figlet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'captcha_figlet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'captchaFiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'CaptchaFiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'formcaptchafiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'form_captcha_figlet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'formCaptchaFiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'FormCaptchaFiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'captchaimage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'captcha/image' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'captcha_image' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'captchaImage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'CaptchaImage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'formcaptchaimage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'form_captcha_image' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'formCaptchaImage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'FormCaptchaImage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'captcharecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'captcha/recaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'captcha_recaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'captchaRecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'CaptchaRecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'formcaptcharecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'form_captcha_recaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'formCaptchaRecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'FormCaptchaRecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'formcheckbox' => 'Zend\\Form\\View\\Helper\\FormCheckbox',
      'form_checkbox' => 'Zend\\Form\\View\\Helper\\FormCheckbox',
      'formCheckbox' => 'Zend\\Form\\View\\Helper\\FormCheckbox',
      'FormCheckbox' => 'Zend\\Form\\View\\Helper\\FormCheckbox',
      'formcollection' => 'Zend\\Form\\View\\Helper\\FormCollection',
      'form_collection' => 'Zend\\Form\\View\\Helper\\FormCollection',
      'formCollection' => 'Zend\\Form\\View\\Helper\\FormCollection',
      'FormCollection' => 'Zend\\Form\\View\\Helper\\FormCollection',
      'formcolor' => 'Zend\\Form\\View\\Helper\\FormColor',
      'form_color' => 'Zend\\Form\\View\\Helper\\FormColor',
      'formColor' => 'Zend\\Form\\View\\Helper\\FormColor',
      'FormColor' => 'Zend\\Form\\View\\Helper\\FormColor',
      'formdate' => 'Zend\\Form\\View\\Helper\\FormDate',
      'form_date' => 'Zend\\Form\\View\\Helper\\FormDate',
      'formDate' => 'Zend\\Form\\View\\Helper\\FormDate',
      'FormDate' => 'Zend\\Form\\View\\Helper\\FormDate',
      'formdatetime' => 'Zend\\Form\\View\\Helper\\FormDateTime',
      'form_date_time' => 'Zend\\Form\\View\\Helper\\FormDateTime',
      'formDateTime' => 'Zend\\Form\\View\\Helper\\FormDateTime',
      'FormDateTime' => 'Zend\\Form\\View\\Helper\\FormDateTime',
      'formdatetimelocal' => 'Zend\\Form\\View\\Helper\\FormDateTimeLocal',
      'form_date_time_local' => 'Zend\\Form\\View\\Helper\\FormDateTimeLocal',
      'formDateTimeLocal' => 'Zend\\Form\\View\\Helper\\FormDateTimeLocal',
      'FormDateTimeLocal' => 'Zend\\Form\\View\\Helper\\FormDateTimeLocal',
      'formdatetimeselect' => 'Zend\\Form\\View\\Helper\\FormDateTimeSelect',
      'form_date_time_select' => 'Zend\\Form\\View\\Helper\\FormDateTimeSelect',
      'formDateTimeSelect' => 'Zend\\Form\\View\\Helper\\FormDateTimeSelect',
      'FormDateTimeSelect' => 'Zend\\Form\\View\\Helper\\FormDateTimeSelect',
      'formdateselect' => 'Zend\\Form\\View\\Helper\\FormDateSelect',
      'form_date_select' => 'Zend\\Form\\View\\Helper\\FormDateSelect',
      'formDateSelect' => 'Zend\\Form\\View\\Helper\\FormDateSelect',
      'FormDateSelect' => 'Zend\\Form\\View\\Helper\\FormDateSelect',
      'form_element' => 'Zend\\Form\\View\\Helper\\FormElement',
      'formelement' => 'Zend\\Form\\View\\Helper\\FormElement',
      'formElement' => 'Zend\\Form\\View\\Helper\\FormElement',
      'FormElement' => 'Zend\\Form\\View\\Helper\\FormElement',
      'form_element_errors' => 'Zend\\Form\\View\\Helper\\FormElementErrors',
      'formelementerrors' => 'Zend\\Form\\View\\Helper\\FormElementErrors',
      'formElementErrors' => 'Zend\\Form\\View\\Helper\\FormElementErrors',
      'FormElementErrors' => 'Zend\\Form\\View\\Helper\\FormElementErrors',
      'form_email' => 'Zend\\Form\\View\\Helper\\FormEmail',
      'formemail' => 'Zend\\Form\\View\\Helper\\FormEmail',
      'formEmail' => 'Zend\\Form\\View\\Helper\\FormEmail',
      'FormEmail' => 'Zend\\Form\\View\\Helper\\FormEmail',
      'form_file' => 'Zend\\Form\\View\\Helper\\FormFile',
      'formfile' => 'Zend\\Form\\View\\Helper\\FormFile',
      'formFile' => 'Zend\\Form\\View\\Helper\\FormFile',
      'FormFile' => 'Zend\\Form\\View\\Helper\\FormFile',
      'formfileapcprogress' => 'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress',
      'form_file_apc_progress' => 'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress',
      'formFileApcProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress',
      'FormFileApcProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress',
      'formfilesessionprogress' => 'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress',
      'form_file_session_progress' => 'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress',
      'formFileSessionProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress',
      'FormFileSessionProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress',
      'formfileuploadprogress' => 'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress',
      'form_file_upload_progress' => 'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress',
      'formFileUploadProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress',
      'FormFileUploadProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress',
      'formhidden' => 'Zend\\Form\\View\\Helper\\FormHidden',
      'form_hidden' => 'Zend\\Form\\View\\Helper\\FormHidden',
      'formHidden' => 'Zend\\Form\\View\\Helper\\FormHidden',
      'FormHidden' => 'Zend\\Form\\View\\Helper\\FormHidden',
      'formimage' => 'Zend\\Form\\View\\Helper\\FormImage',
      'form_image' => 'Zend\\Form\\View\\Helper\\FormImage',
      'formImage' => 'Zend\\Form\\View\\Helper\\FormImage',
      'FormImage' => 'Zend\\Form\\View\\Helper\\FormImage',
      'forminput' => 'Zend\\Form\\View\\Helper\\FormInput',
      'form_input' => 'Zend\\Form\\View\\Helper\\FormInput',
      'formInput' => 'Zend\\Form\\View\\Helper\\FormInput',
      'FormInput' => 'Zend\\Form\\View\\Helper\\FormInput',
      'formlabel' => 'Zend\\Form\\View\\Helper\\FormLabel',
      'form_label' => 'Zend\\Form\\View\\Helper\\FormLabel',
      'formLabel' => 'Zend\\Form\\View\\Helper\\FormLabel',
      'FormLabel' => 'Zend\\Form\\View\\Helper\\FormLabel',
      'formmonth' => 'Zend\\Form\\View\\Helper\\FormMonth',
      'form_month' => 'Zend\\Form\\View\\Helper\\FormMonth',
      'formMonth' => 'Zend\\Form\\View\\Helper\\FormMonth',
      'FormMonth' => 'Zend\\Form\\View\\Helper\\FormMonth',
      'formmonthselect' => 'Zend\\Form\\View\\Helper\\FormMonthSelect',
      'form_month_select' => 'Zend\\Form\\View\\Helper\\FormMonthSelect',
      'formMonthSelect' => 'Zend\\Form\\View\\Helper\\FormMonthSelect',
      'FormMonthSelect' => 'Zend\\Form\\View\\Helper\\FormMonthSelect',
      'formmulticheckbox' => 'Zend\\Form\\View\\Helper\\FormMultiCheckbox',
      'form_multi_checkbox' => 'Zend\\Form\\View\\Helper\\FormMultiCheckbox',
      'formMultiCheckbox' => 'Zend\\Form\\View\\Helper\\FormMultiCheckbox',
      'FormMultiCheckbox' => 'Zend\\Form\\View\\Helper\\FormMultiCheckbox',
      'formnumber' => 'Zend\\Form\\View\\Helper\\FormNumber',
      'form_number' => 'Zend\\Form\\View\\Helper\\FormNumber',
      'formNumber' => 'Zend\\Form\\View\\Helper\\FormNumber',
      'FormNumber' => 'Zend\\Form\\View\\Helper\\FormNumber',
      'formpassword' => 'Zend\\Form\\View\\Helper\\FormPassword',
      'form_password' => 'Zend\\Form\\View\\Helper\\FormPassword',
      'formPassword' => 'Zend\\Form\\View\\Helper\\FormPassword',
      'FormPassword' => 'Zend\\Form\\View\\Helper\\FormPassword',
      'formradio' => 'Zend\\Form\\View\\Helper\\FormRadio',
      'form_radio' => 'Zend\\Form\\View\\Helper\\FormRadio',
      'formRadio' => 'Zend\\Form\\View\\Helper\\FormRadio',
      'FormRadio' => 'Zend\\Form\\View\\Helper\\FormRadio',
      'formrange' => 'Zend\\Form\\View\\Helper\\FormRange',
      'form_range' => 'Zend\\Form\\View\\Helper\\FormRange',
      'formRange' => 'Zend\\Form\\View\\Helper\\FormRange',
      'FormRange' => 'Zend\\Form\\View\\Helper\\FormRange',
      'formreset' => 'Zend\\Form\\View\\Helper\\FormReset',
      'form_reset' => 'Zend\\Form\\View\\Helper\\FormReset',
      'formReset' => 'Zend\\Form\\View\\Helper\\FormReset',
      'FormReset' => 'Zend\\Form\\View\\Helper\\FormReset',
      'formrow' => 'Zend\\Form\\View\\Helper\\FormRow',
      'form_row' => 'Zend\\Form\\View\\Helper\\FormRow',
      'formRow' => 'Zend\\Form\\View\\Helper\\FormRow',
      'FormRow' => 'Zend\\Form\\View\\Helper\\FormRow',
      'formsearch' => 'Zend\\Form\\View\\Helper\\FormSearch',
      'form_search' => 'Zend\\Form\\View\\Helper\\FormSearch',
      'formSearch' => 'Zend\\Form\\View\\Helper\\FormSearch',
      'FormSearch' => 'Zend\\Form\\View\\Helper\\FormSearch',
      'formselect' => 'Zend\\Form\\View\\Helper\\FormSelect',
      'form_select' => 'Zend\\Form\\View\\Helper\\FormSelect',
      'formSelect' => 'Zend\\Form\\View\\Helper\\FormSelect',
      'FormSelect' => 'Zend\\Form\\View\\Helper\\FormSelect',
      'formsubmit' => 'Zend\\Form\\View\\Helper\\FormSubmit',
      'form_submit' => 'Zend\\Form\\View\\Helper\\FormSubmit',
      'formSubmit' => 'Zend\\Form\\View\\Helper\\FormSubmit',
      'FormSubmit' => 'Zend\\Form\\View\\Helper\\FormSubmit',
      'formtel' => 'Zend\\Form\\View\\Helper\\FormTel',
      'form_tel' => 'Zend\\Form\\View\\Helper\\FormTel',
      'formTel' => 'Zend\\Form\\View\\Helper\\FormTel',
      'FormTel' => 'Zend\\Form\\View\\Helper\\FormTel',
      'formtext' => 'Zend\\Form\\View\\Helper\\FormText',
      'form_text' => 'Zend\\Form\\View\\Helper\\FormText',
      'formText' => 'Zend\\Form\\View\\Helper\\FormText',
      'FormText' => 'Zend\\Form\\View\\Helper\\FormText',
      'formtextarea' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'form_text_area' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'formTextarea' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'formTextArea' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'FormTextArea' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'formtime' => 'Zend\\Form\\View\\Helper\\FormTime',
      'form_time' => 'Zend\\Form\\View\\Helper\\FormTime',
      'formTime' => 'Zend\\Form\\View\\Helper\\FormTime',
      'FormTime' => 'Zend\\Form\\View\\Helper\\FormTime',
      'formurl' => 'Zend\\Form\\View\\Helper\\FormUrl',
      'form_url' => 'Zend\\Form\\View\\Helper\\FormUrl',
      'formUrl' => 'Zend\\Form\\View\\Helper\\FormUrl',
      'FormUrl' => 'Zend\\Form\\View\\Helper\\FormUrl',
      'formweek' => 'Zend\\Form\\View\\Helper\\FormWeek',
      'form_week' => 'Zend\\Form\\View\\Helper\\FormWeek',
      'formWeek' => 'Zend\\Form\\View\\Helper\\FormWeek',
      'FormWeek' => 'Zend\\Form\\View\\Helper\\FormWeek',
    ),
    'factories' => 
    array (
      'Zend\\Form\\View\\Helper\\Form' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormButton' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormCaptcha' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\Captcha\\Dumb' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\Captcha\\Figlet' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\Captcha\\Image' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormCheckbox' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormCollection' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormColor' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDate' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDateTime' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDateTimeLocal' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDateTimeSelect' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDateSelect' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormElement' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormElementErrors' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormEmail' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormFile' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormHidden' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormImage' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormInput' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormLabel' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormMonth' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormMonthSelect' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormMultiCheckbox' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormNumber' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormPassword' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormRadio' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormRange' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormReset' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormRow' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormSearch' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormSelect' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormSubmit' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormTel' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormText' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormTextarea' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormTime' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormUrl' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormWeek' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
    ),
  ),
  'route_manager' => 
  array (
  ),
  'router' => 
  array (
    'routes' => 
    array (
      'home' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/',
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\IndexController',
            'action' => 'index',
          ),
        ),
      ),
      'application' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/application[/:action]',
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\IndexController',
            'action' => 'index',
          ),
        ),
      ),
      'auth' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/auth',
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\AuthController',
            'action' => 'login',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'process' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '[/:action]',
              'constraints' => 
              array (
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
              ),
              'defaults' => 
              array (
              ),
            ),
          ),
        ),
      ),
      'login' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/login',
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\AuthController',
            'action' => 'login',
          ),
        ),
      ),
      'logout' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/logout',
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\AuthController',
            'action' => 'logout',
          ),
        ),
      ),
      'dashboard' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/dashboard',
          'defaults' => 
          array (
            'controller' => 'Application\\Controller\\DashboardController',
            'action' => 'index',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'process' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '[/:action]',
              'constraints' => 
              array (
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
              ),
              'defaults' => 
              array (
              ),
            ),
          ),
        ),
      ),
      'employee' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/employee[/:action[/:id[/:tab]]]',
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\EmployeeController',
            'action' => 'index',
          ),
        ),
      ),
      'designation' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/designation[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\DesignationController',
            'action' => 'index',
          ),
        ),
      ),
      'company' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/company[/:action[/:id]]',
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\CompanyController',
            'action' => 'index',
          ),
        ),
      ),
      'branch' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/branch[/:action[/:id]]',
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\BranchController',
            'action' => 'index',
          ),
        ),
      ),
      'department' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/department[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\DepartmentController',
            'action' => 'index',
          ),
        ),
      ),
      'position' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/position[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\PositionController',
            'action' => 'index',
          ),
        ),
      ),
      'serviceType' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/serviceType[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\ServiceTypeController',
            'action' => 'index',
          ),
        ),
      ),
      'jobHistory' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/history/jobHistory[/:action[/:id]]',
          'constant' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\JobHistoryController',
            'action' => 'index',
          ),
        ),
      ),
      'empCurrentPosting' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/empCurrentPosting[/:action[/:id]]',
          'constant' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\EmpCurrentPostingController',
            'action' => 'index',
          ),
        ),
      ),
      'webService' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/webService[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\WebServiceController',
            'action' => 'index',
          ),
        ),
      ),
      'recommendapprove' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/recommendapprove[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\RecommendApproveController',
            'action' => 'index',
          ),
        ),
      ),
      'serviceEventType' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/serviceEventType[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\ServiceEventTypeController',
            'action' => 'index',
          ),
        ),
      ),
      'academicDegree' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/academicDegree[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\AcademicDegreeController',
            'action' => 'index',
          ),
        ),
      ),
      'academicUniversity' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/academicUniversity[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\AcademicUniversityController',
            'action' => 'index',
          ),
        ),
      ),
      'academicProgram' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/academicProgram[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\AcademicProgramController',
            'action' => 'index',
          ),
        ),
      ),
      'academicCourse' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/academicCourse[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\AcademicCourseController',
            'action' => 'index',
          ),
        ),
      ),
      'training' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/training[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\TrainingController',
            'action' => 'index',
          ),
        ),
      ),
      'loanAdvance' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/setup/loanAdvance[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Setup\\Controller\\LoanAdvanceController',
            'action' => 'index',
          ),
        ),
      ),
      'leavesetup' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/leave/leavesetup[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'LeaveManagement\\Controller\\LeaveSetup',
            'action' => 'index',
          ),
        ),
      ),
      'leaveassign' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/leave/leaveassign[/:action[/:eid[/:id]]]',
          'defaults' => 
          array (
            'controller' => 'LeaveManagement\\Controller\\leaveAssign',
            'action' => 'index',
          ),
        ),
      ),
      'leaveapply' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/leave/leaveapply[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'LeaveManagement\\Controller\\LeaveApply',
            'action' => 'index',
          ),
        ),
      ),
      'leavestatus' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/leave/leavestatus[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'LeaveManagement\\Controller\\LeaveStatus',
            'action' => 'index',
          ),
        ),
      ),
      'leavebalance' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/leave/leavebalance[/:action[/:eid[/:id]]]',
          'defaults' => 
          array (
            'controller' => 'LeaveManagement\\Controller\\LeaveBalance',
            'action' => 'index',
          ),
        ),
      ),
      'holidaysetup' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/holiday/holidaysetup[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'HolidayManagement\\Controller\\HolidaySetup',
            'action' => 'list',
          ),
        ),
      ),
      'shiftassign' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/attendance/shiftassign[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'AttendanceManagement\\Controller\\ShiftAssign',
            'action' => 'index',
          ),
        ),
      ),
      'attendancebyhr' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/attendance/attendancebyhr[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'AttendanceManagement\\Controller\\AttendanceByHr',
            'action' => 'index',
          ),
        ),
      ),
      'shiftsetup' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/attendance/shiftsetup[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'AttendanceManagement\\Controller\\ShiftSetup',
            'action' => 'index',
          ),
        ),
      ),
      'attendancestatus' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/attendance/attendancestatus[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'AttendanceManagement\\Controller\\AttendanceStatus',
            'action' => 'index',
          ),
        ),
      ),
      'dailyAttendance' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/attendance/dailyAttendance[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'AttendanceManagement\\Controller\\DailyAttendance',
            'action' => 'index',
          ),
        ),
      ),
      'myattendance' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/myattendance[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\Controller\\MyAttendance',
            'action' => 'index',
          ),
        ),
      ),
      'holiday' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/holiday[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\Controller\\Holiday',
            'action' => 'index',
          ),
        ),
      ),
      'leave' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/leave[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\Controller\\Leave',
            'action' => 'index',
          ),
        ),
      ),
      'leaverequest' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/leaverequest[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\Controller\\LeaveRequest',
            'action' => 'index',
          ),
        ),
      ),
      'attendancerequest' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/attendancerequest[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\Controller\\AttendanceRequest',
            'action' => 'index',
          ),
        ),
      ),
      'service' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/service[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\Controller\\Service',
            'action' => 'index',
          ),
        ),
      ),
      'profile' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/profile[/:action[/:tab]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\Controller\\Profile',
            'action' => 'index',
          ),
        ),
      ),
      'payslip' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/payslip[/:action]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\Controller\\PaySlip',
            'action' => 'index',
          ),
        ),
      ),
      'loanAdvanceRequest' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/loanAdvanceRequest[/:action]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'SelfService\\LoanAdvanceRequest',
            'action' => 'index',
          ),
        ),
      ),
      'restful' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/restful',
          'defaults' => 
          array (
            'controller' => 'RestfulService\\Controller\\RestfulService',
            'action' => 'index',
          ),
        ),
      ),
      'monthlyValue' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/payroll/monthlyValue[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'Payroll\\Controller\\MonthlyValue',
            'action' => 'index',
          ),
        ),
      ),
      'flatValue' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/payroll/flatValue[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'Payroll\\Controller\\FlatValue',
            'action' => 'index',
          ),
        ),
      ),
      'rules' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/payroll/rules[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'Payroll\\Controller\\Rules',
            'action' => 'index',
          ),
        ),
      ),
      'generate' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/payroll/generate[/:action[/:id]]',
          'defaults' => 
          array (
            'controller' => 'Payroll\\Controller\\Generate',
            'action' => 'index',
          ),
        ),
      ),
      'leaveapprove' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/managerservice/leaveapprove[/:action[/:id][/:role]]',
          'defaults' => 
          array (
            'controller' => 'ManagerService\\Controller\\LeaveApproveController',
            'action' => 'index',
          ),
        ),
      ),
      'attedanceapprove' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/managerservice/attendanceapprove[/:action[/:id][/:role]]',
          'defaults' => 
          array (
            'controller' => 'ManagerService\\Controller\\AttendanceApproveController',
            'action' => 'index',
          ),
        ),
      ),
      'rolesetup' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/system/rolesetup[/:action[/:id][/:role]]',
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'System\\Controller\\RoleSetupController',
            'action' => 'index',
          ),
        ),
      ),
      'usersetup' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/system/usersetup[/:action[/:id][/:role]]',
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'System\\Controller\\UserSetupController',
            'action' => 'index',
          ),
        ),
      ),
      'menusetup' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/system/menusetup[/:action[/:id][/:role]]',
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'System\\Controller\\MenuSetupController',
            'action' => 'index',
          ),
        ),
      ),
      'dashboardsetup' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/system/dashboard[/:action[/:id]]',
          'constraint' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'System\\Controller\\DashboardController',
            'action' => 'index',
          ),
        ),
      ),
      'trainingAssign' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/training/trainingAssign[/:action[/:id]]',
          'constraints' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Training\\Controller\\TrainingAssignController',
            'action' => 'index',
          ),
        ),
      ),
      'appraisal-setup' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/appraisal[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Appraisal\\Controller\\Appraisal',
            'action' => 'index',
          ),
        ),
      ),
      'appraisal-evaluation-review' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/managerservice/appraisal[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Appraisal\\Controller\\EvaluationAndReview',
            'action' => 'index',
          ),
        ),
      ),
      'performance-appraisal' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/selfservice/performanceappraisal[/:action[/:id]]',
          'constants' => 
          array (
            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            'id' => '[0-9]+',
          ),
          'defaults' => 
          array (
            'controller' => 'Appraisal\\Controller\\PerformanceAppraisal',
            'action' => 'index',
          ),
        ),
      ),
    ),
  ),
  'console' => 
  array (
    'router' => 
    array (
      'routes' => 
      array (
        'user-reset-password' => 
        array (
          'options' => 
          array (
            'route' => 'attendance daily-attendance',
            'defaults' => 
            array (
              'controller' => 'Application\\Controller\\CronController',
              'action' => 'index',
            ),
          ),
        ),
        'test' => 
        array (
          'options' => 
          array (
            'route' => 'test',
            'defaults' => 
            array (
              'controller' => 'Application\\Controller\\CronController',
              'action' => 'test',
            ),
          ),
        ),
        'check-update' => 
        array (
          'options' => 
          array (
            'route' => 'attendance employee-attendance <employeeId> <attendanceDt> <attendanceTime>',
            'defaults' => 
            array (
              'controller' => 'Application\\Controller\\CronController',
              'action' => 'employeeAttendance',
            ),
          ),
        ),
      ),
    ),
  ),
  'navigation' => 
  array (
    'navigation-example' => 
    array (
      0 => 
      array (
        'label' => 'Google',
        'uri' => 'https://www.google.com',
        'target' => '_blank',
      ),
      1 => 
      array (
        'label' => 'Home',
        'route' => 'leavesetup',
      ),
      2 => 
      array (
        'label' => 'Modules',
        'uri' => '#',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'LearnZF2Ajax',
            'route' => 'leavesetup',
          ),
          1 => 
          array (
            'label' => 'LearnZF2FormUsage',
            'route' => 'leavesetup',
          ),
          2 => 
          array (
            'label' => 'LearnZF2Barcode',
            'route' => 'leavesetup',
          ),
          3 => 
          array (
            'label' => 'LearnZF2Pagination',
            'route' => 'leavesetup',
          ),
          4 => 
          array (
            'label' => 'LearnZF2Log',
            'route' => 'leavesetup',
          ),
        ),
      ),
    ),
    'employee' => 
    array (
      0 => 
      array (
        'label' => 'Employee',
        'route' => 'employee',
      ),
      1 => 
      array (
        'label' => 'Employee',
        'route' => 'employee',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'employee',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'employee',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'employee',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'designation' => 
    array (
      0 => 
      array (
        'label' => 'Designation',
        'route' => 'designation',
      ),
      1 => 
      array (
        'label' => 'Designation',
        'route' => 'designation',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'designation',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'designation',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'designation',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'company' => 
    array (
      0 => 
      array (
        'label' => 'Company',
        'route' => 'company',
      ),
      1 => 
      array (
        'label' => 'Company',
        'route' => 'company',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'company',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'company',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'company',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'branch' => 
    array (
      0 => 
      array (
        'label' => 'Branch',
        'route' => 'branch',
      ),
      1 => 
      array (
        'label' => 'Branch',
        'route' => 'branch',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'branch',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'branch',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'branch',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'department' => 
    array (
      0 => 
      array (
        'label' => 'Department',
        'route' => 'department',
      ),
      1 => 
      array (
        'label' => 'Department',
        'route' => 'department',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'department',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'department',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'department',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'position' => 
    array (
      0 => 
      array (
        'label' => 'Position',
        'route' => 'position',
      ),
      1 => 
      array (
        'label' => 'Position',
        'route' => 'position',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'position',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'position',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'position',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'serviceType' => 
    array (
      0 => 
      array (
        'label' => 'Service Type',
        'route' => 'serviceType',
      ),
      1 => 
      array (
        'label' => 'Service Type',
        'route' => 'serviceType',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'serviceType',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'serviceType',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'serviceType',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'empCurrentPosting' => 
    array (
      0 => 
      array (
        'label' => 'Employee Current Posting',
        'route' => 'empCurrentPosting',
      ),
      1 => 
      array (
        'label' => 'Employee Current Posting',
        'route' => 'empCurrentPosting',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'empCurrentPosting',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'empCurrentPosting',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'empCurrentPosting',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'jobHistory' => 
    array (
      0 => 
      array (
        'label' => 'Service Status Update',
        'route' => 'jobHistory',
      ),
      1 => 
      array (
        'label' => 'Service Status Update',
        'route' => 'jobHistory',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'jobHistory',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'jobHistory',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'jobHistory',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'recommendapprove' => 
    array (
      0 => 
      array (
        'label' => 'Reporting Hierarchy',
        'route' => 'recommendapprove',
      ),
      1 => 
      array (
        'label' => 'Reporting Hierarchy',
        'route' => 'recommendapprove',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'recommendapprove',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'recommendapprove',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'recommendapprove',
            'action' => 'edit',
          ),
          3 => 
          array (
            'label' => 'Group Assign',
            'route' => 'recommendapprove',
            'action' => 'groupAssign',
          ),
        ),
      ),
    ),
    'serviceEventType' => 
    array (
      0 => 
      array (
        'label' => 'Service Event Type',
        'route' => 'serviceEventType',
      ),
      1 => 
      array (
        'label' => 'Service Event Type',
        'route' => 'serviceEventType',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'serviceEventType',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'serviceEventType',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'serviceEventType',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'academicDegree' => 
    array (
      0 => 
      array (
        'label' => 'Academic Degree',
        'route' => 'academicDegree',
      ),
      1 => 
      array (
        'label' => 'Academic Degree',
        'route' => 'academicDegree',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'academicDegree',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'academicDegree',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'academicDegree',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'academicUniversity' => 
    array (
      0 => 
      array (
        'label' => 'Academic University',
        'route' => 'academicUniversity',
      ),
      1 => 
      array (
        'label' => 'Academic University',
        'route' => 'academicUniversity',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'academicUniversity',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'academicUniversity',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'academicUniversity',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'academicProgram' => 
    array (
      0 => 
      array (
        'label' => 'Academic Program',
        'route' => 'academicProgram',
      ),
      1 => 
      array (
        'label' => 'Academic Program',
        'route' => 'academicProgram',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'academicProgram',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'academicProgram',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'academicProgram',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'academicCourse' => 
    array (
      0 => 
      array (
        'label' => 'Academic Course',
        'route' => 'academicCourse',
      ),
      1 => 
      array (
        'label' => 'Academic Course',
        'route' => 'academicCourse',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'academicCourse',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'academicCourse',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'academicCourse',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'training' => 
    array (
      0 => 
      array (
        'label' => 'Training',
        'route' => 'training',
      ),
      1 => 
      array (
        'label' => 'Training',
        'route' => 'training',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'training',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'training',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'training',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'loanAdvance' => 
    array (
      0 => 
      array (
        'label' => 'Loan And Advance',
        'route' => 'loanAdvance',
      ),
      1 => 
      array (
        'label' => 'Loan And Advance',
        'route' => 'loanAdvance',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'loanAdvance',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'loanAdvance',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'loanAdvance',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'leavesetup' => 
    array (
      0 => 
      array (
        'label' => 'Leave Setup',
        'route' => 'leavesetup',
      ),
      1 => 
      array (
        'label' => 'Leave Setup',
        'route' => 'leavesetup',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'leavesetup',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'leavesetup',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'leavesetup',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'leaveassign' => 
    array (
      0 => 
      array (
        'label' => 'Leave Assign',
        'route' => 'leaveassign',
      ),
      1 => 
      array (
        'label' => 'Leave Assign',
        'route' => 'leaveassign',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'leaveassign',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'leaveassign',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Assign',
            'route' => 'leaveassign',
            'action' => 'assign',
          ),
          3 => 
          array (
            'label' => 'Edit',
            'route' => 'leaveassign',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'leaveapply' => 
    array (
      0 => 
      array (
        'label' => 'Leave Apply',
        'route' => 'leaveapply',
      ),
      1 => 
      array (
        'label' => 'Leave Apply',
        'route' => 'leaveapply',
        'pageleaverequests' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'leaveapply',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'leaveapply',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'leaveapply',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'leavestatus' => 
    array (
      0 => 
      array (
        'label' => 'Leave Request Status',
        'route' => 'leavestatus',
      ),
      1 => 
      array (
        'label' => 'Leave Request Status',
        'route' => 'leavestatus',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'leavestatus',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'leavestatus',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Detail',
            'route' => 'leavestatus',
            'action' => 'view',
          ),
        ),
      ),
    ),
    'leavebalance' => 
    array (
      0 => 
      array (
        'label' => 'Leave Balance',
        'route' => 'leavebalance',
      ),
      1 => 
      array (
        'label' => 'Leave Balance',
        'route' => 'leavebalance',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'leavebalance',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Leave Apply',
            'route' => 'leavebalance',
            'action' => 'apply',
          ),
          2 => 
          array (
            'label' => 'Detail',
            'route' => 'leavebalance',
            'action' => 'view',
          ),
        ),
      ),
    ),
    'holidaysetup' => 
    array (
      0 => 
      array (
        'label' => 'Holiday',
        'route' => 'holidaysetup',
      ),
      1 => 
      array (
        'label' => 'Holiday',
        'route' => 'holidaysetup',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Detail',
            'route' => 'holidaysetup',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'holidaysetup',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'List',
            'route' => 'holidaysetup',
            'action' => 'list',
          ),
          3 => 
          array (
            'label' => 'Edit',
            'route' => 'holidaysetup',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'shiftsetup' => 
    array (
      0 => 
      array (
        'label' => 'Shift',
        'route' => 'shiftsetup',
      ),
      1 => 
      array (
        'label' => 'Shift',
        'route' => 'shiftsetup',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'shiftsetup',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'shiftsetup',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'shiftsetup',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'attendancebyhr' => 
    array (
      0 => 
      array (
        'label' => 'Attendance',
        'route' => 'attendancebyhr',
      ),
      1 => 
      array (
        'label' => 'Attendance',
        'route' => 'attendancebyhr',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'attendancebyhr',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Entry',
            'route' => 'attendancebyhr',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'attendancebyhr',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'shiftassign' => 
    array (
      0 => 
      array (
        'label' => 'Shift Assign',
        'route' => 'shiftassign',
      ),
      1 => 
      array (
        'label' => 'Shift Assign',
        'route' => 'shiftassign',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'shiftassign',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'shiftassign',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'shiftassign',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'attendancestatus' => 
    array (
      0 => 
      array (
        'label' => 'Attendance Request Status',
        'route' => 'attendancestatus',
      ),
      1 => 
      array (
        'label' => 'Attendance Request Status',
        'route' => 'attendancestatus',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'attendancestatus',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'attendancestatus',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Detail',
            'route' => 'attendancestatus',
            'action' => 'view',
          ),
        ),
      ),
    ),
    'myattendance' => 
    array (
      0 => 
      array (
        'label' => 'Attendance',
        'route' => 'myattendance',
      ),
      1 => 
      array (
        'label' => 'Attendance',
        'route' => 'myattendance',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'myattendance',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Entry',
            'route' => 'myattendance',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'myattendance',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'holiday' => 
    array (
      0 => 
      array (
        'label' => 'Holiday',
        'route' => 'holiday',
      ),
      1 => 
      array (
        'label' => 'Holiday',
        'route' => 'holiday',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'holiday',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'holiday',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'holiday',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'leave' => 
    array (
      0 => 
      array (
        'label' => 'Leave',
        'route' => 'leave',
      ),
      1 => 
      array (
        'label' => 'Leave',
        'route' => 'leave',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'leave',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'leave',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'leave',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'leaverequest' => 
    array (
      0 => 
      array (
        'label' => 'Leave Request',
        'route' => 'leaverequest',
      ),
      1 => 
      array (
        'label' => 'Leave Request',
        'route' => 'leaverequest',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'leaverequest',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'leaverequest',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'leaverequest',
            'action' => 'edit',
          ),
          3 => 
          array (
            'label' => 'Detail',
            'route' => 'leaverequest',
            'action' => 'view',
          ),
        ),
      ),
    ),
    'attendancerequest' => 
    array (
      0 => 
      array (
        'label' => 'Attendance Request',
        'route' => 'attendancerequest',
      ),
      1 => 
      array (
        'label' => 'Attendance Request',
        'route' => 'attendancerequest',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'attendancerequest',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'attendancerequest',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'attendancerequest',
            'action' => 'edit',
          ),
          3 => 
          array (
            'label' => 'Detail',
            'route' => 'attendancerequest',
            'action' => 'view',
          ),
        ),
      ),
    ),
    'service' => 
    array (
      0 => 
      array (
        'label' => 'Service',
        'route' => 'service',
      ),
      1 => 
      array (
        'label' => 'Service',
        'route' => 'service',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'History',
            'route' => 'service',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Detail',
            'route' => 'service',
            'action' => 'view',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'service',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'profile' => 
    array (
      0 => 
      array (
        'label' => 'Profile',
        'route' => 'profile',
      ),
      1 => 
      array (
        'label' => 'Profile',
        'route' => 'profile',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Detail',
            'route' => 'profile',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'profile',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'profile',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'payslip' => 
    array (
      0 => 
      array (
        'label' => 'PaySlip',
        'route' => 'payslip',
      ),
      1 => 
      array (
        'label' => 'PaySlip',
        'route' => 'payslip',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Detail',
            'route' => 'payslip',
            'action' => 'index',
          ),
        ),
      ),
    ),
    'loanAdvanceRequest' => 
    array (
      0 => 
      array (
        'label' => 'Loan/Advance Request',
        'route' => 'loanAdvanceRequest',
      ),
      1 => 
      array (
        'label' => 'Loan/Advance Request',
        'route' => 'loanAdvanceRequest',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Detail',
            'route' => 'loanAdvanceRequest',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'loanAdvanceRequest',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'loanAdvanceRequest',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'monthlyValue' => 
    array (
      0 => 
      array (
        'label' => 'Monthly Value',
        'route' => 'monthlyValue',
      ),
      1 => 
      array (
        'label' => 'Monthly Value',
        'route' => 'monthlyValue',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'monthlyValue',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'monthlyValue',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'monthlyValue',
            'action' => 'edit',
          ),
          3 => 
          array (
            'label' => 'Detail',
            'route' => 'monthlyValue',
            'action' => 'detail',
          ),
        ),
      ),
    ),
    'flatValue' => 
    array (
      0 => 
      array (
        'label' => 'Flat Value',
        'route' => 'flatValue',
      ),
      1 => 
      array (
        'label' => 'Flat Value',
        'route' => 'flatValue',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'flatValue',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'flatValue',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'flatValue',
            'action' => 'edit',
          ),
          3 => 
          array (
            'label' => 'Detail',
            'route' => 'flatValue',
            'action' => 'detail',
          ),
        ),
      ),
    ),
    'rules' => 
    array (
      0 => 
      array (
        'label' => 'Rules',
        'route' => 'rules',
      ),
      1 => 
      array (
        'label' => 'Rules',
        'route' => 'rules',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'rules',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'rules',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'rules',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'generate' => 
    array (
      0 => 
      array (
        'label' => 'Generate',
        'route' => 'generate',
      ),
      1 => 
      array (
        'label' => 'Generate',
        'route' => 'generate',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'generate',
            'action' => 'index',
          ),
        ),
      ),
    ),
    'leaveapprove' => 
    array (
      0 => 
      array (
        'label' => 'Leave Request',
        'route' => 'leaveapprove',
      ),
      1 => 
      array (
        'label' => 'Leave Request',
        'route' => 'leaveapprove',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'leaveapprove',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'List',
            'route' => 'leaveapprove',
            'action' => 'status',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'leaveapprove',
            'action' => 'edit',
          ),
          3 => 
          array (
            'label' => 'View',
            'route' => 'leaveapprove',
            'action' => 'view',
          ),
        ),
      ),
    ),
    'attedanceapprove' => 
    array (
      0 => 
      array (
        'label' => 'Attendance Request',
        'route' => 'attedanceapprove',
      ),
      1 => 
      array (
        'label' => 'Attendance Request',
        'route' => 'attedanceapprove',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'attedanceapprove',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'List',
            'route' => 'attedanceapprove',
            'action' => 'status',
          ),
          2 => 
          array (
            'label' => 'View',
            'route' => 'attedanceapprove',
            'action' => 'view',
          ),
        ),
      ),
    ),
    'rolesetup' => 
    array (
      0 => 
      array (
        'label' => 'Role Setup',
        'route' => 'rolesetup',
      ),
      1 => 
      array (
        'label' => 'Role Setup',
        'route' => 'rolesetup',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'rolesetup',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'rolesetup',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'rolesetup',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'usersetup' => 
    array (
      0 => 
      array (
        'label' => 'User Setup',
        'route' => 'usersetup',
      ),
      1 => 
      array (
        'label' => 'User Setup',
        'route' => 'usersetup',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'usersetup',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'usersetup',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'usersetup',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'menusetup' => 
    array (
      0 => 
      array (
        'label' => 'Menu Setup',
        'route' => 'menusetup',
      ),
      1 => 
      array (
        'label' => 'Menu Setup',
        'route' => 'menusetup',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'menusetup',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'menusetup',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'menusetup',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'dashboardsetup' => 
    array (
      0 => 
      array (
        'label' => 'Dashboard Setup',
        'route' => 'dashboardsetup',
      ),
      1 => 
      array (
        'label' => 'Dashboard Setup',
        'route' => 'dashboardsetup',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Assign Dashboard',
            'route' => 'dashboardsetup',
            'action' => 'index',
          ),
        ),
      ),
    ),
    'trainingAssign' => 
    array (
      0 => 
      array (
        'label' => 'Training Assign',
        'route' => 'trainingAssign',
      ),
      1 => 
      array (
        'label' => 'Training Assign',
        'route' => 'trainingAssign',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'trainingAssign',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'trainingAssign',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'trainingAssign',
            'action' => 'edit',
          ),
        ),
      ),
    ),
    'appraisal-setup' => 
    array (
      0 => 
      array (
        'label' => 'Appraisal',
        'route' => 'appraisal-setup',
      ),
      1 => 
      array (
        'label' => 'Appraisal',
        'route' => 'appraisal-setup',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'appraisal-setup',
            'action' => 'index',
          ),
          1 => 
          array (
            'label' => 'Add',
            'route' => 'appraisal-setup',
            'action' => 'add',
          ),
          2 => 
          array (
            'label' => 'Edit',
            'route' => 'appraisal-setup',
            'action' => 'edit',
          ),
          3 => 
          array (
            'label' => 'Review',
            'route' => 'appraisal-setup',
            'action' => 'review',
          ),
        ),
      ),
    ),
    'appraisal-evaluation-review' => 
    array (
      0 => 
      array (
        'label' => 'Appraisal',
        'route' => 'appraisal-evaluation-review',
      ),
      1 => 
      array (
        'label' => 'Appraisal',
        'route' => 'appraisal-evaluation-review',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'Evaluation',
            'route' => 'appraisal-evaluation-review',
            'action' => 'evaluation',
          ),
          1 => 
          array (
            'label' => 'Review',
            'route' => 'appraisal-evaluation-review',
            'action' => 'review',
          ),
        ),
      ),
    ),
    'performance-appraisal' => 
    array (
      0 => 
      array (
        'label' => 'Performance Appraisal',
        'route' => 'performance-appraisal',
      ),
      1 => 
      array (
        'label' => 'Performance Appraisal',
        'route' => 'performance-appraisal',
        'pages' => 
        array (
          0 => 
          array (
            'label' => 'List',
            'route' => 'performance-appraisal',
            'action' => 'index',
          ),
        ),
      ),
    ),
  ),
  'controllers' => 
  array (
    'factories' => 
    array (
      'Application\\Controller\\IndexController' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Application\\Controller\\DashboardController' => 'Application\\Factory\\DashBoardFactory',
      'Application\\Controller\\CronController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\EmployeeController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\DesignationController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\CompanyController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\BranchController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\DepartmentController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\PositionController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\ServiceTypeController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\EmpCurrentPostingController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\JobHistoryController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\WebServiceController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\RecommendApproveController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\ServiceEventTypeController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\AcademicDegreeController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\AcademicUniversityController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\AcademicProgramController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\AcademicCourseController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\TrainingController' => 'Application\\Controller\\ControllerFactory',
      'Setup\\Controller\\LoanAdvanceController' => 'Application\\Controller\\ControllerFactory',
      'LeaveManagement\\Controller\\LeaveSetup' => 'Application\\Controller\\ControllerFactory',
      'LeaveManagement\\Controller\\leaveAssign' => 'Application\\Controller\\ControllerFactory',
      'LeaveManagement\\Controller\\LeaveApply' => 'Application\\Controller\\ControllerFactory',
      'LeaveManagement\\Controller\\LeaveStatus' => 'Application\\Controller\\ControllerFactory',
      'LeaveManagement\\Controller\\LeaveBalance' => 'Application\\Controller\\ControllerFactory',
      'HolidayManagement\\Controller\\HolidaySetup' => 'Application\\Controller\\ControllerFactory',
      'AttendanceManagement\\Controller\\ShiftAssign' => 'Application\\Controller\\ControllerFactory',
      'AttendanceManagement\\Controller\\AttendanceByHr' => 'Application\\Controller\\ControllerFactory',
      'AttendanceManagement\\Controller\\ShiftSetup' => 'Application\\Controller\\ControllerFactory',
      'AttendanceManagement\\Controller\\AttendanceStatus' => 'Application\\Controller\\ControllerFactory',
      'AttendanceManagement\\Controller\\DailyAttendance' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\Controller\\MyAttendance' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\Controller\\Holiday' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\Controller\\Leave' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\Controller\\LeaveRequest' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\Controller\\AttendanceRequest' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\Controller\\Profile' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\Controller\\Service' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\Controller\\PaySlip' => 'Application\\Controller\\ControllerFactory',
      'SelfService\\LoanAdvanceRequest' => 'Application\\Controller\\ControllerFactory',
      'RestfulService\\Controller\\RestfulService' => 'Application\\Controller\\ControllerFactory',
      'Payroll\\Controller\\MonthlyValue' => 'Application\\Controller\\ControllerFactory',
      'Payroll\\Controller\\FlatValue' => 'Application\\Controller\\ControllerFactory',
      'Payroll\\Controller\\Rules' => 'Application\\Controller\\ControllerFactory',
      'Payroll\\Controller\\Generate' => 'Application\\Controller\\ControllerFactory',
      'ManagerService\\Controller\\LeaveApproveController' => 'Application\\Controller\\ControllerFactory',
      'ManagerService\\Controller\\AttendanceApproveController' => 'Application\\Controller\\ControllerFactory',
      'System\\Controller\\RoleSetupController' => 'Application\\Controller\\ControllerFactory',
      'System\\Controller\\UserSetupController' => 'Application\\Controller\\ControllerFactory',
      'System\\Controller\\MenuSetupController' => 'Application\\Controller\\ControllerFactory',
      'System\\Controller\\DashboardController' => 'Application\\Factory\\DashBoardFactory',
      'Training\\Controller\\TrainingAssignController' => 'Application\\Controller\\ControllerFactory',
      'Appraisal\\Controller\\Appraisal' => 'Application\\Controller\\ControllerFactory',
      'Appraisal\\Controller\\EvaluationAndReview' => 'Application\\Controller\\ControllerFactory',
      'Appraisal\\Controller\\PerformanceAppraisal' => 'Application\\Controller\\ControllerFactory',
    ),
  ),
  'view_manager' => 
  array (
    'display_not_found_reason' => true,
    'display_exceptions' => true,
    'doctype' => 'HTML5',
    'not_found_template' => 'error/404',
    'exception_template' => 'error/index',
    'template_map' => 
    array (
      'layout/layout' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/layout.phtml',
      'layout/login' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/login.phtml',
      'layout/json' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/json.phtml',
      'application/index/index' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/application/index/index.phtml',
      'error/404' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/error/404.phtml',
      'error/index' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/error/index.phtml',
      'error/no_access' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/error/no_access.phtml',
      'partial/header' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/partials/header.phtml',
      'partial/footer' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/partials/footer.phtml',
      'partial/sidebar' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/partials/sidebar.phtml',
      'partial/breadcrumb' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/partials/breadcrumb.phtml',
      'partial/profile' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/partials/profile.phtml',
      'dashboard-item/holiday-list' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/dashboard-items/holiday-list.phtml',
      'dashboard-item/attendance-request' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/dashboard-items/attendance-request.phtml',
      'dashboard-item/leave-apply' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/dashboard-items/leave-apply.phtml',
      'dashboard-item/present-absent' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/dashboard-items/present-absent.phtml',
      'dashboard-item/employee-count-by-branch' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/dashboard-items/employee-count-by-branch.phtml',
      'dashboard-item/today-leave' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/dashboard-items/today-leave.phtml',
      'dashboard-item/birthdays' => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view/layout/dashboard-items/birthdays.phtml',
    ),
    'template_path_stack' => 
    array (
      0 => '/var/www/html/neo/neo-hris-metronic/module/Application/config/../view',
      1 => '/var/www/html/neo/neo-hris-metronic/module/Setup/config/../view',
      2 => '/var/www/html/neo/neo-hris-metronic/module/LeaveManagement/config/../view',
      3 => '/var/www/html/neo/neo-hris-metronic/module/HolidayManagement/config/../view',
      4 => '/var/www/html/neo/neo-hris-metronic/module/AttendanceManagement/config/../view',
      5 => '/var/www/html/neo/neo-hris-metronic/module/SelfService/config/../view',
      6 => '/var/www/html/neo/neo-hris-metronic/module/RestfulService/config/../view',
      7 => '/var/www/html/neo/neo-hris-metronic/module/Payroll/config/../view',
      8 => '/var/www/html/neo/neo-hris-metronic/module/ManagerService/config/../view',
      9 => '/var/www/html/neo/neo-hris-metronic/module/System/config/../view',
      10 => '/var/www/html/neo/neo-hris-metronic/module/Training/config/../view',
      11 => '/var/www/html/neo/neo-hris-metronic/module/Appraisal/config/../view',
    ),
  ),
  'dashboard-items' => 
  array (
    'holiday-list' => 'dashboard-item/holiday-list',
    'attendance-request' => 'dashboard-item/attendance-request',
    'leave-apply' => 'dashboard-item/leave-apply',
    'present-absent' => 'dashboard-item/present-absent',
    'emp-cnt-by-branch' => 'dashboard-item/employee-count-by-branch',
    'today-leave' => 'dashboard-item/today-leave',
    'birthdays' => 'dashboard-item/birthdays',
  ),
  'role-types' => 
  array (
    'A' => 'Admin',
    'B' => 'Branch Manager',
    'E' => 'Employee',
  ),
  'db' => 
  array (
    'driver' => 'oci8',
    'connection_string' => '(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.4.2)(PORT = 1521))
        (CONNECT_DATA =
        (SERVER = DEDICATED)
        (SERVICE_NAME = ITN)
        )
        )',
    'username' => 'HRIS',
    'password' => 'NEO_HRIS',
    'platform_options' => 
    array (
      'quote_identifiers' => false,
    ),
  ),
);