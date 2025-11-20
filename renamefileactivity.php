<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\Loader::includeModule('main');

class CBPRenameFileActivity extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array(
            "Title" => "",
            "FileId" => "",
            "NewFileName" => "",
            "DocumentNumber" => "",
            "DocumentDate" => "",
            "ContractorName" => "",
            "NewFileId" => "",
            "NewFileUrl" => "",
            "Extension" => "",
        );
    }

    public function Execute()
    {
        $this->NewFileId = "";
        $this->NewFileUrl = "";
        
        // Получаем ID файла
        $fileId = (int)$this->FileId;
        
        if (empty($fileId)) {
            $this->WriteToTrackingService("Не указан ID файла");
            return CBPActivityExecutionStatus::Closed;
        }

        try {
            // Получаем информацию о файле
            $file = \CFile::GetByID($fileId)->Fetch();
            
            if (!$file) {
                $this->WriteToTrackingService("Файл с ID {$fileId} не найден");
                return CBPActivityExecutionStatus::Closed;
            }

            // Формируем новое имя файла
            $newFileName = $this->GenerateFileName($file);
            
            if (empty($newFileName)) {
                $this->WriteToTrackingService("Не удалось сформировать имя файла");
                return CBPActivityExecutionStatus::Closed;
            }

            // Переименовываем файл
            $result = $this->RenameFile($fileId, $file, $newFileName);
            
            if ($result) {
                $this->WriteToTrackingService("Файл успешно переименован: {$newFileName}");
                $this->NewFileId = $result['ID'];
                $this->NewFileUrl = $result['URL'];
                $this->WriteToTrackingService("ID нового файла: {$this->NewFileId}");
                $this->WriteToTrackingService("URL нового файла: {$this->NewFileUrl}");
            } else {
                $this->WriteToTrackingService("Ошибка при переименовании файла");
            }

        } catch (Exception $e) {
            $this->WriteToTrackingService("Ошибка: " . $e->getMessage());
        }

        return CBPActivityExecutionStatus::Closed;
    }

    /**
     * Генерация нового имени файла на основе параметров
     */
    private function GenerateFileName($file)
    {
        // Получаем оригинальное расширение файла
       // $extension = pathinfo($file['ORIGINAL_NAME'], PATHINFO_EXTENSION);
        $extension = trim($this->Extension);
        
        // Базовое имя из параметра
        $newName = trim($this->NewFileName);
        
        // Массив для частей имени
        $nameParts = [];
        
        if (!empty($newName)) {
            $nameParts[] = $newName;
        }
        
        // Добавляем номер документа, если указан
        if (!empty($this->DocumentNumber)) {
            $nameParts[] = "№" . trim($this->DocumentNumber);
        }
        
        // Добавляем дату документа, если указана
        if (!empty($this->DocumentDate)) {
            $date = trim($this->DocumentDate);
            // Пытаемся привести дату к формату ДД.ММ.ГГГГ
            if (strtotime($date)) {
                $date = date('d.m.Y', strtotime($date));
            }
            $nameParts[] = "от " . $date;
        }
        
        // Добавляем контрагента, если указан
        if (!empty($this->ContractorName)) {
            $contractor = trim($this->ContractorName);
            // Очищаем имя от недопустимых символов
            $contractor = preg_replace('/[<>:\"\/\\\\|?*]/', '', $contractor);
            $nameParts[] = $contractor;
        }
        
        // Собираем имя файла
        if (empty($nameParts)) {
            // Если ничего не задано, используем оригинальное имя
            return $file['ORIGINAL_NAME'];
        }
        
        $finalName = implode(' ', $nameParts);
        
        // Очищаем имя от недопустимых символов
        $finalName = preg_replace('/[<>:\"\/\\\\|?*]/', '', $finalName);
        
        // Добавляем расширение
        if (!empty($extension)) {
            $finalName .= '.' . $extension;
        }
        
        return $finalName;
    }

    /**
     * Переименование файла
     */
    private function RenameFile($fileId, $file, $newFileName)
    {
        try {
            // Получаем содержимое файла
            $filePath = $_SERVER["DOCUMENT_ROOT"] . $file['SRC'];
            
            if (!file_exists($filePath)) {
                $this->WriteToTrackingService("Физический файл не найден: {$filePath}");
                return false;
            }

            // Читаем содержимое файла
            $fileContent = file_get_contents($filePath);
            
            if ($fileContent === false) {
                $this->WriteToTrackingService("Не удалось прочитать файл");
                return false;
            }

            // Создаем массив для CFile::SaveFile с новым именем
            $arFile = [
                'name' => $newFileName,
                'size' => filesize($filePath),
                'tmp_name' => $filePath,
                'type' => $file['CONTENT_TYPE'],
                'MODULE_ID' => $file['MODULE_ID'] ?? 'bizproc',
            ];

            // Сохраняем файл с новым именем
            $newFileId = \CFile::SaveFile($arFile, $file['MODULE_ID'] ?? 'bizproc');
            
            if ($newFileId) {
                $this->WriteToTrackingService("Файл успешно пересохранен с новым именем. Новый ID: {$newFileId}");
                
                // Удаляем старый файл
                \CFile::Delete($fileId);
                
                return [
                    'ID' => $newFileId,
                    'URL' => \CFile::GetPath($newFileId),
                    'NAME' => $newFileName
                ];
            }

            return false;
            
        } catch (Exception $e) {
            $this->WriteToTrackingService("Исключение при переименовании: " . $e->getMessage());
            return false;
        }
    }

    public static function ValidateProperties($arTestProperties = array(), CBPWorkflowTemplateUser $user = null)
    {
        $errors = array();

        if (!isset($arTestProperties["FileId"]) || empty($arTestProperties["FileId"])) {
            $errors[] = array(
                "code" => "Empty", 
                "parameter" => "FileId", 
                "message" => "File ID is required"
            );
        }

        if (!isset($arTestProperties["NewFileName"]) || trim($arTestProperties["NewFileName"]) === "") {
            $errors[] = array(
                "code" => "Empty", 
                "parameter" => "NewFileName", 
                "message" => "New file name is required"
            );
        }

        return array_merge($errors, parent::ValidateProperties($arTestProperties, $user));
    }

    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues, $formName = "")
    {
        $runtime = CBPRuntime::GetRuntime();

        $arMap = [
            'FileId' => 'fileid',
            'NewFileName' => 'newfilename',
            'DocumentNumber' => 'documentnumber',
            'DocumentDate' => 'documentdate',
            'ContractorName' => 'contractorname',
            'Extension' => 'extension',
        ];

        if (!is_array($arCurrentValues)) {
            $arCurrentValues = [];
            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
            if (is_array($arCurrentActivity["Properties"])) {
                foreach ($arMap as $propertyKey => $fieldName) {
                    $arCurrentValues[$fieldName] = $arCurrentActivity["Properties"][$propertyKey] ?? "";
                }
            }
        }

        return $runtime->ExecuteResourceFile(__FILE__, "properties_dialog.php", ["arCurrentValues" => $arCurrentValues]);
    }

    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
    {
        $arErrors = [];

        $arMap = [
            'FileId' => 'fileid',
            'NewFileName' => 'newfilename',
            'DocumentNumber' => 'documentnumber',
            'DocumentDate' => 'documentdate',
            'ContractorName' => 'contractorname',
            'Extension' => 'extension',
        ];

        $arProperties = [];
        foreach ($arMap as $key => $value) {
            $arProperties[$key] = $arCurrentValues[$value] ?? "";
        }

        $arErrors = self::ValidateProperties($arProperties);
        if (!empty($arErrors)) {
            return false;
        }

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $arCurrentActivity["Properties"] = $arProperties;

        return true;
    }
}
?>