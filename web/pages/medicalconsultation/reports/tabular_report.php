<div>
    <?php
    //Get All reports 
    $consultationQueues = MedicalDoctorConsultationQueue::getConsultationQueuesForManager($conn, $consultationQueueManager1->getManagerId());
    if (!is_null($consultationQueues) && (sizeof($consultationQueues) > 0)) {

        //Now working 
    ?>
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th>S/N</th>
                        <th>Get Reports</th>
                        <th>Status</th>
                        <th>Owner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 0;
                    foreach ($consultationQueues as $cQueue1) {
                        if (! $cQueue1->isAttended()) continue;
                        $index++;
                        //Preparing Link
                        $pdfpage = (($profile1->getBaseURL()) . "/documents/pdf/__get_document__.php");
                        $pdfpage = str_replace((DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR, $pdfpage);
                        $link1 = UIControls::getAnchorTag("Get Report", $pdfpage, array(
                            "id" => ($cQueue1->getQueueId()),
                            "dtype" => (Documents::$__MEDICAL_CONSULTATION_QUEUE)
                        ), null, null, array('target' => '_blank'));
                    ?>
                        <tr>
                            <td><?= $index ?></td>
                            <td><?= $link1 ?></td>
                            <td><?= ($cQueue1->isActive()) ? "Active" : "In-Active" ?></td>
                            <td><?= ($cQueue1->getAttendedBy()->getLoginId() == $login1->getLoginId()) ? "Me" : ($cQueue1->getAttendedBy()->getLoginName()) ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php

    } else {
        echo UICardView::getDangerReportCard("Medical Consultation", "There is no yet Consultation found for this patient");
    }
    ?>
</div>
