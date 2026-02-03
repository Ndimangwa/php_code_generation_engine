<div class="container data-container mt-2 mb-2">
    <div class="row">
        <!-- <div class="col-md-6 d-none d-md-block text-primary"><div class="mb-2 display-2 p-2" style="height: 100%; position: relative;"><span style="position: relative; top: 25%;"><i><?= $profile1->getProfileName() ?></i></span></div></div>
-->
        <div class="col-md-10 offset-md-1 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    UPLOAD FILE
                </div>
                <div class="card-body">
                    <?php
                    $conn = null;
                    $nextPage = $thispage;
                    try {
                        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
                        if (isset($_POST['submit'])) {
                            //We need to Work on these codes  -- begin
                            if (($_FILES['path']['name'] != "")) {
                                // Where the file is going to be stored
                                $target_dir = "../tmp/";
                                $file = $_FILES['path']['name'];
                                $path = pathinfo($file);
                                $filename = $path['filename'];
                                $ext = $path['extension'];
                                $temp_name = $_FILES['path']['tmp_name'];
                                $tfilename = $filename . "." . $ext;
                                $path_filename_ext = $target_dir . $tfilename;
                                echo $path_filename_ext;

                                // Check if file already exists
                                if (file_exists($path_filename_ext)) {
                                    throw new Exception("File Already Exists");
                                } else {
                                    $colArray1 = $_POST;
                                    $colArray1['path'] = $tfilename;
                                    if (! move_uploaded_file($temp_name, $path_filename_ext)) throw new Exception("Failed to Upload a file");
                                    __data__::insert($conn, "UploadedFiles", $colArray1, true, Constant::$default_select_empty_value);
                                }
                            }
                            //We need to Work on these codes -- end
                            echo UICardView::getSuccesfulReportCard("File Upload", "You have Succesful Uploaded a file", 'Congratulation');
                        } else {
                            $formToDisplay = __data__::createDataCaptureForm($nextPage, "UploadedFiles", array(
                                array('pname' => 'fileName', 'caption' => 'File Caption', 'required' => true, 'placeholder' => 'Lab Results'),
                                array('pname' => 'path', 'type' => 'file', 'caption' => 'Upload File', 'required' => true)
                            ), "Upload File", "create", $conn, 0, array(
                                'page' => $page,
                                'submit' => 1
                            ), null, null, 'file-upload', $thispage, true);
                            echo $formToDisplay;
                        }
                    } catch (Exception $e) {
                        echo __data__::showDangerAlert($e->getMessage());
                    }
                    $conn = null;
                    ?>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <i><a href="<?= $nextPage ?>" class="card-link">Back to Dashboard</a></i><br />
                        <span class="text-muted"><i>Rule: (Root)</i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>