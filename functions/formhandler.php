<?php
require_once ROOT . "functions/utilities.php";
require_once ROOT . "functions/helper.php";

class FormField
{
    public string $name;
    public mixed $value;
    public bool $isRequired;
    public string $inputType;

    public function __construct(string $name, mixed $value = null, bool $isRequired = true, string $inputType = 'text')
    {
        $this->name = $name;
        $this->value = $value;
        $this->isRequired = $isRequired;
        $this->inputType = $inputType;
    }
}

class formHandler extends helper
{
    private $data = [];
    public $errors = [];
    private $files = [];
    private $validatedData = [];
    private $action;
    private $targetTable;

    public function validateForm(array $formData, string $targetTable = "", string $action = null, bool $showError = true): ?array
    {
        if(isset($formData['input_data'])) unset($formData['input_data']);
        $this->data = $formData;
        $this->action = $action;
        $this->targetTable = $targetTable;

        if (!$this->isValidDataArray()) {
            return null;
        }

        $this->processInputs($showError);
        $this->processFiles();

        if ($this->hasErrors()) {
            return null;
        }

        if (!$this->performDatabaseAction()) {
            return null;
        }
        if(is_array($this->validatedData) && isset($this->validatedData['confirm_password'])) unset($this->validatedData['confirm_password']);
        return $this->validatedData;
    }

    private function isValidDataArray(): bool
    {
        if (!is_array($this->data)) {
            $this->addError("Invalid form data provided.");
            return false;
        }
        return true;
    }

    private function processInputs(bool $showError): void
    {
        foreach ($this->data as $key => $field) {
            // Handle FormField objects
            if ($field instanceof FormField) {
                $this->processFormField($field, $showError);
                continue;
            }

            // Handle array-based fields (backward compatibility)
            if ($this->isFileInput($field)) {
                $this->files[$key] = $field;
                continue;
            }

            if ($this->isRequired($field) && !$this->isFieldSet($key)) {
                if ($showError) {
                    $this->addError(ucwords(str_replace("_", " ", $key)) . " is required.");
                }
                continue;
            }

            $this->validatedData[$key] = $this->sanitizeInput($key, $field);
        }

        $this->validatePasswords();
    }

    private function processFormField(FormField $field, bool $showError): void
    {
        if ($field->inputType === 'file') {
            $this->files[$field->name] = $field;
            return;
        }

        if ($field->isRequired && !$this->isFieldSet($field->name)) {
            if ($showError) {
                $this->addError(ucwords(str_replace("_", " ", $field->name)) . " is required.");
            }
            return;
        }

        $this->validatedData[$field->name] = $this->sanitizeInput($field->name);
    }

    private function processFiles(): void
    {
        foreach ($this->files as $key => $file) {
            if ($file instanceof FormField) {
                $processedFile = $this->processSingleFile($file->name, $file->value);
            } else {
                $processedFile = $this->processSingleFile($key, $file);
            }

            if ($processedFile === false) {
                $this->addError("Failed to process file: $key");
                continue;
            }
            $this->validatedData[$key] = $processedFile;
        }
    }

    private function isFileInput(array $field): bool
    {
        return isset($field['input_type']) && $field['input_type'] === 'file';
    }

    private function isRequired(array $field): bool
    {
        return isset($field['is_required']) ? $field['is_required'] : true;
    }

    private function isFieldSet(string $key): bool
    {
        return isset($_POST[$key]) && ($_POST[$key] !== '' || $_POST[$key] === '0' || $_POST[$key] === 0);
    }

    private function sanitizeInput(string $key): string
    {
        if (isset($_POST[$key]) && is_array($_POST[$key])) {
            return json_encode($_POST[$key]);
        }

        if (isset($_POST[$key])) {
            return htmlspecialchars($_POST[$key]);
        }

        return "";
    }

    private function validatePasswords(): void
    {
        if (isset($this->data['password'], $this->data['confirm_password']) && !empty($_POST['password'])) {
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $this->addError("Password and confirm password do not match.");
            } else {
                $this->validatedData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                unset($this->validatedData['confirm_password']);
            }
        }
    }

    private function processSingleFile(string $key, array $file)
    {
        if (!isset($file['file_name']) || empty($file['file_name'])) {
            return "no--value";
        }

        $fileName = preg_replace('/\.\w+$/', '', $file['file_name']);
        $validFormats = $file['formart'] ?? null;

        return $this->process_image($fileName, $file['path'], $key, valid_formats1: $validFormats);
    }

    private function performDatabaseAction(): bool
    {
        if (empty($this->targetTable) || $this->action === null) {
            return true;
        }

        switch ($this->action) {
            case 'insert':
                return $this->quick_insert($this->targetTable, $this->validatedData);
            case 'update':
                if (!isset($this->validatedData['ID'])) {
                    $this->addError("ID is required for update action.");
                    return false;
                }
                $id = $this->validatedData['ID'];
                return $this->update($this->targetTable, $this->validatedData, "ID = '$id'");
            default:
                return true;
        }
    }

    private function addError(string $message): void
    {
        $this->errors[] = $message;
        // echo $this->message($message, "error");
    }

    private function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}