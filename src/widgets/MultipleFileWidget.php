<?php
namespace app\modules\site\widgets;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use skeeks\widget\simpleajaxuploader\Widget;

/**
 * Виджет для загрузки нескольких файлов.
 * 
 * @author Михаил Снетков <msnetkov@sfu-kras.ru>
 */
class MultipleFileWidget extends InputWidget
{
    public $realAttribute = null;
    public $previewElementId = null;
    public $actionFileUpload = 'file-upload';
    public $actionFilePreview = 'file-preview';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $value = $this->model->{$this->attribute};

        $formId = self::$stack[0]->id;
        $inputName = Html::getInputName($this->model, $this->attribute);

        $templateHtml = '
            <p data-file-id="{{id}}">
                <a href="{{url}}" target="_blank">{{filename}}</a> <a href="#" class="' . $this->id . '-remove-file"><span class="glyphicon glyphicon-trash"></span></a>
                <input type="hidden" name="' . $inputName . '[]" value="{{id}}">
            </p>';

        $html = '';
        $html .= sprintf('<div style="display: none;" id="%s">%s</div>', $this->id . '-template', $templateHtml);
        $html .= sprintf('<input type="hidden" name="%s" value="">', $inputName);
        $html .= sprintf('<a href="#" class="btn btn-primary btn-xs" id="%s"><span class="glyphicon glyphicon-plus"></span> добавить файл</a>', $this->id);
        $html .= '<div id="' . $this->id . '-error"></div>';
        $html .= '<div id="' . $this->id . '-files">';

        if ($files = $this->model->{$this->realAttribute})
        {
            foreach ($files as $file)
                $html .= str_replace(['{{id}}', '{{filename}}', '{{url}}'], [$file->id, $file->name, $file->url], $templateHtml);
        }

        $html .= '</div>';

        Widget::widget([
            "clientOptions" =>
            [
                'button' => $this->id,
                'url' => Url::to([$this->actionFileUpload]),
                "name" => 'file',
                'onComplete' => new \yii\web\JsExpression('function(filename, response, uploadBtn, fileSize)
                {
                    response = $.parseJSON(response);

                    var $error = $("#' . $this->id . '-error");

                    if (!response.success)
                    {
                        $error.html("<p><i>Ошибка</i>: \"" + response.message + "\".</p>");
                        return;
                    }

                    $error.empty();

                    var template = $("#' . $this->id . '-template").html();
                    template = template.replace("{{id}}", response.file.id);
                    template = template.replace("{{id}}", response.file.id);
                    template = template.replace("{{filename}}", response.file.name);
                    template = template.replace("{{url}}", response.file.url);

                    var $files = $("#' . $this->id . '-files");
                    $files.append(template);

                    $("#' . $this->previewElementId . '").append($("<div></div>").load("' . Url::to([$this->actionFilePreview, 'elementId' => $this->previewElementId, 'id' => '']) . '" + response.file.id));
                }'),
            ]
        ]);

        $this->view->registerJs(sprintf('
            $("#%s").on("click", "a.%s-remove-file", function(e)
            {
                e.preventDefault();
                var $this = $(this);
                var $p = $this.closest("p");
                var fileId = $("input[type=hidden]", $p).val();

                $p.remove();

                $("#%s input[name=\"%s\"]").val("");
                $("#%s div[data-file-id=" + fileId + "]").empty();
            });
        ', self::$stack[0]->id, $this->id, self::$stack[0]->id, Html::getInputName($this->model, $this->attribute), $this->previewElementId));

        return $html;
    }
}
