<?php
class SafeFormBuilder extends FormBuilder
{
    private array $submittedData;

    public function __construct(string $method, string $action, string $submitText = 'Submit') {
        parent::__construct($method, $action, $submitText);
        $this->submittedData = $this->loadSubmittedData($method);
    }

    private function loadSubmittedData(string $method) : array {
        return $method === self::METHOD_POST ? $_POST : $_GET;
    }

    private function getSubmittedValue(string $name, ?string $default = null): ?string {
        return $this->submittedData[$name] ?? $default;
    }

    public function addTextField($name, $defaultValue = '') : self{
        $selectedValue = $this->getSubmittedValue($name, $defaultValue);
        return parent::addTextField($name, $selectedValue);
    }

    public function addRadioGroup($name, $options) : self
    {
        $selectedValue = $this->getSubmittedValue($name);
        foreach ($options as $value) {
            $this->fields[] = [
                'type' => 'radio',
                'name' => $name,
                'value' => $value,
                'checked' => ($selectedValue === $value)
            ];
        }
        return $this;
    }

    public function addCheckbox($name, $value, $checked = false) : self{
        if (empty($this->submittedData)){return parent::addCheckbox($name, $value, $checked);}
        $submittedValue = $this->getSubmittedValue($name);

        return parent::addCheckbox($name, $value, $submittedValue);
    }

    public function addTextarea($name, $content = '', $rows = 4, $cols = 50) : self{
        $submittedValue = $this->getSubmittedValue($name, $content);
        return parent::addTextarea($name, $submittedValue, $rows, $cols);
    }

}