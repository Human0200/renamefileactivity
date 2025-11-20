<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arActivityDescription = array(
    "NAME" => GetMessage("RENAME_FILE_NAME"),
    "DESCRIPTION" => GetMessage("RENAME_FILE_DESC"),
    "TYPE" => "activity",
    "CLASS" => "RenameFileActivity",
    "JSCLASS" => "BizProcActivity",
    "CATEGORY" => array("ID" => "document"),
    "PROPERTIES" => array(
        "FileId" => array(
            "Name" => GetMessage("RENAME_FILE_FILE_ID"),
            "Type" => "int",
            "Required" => true,
            "Default" => ""
        ),
        "NewFileName" => array(
            "Name" => GetMessage("RENAME_FILE_NEW_NAME"),
            "Type" => "string",
            "Required" => true,
            "Default" => ""
        ),
        "DocumentNumber" => array(
            "Name" => GetMessage("RENAME_FILE_DOC_NUMBER"),
            "Type" => "string",
            "Required" => false,
            "Default" => ""
        ),
        "DocumentDate" => array(
            "Name" => GetMessage("RENAME_FILE_DOC_DATE"),
            "Type" => "string",
            "Required" => false,
            "Default" => ""
        ),
        "ContractorName" => array(
            "Name" => GetMessage("RENAME_FILE_CONTRACTOR"),
            "Type" => "string",
            "Required" => false,
            "Default" => ""
        ),
    ),
    "RETURN" => array(
        "NewFileId" => array(
            "NAME" => GetMessage("RENAME_FILE_RETURN_ID"),
            "TYPE" => "int",
        ),
        "NewFileUrl" => array(
            "NAME" => GetMessage("RENAME_FILE_RETURN_URL"),
            "TYPE" => "string",
        ),
    )
);
?>