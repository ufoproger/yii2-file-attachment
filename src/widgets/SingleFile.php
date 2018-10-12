<?php

namespace ufoproger\fileattachment\widgets;

use Yii;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Url;
// use skeeks\widget\simpleajaxuploader\Widget;
use yii\helpers\Json;
use ufoproger\fileattachment\models\File;
use ufoproger\fileattachment\Asset;

class SingleFile extends InputWidget
{
    public $previewElementId = 'preview';
    public $actionFileUpload = 'file-upload';
    public $actionFilePreview = 'file-preview';
    public $removeButton = '<span>удалить</span>';

    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::init();
        $view = $this->getView();

        $value = $this->model->{$this->attribute};

        $formId = self::$stack[0]->id;
        $inputName = Html::getInputName($this->model, $this->attribute);

        $templateHtml = '<p data-file-id="{{id}}"><a href="{{url}}" target="_blank">{{filename}}</a> <a href="#" class="' . $this->id . '-remove-file">' . $this->removeButton . '</a></p>';

        $html = '';
        $html .= sprintf('<div style="display: none;" id="%s">%s</div>', $this->id . '-template', $templateHtml);
        $html .= Html::activeInput('hidden', $this->model, $this->attribute);
        $html .= sprintf('<a href="#" class="btn btn-primary btn-sm btn-outline-primary" id="%s"><span class="fas fa-plus"></span> %s</a>', $this->id, Yii::t('app', 'Прикрепить файл'));
        $html .= '<div id="' . $this->id . '-files">';

        if ($file = File::findOne($this->attribute)) {
            $html .= str_replace(['{{id}}', '{{filename}}', '{{url}}'], [$file->id, $file->name, $file->url], $templateHtml);
        }

        $html .= Html::error($this->model, $this->attribute);
        $html .= '</div>';
        $html .= '<script type="text/javascript">function aaa(a,b,c,d) { console.log("LOG", a, b) } </script>';

        $formId = self::$stack[0]->id;
        $clientOptions = [
            'button' => $this->id,
            'url' => Url::to([$this->actionFileUpload]),
            "name" => 'file',
            'onComplete' => new \yii\web\JsExpression('aaa'),
            // 'onComplete' => new \yii\web\JsExpression(sprintf('function( filename, response, uploadBtn, fileSize ){
            //     response = $.parseJSON(response);
                
            //     var $error = $("#%s").parent().find(".help-block-error");
                
            //     if (!response.success)
            //     {
            //         $error.text(response.message);
            //         return;
            //     }

            //     var $files = $("#%s-files");
            //     var $input = $("#%s input[name=\"%s\"]");

            //     $files.empty();
            //     $error.empty();

            //     var template = $("#%s-template").html();
            //     template = template
            //         .replace("{{id}}", response.file.id)
            //         .replace("{{filename}}", response.file.name)
            //         .replace("{{url}}", response.file.url);

            //     $files.append(template);
            //     $input.val(response.file.id);
            // }',
            // $this->id,
            // $this->id,
            // $formId,
            // $inputName,
            // $this->id))
        ];

        $options = Json::encode($clientOptions);
        $script = "new ss.SimpleUpload($options);" . PHP_EOL;

        $view->registerJs($script);
        Asset::register($view);

        $this->view->registerJs(sprintf('
            $("#%s").on("click", "a.%s-remove-file", function(e)
            {
                e.preventDefault();
                var $this = $(this);
                var $p = $this.closest("p");
                $p.remove();

                $("#%s input[name=\"%s\"]").val("");

                $("#%s").empty();
            });
        ', self::$stack[0]->id, $this->id, self::$stack[0]->id, Html::getInputName($this->model, $this->attribute), $this->previewElementId));

        $this->view->registerJs($this->render('js', [
            'id1' => $this->id,
            'id2' => self::$stack[0]->id,
        ]));
        return $html;
    }
}
