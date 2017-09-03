<?
namespace eseperio\bigmenu\traits;

trait BigmenuTrait
{

    public function renderBigmenu($view, $params=[])
    {
        $method = "render";
        if (\Yii::$app->request->isAjax)
            $method = "renderAjax";

        return $this->{$method}($view, $params);
    }
}