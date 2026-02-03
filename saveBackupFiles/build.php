<?php
    require_once("init/__datafile__.php");
    require_once("schema/schemabuild.php");
    require_once("sqlEngine/sqlbuild.php");
    require_once("algorithm/csv2json.php");
    require_once("algorithm/sysbuild.php");
    /*
    Copyright: Ndimangwa Fadhili Ngoya
    Date: 13th April, 2021
    Phone: +255 787 101 808 / +255 762 357 596
    Email: ndimangwa@gmail.com 

    Smart way of building a web-based as well as mobile-based system
    */
    if (sizeof($argv) == 2 && ($argv[1] == "-h" || $argv[1] == "--help"))   {
        echo "\n********************************************************************************";
        echo "\n**********************************SYSTEM BUILD**********************************";
        echo "\n********************************************************************************";
        echo "\n**This application, will lay foundation code for a web-based as well as      -**";
        echo "\n**    -mobile-base applications                                               **";
        echo "\n**                                                                            **";
        echo "\n**Command Syntax: php build.php -i init-folder -t target-folder              -**";
        echo "\n**        -s input-schema --sq optional-sequece-number                        **";
        echo "\n**                                                                            **";
        echo "\n**init-folder : Initialization folder, use -i or --init or --init-folder      **";
        echo "\n**target-folder : Target folder, use -t or --target or --target-folder        **";
        echo "\n**input-schema : Schema in CSV, use -s or --schema or --input-schema          **";
        echo "\n**optional-sequence-number: use --sq or --sequence or --sequence-number       **";
        echo "\n**     the sequence-number will be labeled per class, default 0               **";
        die("\n********************************************************************************\n");
    }
    //Now we start business
    if (sizeof($argv) <7) die("\nError in command syntanx; Kindly run php build.php --help\n");
    $initFolder = null;
    $targetFolder = null;
    $inputSchema = null;
    $staticFolder = "static".DIRECTORY_SEPARATOR;
    $classesFolder = "classes".DIRECTORY_SEPARATOR;
    $accessForbidenFolder = "accessForbidden".DIRECTORY_SEPARATOR;
    $standardTypeFile = "standards/types/__types__.csv";
    $defaultIndexDocumentationFile = "generalToolsDir/initialIndex.php";
    $__DEFAULT_INIT_SEQ_NO = 0;
    $seqNumber = $__DEFAULT_INIT_SEQ_NO;
    //Extracting data
    for ($i=1; $i< sizeof($argv)-1; $i=$i+2)    {
        $control = $argv[$i];
        $data = $argv[$i+1];
        if (is_null($initFolder) && in_array($control, ["-i", "--init", "--init-folder"])) {
            $initFolder = $data;
        } else if (is_null($targetFolder) && in_array($control, ["-t", "--target", "--target-folder"])) {
            $targetFolder = $data;
        } else if (is_null($inputSchema) && in_array($control, ["-s", "--schema", "--input-schema"]))   {
            $inputSchema = $data;
        } else if ($seqNumber == $__DEFAULT_INIT_SEQ_NO && in_array($control, ["--sq", "--sequence", "--sequence-number"])) {
            $seqNumber = intval($data);
        }
    }
    //Checking if everything was okay
    if (is_null($initFolder)) die("\nError, Initialization Folder was not specified\n");
    if (is_null($targetFolder)) die("\nError, Target Folder was not specified\n");
    if (is_null($inputSchema)) die("\nError, Inpur Shema was not specified\n");
    try {
        //$realpath = realpath(".");
        //$webFolder = join(DIRECTORY_SEPARATOR, [$realpath, "web"]);
        $jsonContent = CSV2JSON::transform($inputSchema, $standardTypeFile);
        SYSBuild::build($initFolder, $targetFolder, $inputSchema, $staticFolder, $classesFolder, $jsonContent, $defaultIndexDocumentationFile, $accessForbidenFolder, $seqNumber);
    } catch (Exception $e)  {
        die($e->getMessage());
    }
?>