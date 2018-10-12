<?php
namespace app\modules\site\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

/**
 * Виджет для предпросмотра нескольких файлов.
 * 
 * @author Михаил Снетков <msnetkov@sfu-kras.ru>
 */
class MultipleFilePreviewWidget extends Widget
{
    public $elementId = null;
    public $files = null;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $html = '<div class="center-block" id="' . $this->elementId . '">';

        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $html .= SingleFilePreviewWidget::renderSingleFile($file);
            }
        }

        $html .= '</div>';

        return $html;
    }
}
