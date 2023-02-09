/* 
 * Initialization of angular app
 */
/* global setting_enquiry_form */

var app = angular.module("wcce_enquiry_form", ['ui.sortable']);
/*
 * angular service to pass data from one controller to another
 */
app.service('wcce_enquiry_form_service', function () { 
    var formJson = [];
    if(setting_enquiry_form.form_data !== ''){
        angular.forEach(setting_enquiry_form.form_data,function (data, key){
           setting_enquiry_form.form_data[key].partial = data.partial.split('/')[(data.partial.split('/').length - 1)];
        });
        var formJson = setting_enquiry_form.form_data;
    } 
    
    return {
        getField: function () {
            return formJson;
        },
        setField: function (value) {
            formJson = value;
        }
    };
});
/*
 * angular controller for field menu
 */
app.controller('postbox_menu',['$scope', 'wcce_enquiry_form_service', function ($scope, wcce_enquiry_form_service) {
    $scope.postboxClass = "";
    var formJson = wcce_enquiry_form_service.getField();
    $scope.addFormField = function (type, label, event) {
        event.preventDefault();
        var jsonLength = formJson.length;
        switch (type) {
            case 'selectbox':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    selecttype: 'radio',
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    required: false,
                    options: [
                        {
                            value: 'option1',
                            label: 'Option 1',
                            selected: false
                        },
                        {
                            value: 'option2',
                            label: 'Option 2',
                            selected: true
                        },
                        {
                            value: 'option3',
                            label: 'Option 3',
                            selected: false
                        }
                    ],
                    cssClass: ''
                });
                break;
            case 'email':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    placeholder: '',
                    required: false,
                    /* emailValidation: false, */
                    cssClass: ''
                });
                break;
            case 'textarea':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    defaultValue: '',
                    limit : '',
                    required: false,
                    cssClass: ''
                });
                break;
            case 'checkbox':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    defaultValue: 'unchecked',
                    required: false,
                    cssClass: ''
                });
                break;
            case 'datepicker':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    dateformat: 'mm/dd/yy',
                    required: false,
                    cssClass: ''
                });
                break;
            case 'timepicker':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    twentyFour: false,
                    showSeconds: true,
                    required: false,
                    cssClass: ''
                });
                break;
            case 'captcha':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    required: false
                });
                break;
            case 'recaptcha':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    site_key: '',
                    secret_key: '',
                    required: false
                });
                break;
            case 'file':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    fileSize: '',
                    fileType: [
                        {
                            value : 'application/pdf',
                            label : 'PDF',
                            selected : false
                        },
                        {
                            value : 'image/jpeg',
                            label : 'JPEG',
                            selected : false
                        },
                        {
                            value : 'image/png',
                            label : 'PNG',
                            selected : false
                        },
                        {
                            value : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            label : 'DOC',
                            selected : false
                        },
                        {
                            value : 'application/vnd.ms-excel',
                            label : 'xls',
                            selected : false
                        }
                    ],
                    required: false,
                    muliple: false,
                    cssClass: ''
                });
                break;
            case 'separator':
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    cssClass: ''
                });
                break;
            default :
                formJson.push({
                    id: jsonLength,
                    type: type,
                    label: label,
                    hidden: false,
                    partial: type + '.html',
                    placeholder: '',
                    required: false,
                    cssClass: ''
                });
                break;
        }
        console.log(formJson);
        wcce_enquiry_form_service.setField(formJson);
    };
    $scope.togglePostbox = function () {
        if ($scope.postboxClass === "") {
            $scope.postboxClass = "closed";
        } else {
            $scope.postboxClass = "";
        }
    };
}]);
/*
 * angular controller for form fields
 */
app.controller('postbox_content',['$scope', '$http', 'wcce_enquiry_form_service', function ($scope, $http, wcce_enquiry_form_service) {
    var formJson = wcce_enquiry_form_service.getField();
    $scope.fields = formJson;
    $scope.showSaveSpinner = false;
    $scope.partial_url = setting_enquiry_form.partials;
    $scope.togglePostboxField = function (index) {
        if ($scope.fields[index].hidden) {
            $scope.fields[index].hidden = false;
        } else {
            $scope.fields[index].hidden = true;
        }
    };
    $scope.removeFormField = function (index, event) {
        event.preventDefault();
        formJson.splice(index, 1);
        wcce_enquiry_form_service.setField(formJson);
    };
    $scope.addSelectBoxOption = function (index, event) {
        event.preventDefault();
        var count = $scope.fields[index].options.length + 1;
        $scope.fields[index].options.push({value: 'option' + count, label: 'Option ' + count, selected: false});
    };
    $scope.removeSelectboxOption = function (index, key, event) {
        event.preventDefault();
        $scope.fields[index].options.splice(key, 1);
    };
    $scope.fieldSortableOptions = {
        stop: function (e, ui) {

        }
    };
    $scope.listOnchange = function (parentIndex,index){
        console.log($scope.fields[parentIndex].options);
        angular.forEach($scope.fields[parentIndex].options,function(value,key){
            if(key !== index){
                $scope.fields[parentIndex].options[key].selected = false;
            }
        });
    };
    $scope.saveFormData = function () {
        $scope.showSaveSpinner = true;
    
        var data = jQuery.param({
            action: 'wcce_enquiry_form_settings_data',
            form_data: JSON.stringify($scope.fields)
        });
        
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        $http.post(setting_enquiry_form.ajaxurl,data,config).success(function (data, status, headers, config){ 
            $scope.showSaveSpinner = false;
        }).error(function (data, status, header, config){
            console.log(data);
            $scope.showSaveSpinner = false;
        });
    };
}]);

