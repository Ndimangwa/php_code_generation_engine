(
    (
        !is_null($examinationQueue1) && ($examinationQueue1->isPendingPayment()) 
    ) ? 
    (
        array("disabled" => $controlDisabled, "group" => array("name" => "examination", "classes" => array("border", "border-primary", "bg-danger", "text-center")), "header" =>  "h4", "use-class" => "PatientExaminationQueue", "pname" => "listOfServices", "use-name" => "listOfExaminations", "type" => "label", "caption" => "YOU HAVE PENDING PAYMENT FOR EXAMINATION!!! YOU NEED TO MAKE PAYMENT")

    ) : 
    (
        array("disabled" => $controlDisabled, "use-class" => "PatientExaminationQueue", "group" => array("name" => "examination", "classes" => array("border", "border-primary")), "pname" => "listOfServices", "use-name" => "listOfExaminations", "value" => (is_null($examinationQueue1) ? null : ($examinationQueue1->getListOfServices())), "caption" => "Services (Lab/X-Ray/Ultasound)", "required" => false, "include-columns" => array("serviceName" => array("caption" => "Service Name"), "currency" => array("caption" => "Currency", "map" => "Currency.code"), "amount" => array("caption" => "Amount")), "filter" => array("category" => array((ServiceCategory::$__LABORATORY_EXAMINATION), (ServiceCategory::$__PLAIN_CONVENTION_X_RAY), (ServiceCategory::$__ULTRA_SOUND))))
    )
),



//PatientDrugQueue1
(
    (
        !is_null($patientDrugQueue1) && ($patientDrugQueue1->isPendingPayment())
    ) ? 
    (
        array("id" => "idDrugSelection", "disabled" => $controlDisabled, "use-class" => "PatientDrugManagement", "group" => array("name" => "drugs-management", "classes" => array("border", "border-primary", "bg-danger", "text-center")), "header" => "h4", "pname" => "pharmaceuticalDrug", "caption" => "YOU HAVE PENDING PAYMENT FOR DRUGS!!! YOU NEED TO MAKE PAYMENT", "type" => "label", "required" => false)
    ) : 
    (
        array("id" => "idDrugSelection", "value" => $listOfDrugs, "disabled" => $controlDisabled, "use-class" => "PatientDrugManagement", "group" => array("name" => "drugs-management", "classes" => array("border", "border-primary")), "pname" => "pharmaceuticalDrug", "caption" => "Drugs Selection", "type" => "list-object", "required" => false, "include-columns" => array("drugName" => array("caption" => "Name of Drug"), "unitOfMeasurement" => array("caption" => "Units"), "temporaryIntegerHolder" => array("caption" => "Quantity", "render-control" => array("required" => true, "value" => "1", "placeholder" => "1")), "usage" => array("caption" => "Usage", "render-control" => array("required" => true, "placeholder" => "1 * 3"))))
    )
),
