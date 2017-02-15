<?php

$this->headLink()
        ->appendStylesheet($this->basePath('telerik_kendoui/styles/kendo.common.min.css'))
        ->appendStylesheet($this->basePath('telerik_kendoui/styles/kendo.rtl.min.css'))
        ->appendStylesheet($this->basePath('telerik_kendoui/styles/kendo.default.min.css'))
        ->appendStylesheet($this->basePath('telerik_kendoui/styles/kendo.dataviz.min.css'))
        ->appendStylesheet($this->basePath('telerik_kendoui/styles/kendo.dataviz.default.min.css'));



$this->headLink()
        ->appendStylesheet($this->basePath('assets/global/plugins/nepalidate/nepali.datepicker.v2.1.min.css'));


$this->headLink()
        ->appendStylesheet($this->basePath('assets/global/plugins/datatables/datatables.min.css'))
        ->appendStylesheet($this->basePath('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css'));

$this->headLink()
        ->appendStylesheet($this->basePath('assets/global/plugins/ladda/ladda-themeless.min.css'));

$this->headLink()
        ->appendStylesheet($this->basePath('assets/global/plugins/codemirror-5.19.0/lib/codemirror.css'));

$this->headLink()
        ->appendStylesheet($this->basePath('dropzone/dropzone.min.css'));

$this->headLink()
        ->appendStylesheet($this->basePath('assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css'));
