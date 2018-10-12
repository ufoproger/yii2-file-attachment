<?php
namespace app\modules\site\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

class SingleFilePreviewWidget extends Widget
{
    public $elementId = null;
    public $file = null;
    public $hideContainer = false;


    public static function renderSingleFile($file)
    {
        $html = sprintf('<div data-file-id="%d">', $file->id);
        $html .= sprintf('<p class="text-center"><a href="%s">%s (%s)</a></p>', $file->url, $file->name, Yii::$app->formatter->asShortSize($file->size, 1));

        if ($file->isImage) {
            $html .= Html::a(Html::img($file->url), $file->url, ['data-lightbox' => 'preview', 'class' => 'thumbnail', 'data-title' => $file->name]);
        } elseif ($file->isDocument) {
            $coverUrl = Yii::getAlias('@web/img/A4-ratio.png');
            $viewerUrl = '//docs.google.com/viewer?' . http_build_query(['url' => Yii::$app->request->hostInfo . $file->url, 'embedded' => 'true']);

            $html .= sprintf(
                '<div class="iframe-full"><img src="%s"><iframe frameborder="0" width="100%%" src="%s"></iframe></div>',
                $coverUrl,
                $viewerUrl
            );
        } else {
            $html .= '<div class="well text-center">' . Yii::t('conf', 'File preview is not available') . '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $html = '';

        if (!$this->hideContainer) {
            $html .= '<div class="center-block" id="' . $this->elementId . '">';
        }

        if (!empty($this->file)) {
            $html .= self::renderSingleFile($this->file);
        }

        if (!$this->hideContainer) {
            $html .= '</div>';
        }

        return $html;
    }
}
