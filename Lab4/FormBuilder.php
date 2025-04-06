<?php
class FormBuilder
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';

    private $method;
    private $target;
    private $submitText;
    public $fields = [];

    public function __construct($method, $target, $submitText = 'Submit')
    {
        $this->method = $method;
        $this->target = $target;
        $this->submitText = $submitText;
    }

    public function addTextField($name, $defaultValue = '') : self
    {
        $this->fields[] = [
            'type' => 'text',
            'name' => $name,
            'value' => $defaultValue
        ];
        return $this;
    }

    public function addRadioGroup($name, $options) : self
    {
        foreach ($options as $value) {
            $this->fields[] = [
                'type' => 'radio',
                'name' => $name,
                'value' => $value
            ];
        }
        return $this;
    }

    public function addCheckbox($name, $value, $checked = false) : self
    {
        $this->fields[] = [
            'type' => 'checkbox',
            'name' => $name,
            'value' => $value,
            'checked' => $checked
        ];
        return $this;
    }

    public function addTextarea($name, $content = '', $rows = 4, $cols = 50) : self
    {
        $this->fields[] = [
            'type' => 'textarea',
            'name' => $name,
            'content' => $content,
            'rows' => $rows,
            'cols' => $cols
        ];
        return $this;
    }

    public function getForm() : string
    {
        $html = '<form method="' . htmlspecialchars($this->method) . '" target="' . htmlspecialchars($this->target) . '">' . PHP_EOL;

        foreach ($this->fields as $field) {
            switch ($field['type']) {
                case 'text':
                    $html .= ' <input type="text" name="' . htmlspecialchars($field['name']) . '" value="' . htmlspecialchars($field['value']) . '" />' . PHP_EOL;
                    break;

                case 'radio':
                    $html .= ' <input type="radio" name="' . htmlspecialchars($field['name']) . '" value="' . htmlspecialchars($field['value']) . '"';
                    if ($field['checked']) {
                        $html .= ' checked';
                    }
                    $html .= ' />' . PHP_EOL;
                    break;

                case 'checkbox':
                    $html .= ' <input type="checkbox" name="' . htmlspecialchars($field['name']) . '" value="' . htmlspecialchars($field['value']) . '"';
                    if ($field['checked']) {
                        $html .= ' checked';
                    }
                    $html .= ' />' . PHP_EOL;
                    break;

                case 'textarea':
                    $html .= ' <textarea name="' . htmlspecialchars($field['name']) . '" rows="' . $field['rows'] . '" cols="' . $field['cols'] . '">';
                    $html .= htmlspecialchars($field['content']);
                    $html .= '</textarea>' . PHP_EOL;
                    break;
            }
        }

        $html .= ' <input type="submit" value="' . htmlspecialchars($this->submitText) . '" />' . PHP_EOL;
        $html .= '</form>';

        return $html;
    }
}
