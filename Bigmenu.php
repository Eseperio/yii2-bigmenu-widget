<?php


namespace eseperio\bigmenu;

use yii\base\Widget;
use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\Url;

/**
 * Bigmenu renders a PURE CSS multilevel menu with advanced functions.
 * It works like default nav widget, but dropdowns are pure css.
 * It also allows large menu items loaded vía ajax using `page' parameter.
 * It has no limit on levels, but be careful. A lot of levels are uncomfortable to users.
 *
 * If an item have `page` property and items the first one will be available on medium-large devices
 * Meanwhile on mobile devices items will be rendered.
 *
 * For example:
 *
 * ```php
 * echo Bigmenu::widget([
 *     'items' => [
 *         [
 *             'label' => 'Home',
 *             'url' => ['site/index'],
 *             'linkOptions' => [...],
 *         ],
 *         [
 *             'label' => 'Multilevel',
 *             'items' => [
 *                  ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
 *                  '<li class="divider"></li>',
 *                  '<li class="dropdown-header">Dropdown Header</li>',
 *                  ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
 *             ],
 *         ],
 *         [
 *             'label' => 'Login',
 *             'url' => ['site/login'],
 *             'visible' => Yii::$app->user->isGuest
 *         ],
 *         [
 *             'label' => 'Login',
 *             'url' => ['site/login'],
 *             'visible' => Yii::$app->user->isGuest,
 *             'page' => ['site/menu']
 *         ],
 *     ],
 *     'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
 * ]);
 * ```
 *
 *
 *
 * @author E.Alamo Github: @eseperio
 * @since  2.0
 */
class Bigmenu extends Widget
{
    /**
     * @var array list of items in the nav widget. Each array element represents a single
     * menu item which can be either a string or an array with the following structure:
     *
     * - label: string, required, the nav item label.
     * - url: optional, the item's URL. Defaults to "#".
     * - visible: boolean, optional, whether this menu item is visible. Defaults to true.
     * - linkOptions: array, optional, the HTML attributes of the item's link.
     * - options: array, optional, the HTML attributes of the item container (LI).
     * - active: boolean, optional, whether the item should be on active state or not.
     * - dropDownOptions: array, optional, the HTML options that will passed to the [[Dropdown]] widget.
     * - items: array|string, optional, the configuration array for creating a [[Dropdown]] widget,
     * - page: array|string, optional, the url of the page to be loaded dynamically. Use bigmenutrait in your controller
     *   or a string representing the dropdown menu. Note that Bootstrap does not support sub-dropdown menus.
     * - encode: boolean, optional, whether the label will be HTML-encoded. If set, supersedes the $encodeLabels option
     * for only this item.
     *
     * If a menu item is a string, it will be rendered directly without HTML encoding.
     */
    public $items = [];
    /**
     * @var string type of hamburger to use on mobile. @see https://github.com/jonsuh/hamburgers#usage
     * Allowed values are:
     * 3dx
     * 3dx-r
     * 3dy
     * 3dy-r
     * 3dxy
     * 3dxy-r
     * arrow
     * arrow-r
     * arrowalt
     * arrowalt-r
     * arrowturn
     * arrowturn-r
     * boring
     * collapse
     * collapse-r
     * elastic
     * elastic-r
     * emphatic
     * emphatic-r
     * minus
     * slider
     * slider-r
     * spin
     * spin-r
     * spring
     * spring-r
     * stand
     * stand-r
     * squeeze
     * vortex
     * vortex-r
     */
    public $hamburger = 'slider';
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];
    /**
     * @var boolean whether the nav items labels should be HTML-encoded.
     */
    public $encodeLabels = true;
    /**
     * @var boolean whether to automatically activate items according to whether their route setting
     * matches the currently requested route.
     * @see isItemActive
     */
    public $activateItems = true;
    /**
     * @var boolean whether to activate parent menu items when one of the corresponding child menu items is active.
     */
    public $activateParents = false;
    /**
     * @var string the route used to determine if a menu item is active or not.
     * If not set, it will use the route of the current request.
     * @see params
     * @see isItemActive
     */
    public $route;
    /**
     * @var array the parameters used to determine if a menu item is active or not.
     * If not set, it will use `$_GET`.
     * @see route
     * @see isItemActive
     */
    public $params;
    /**
     * @var string this property allows you to customize the HTML which is used to generate the drop down caret symbol,
     * which is displayed next to the button text to indicate the drop down functionality.
     * Defaults to `null` which means `<b class="caret"></b>` will be used. To disable the caret, set this property to
     * be an empty string.
     */
    public $dropDownCaret;

    /**
     * @var bool Whether to draw or not the box for dynamic loaded menus
     */
    public $hasPages = false;
    /**
     * @var array the options used in js. By default:
     * [
     *   widthToEnableResponsive => 768
     * ]
     */
    public $jsOptions = [];

    /**
     * @var int Iterations counter for toggle-submenu labels
     */
    private $sub_counter = 0;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        if ($this->dropDownCaret === null) {
            $this->dropDownCaret = Html::tag('b', '', ['class' => 'caret']);
        }
        Html::addCssClass($this->options, ['widget' => 'bigmenu']);
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $view = $this->getView();
        $widthToEnableResponsive = (int)($this->jsOptions['widthToEnableResponsive'] ?? 768) .'px';
        $view->registerCss('@media (max-width:' . $widthToEnableResponsive . '){
            .bigmenu{
                overflow-x: hidden;
            }
         }');
        $view->registerJsVar('YII2_BIGMENU_WIDGET_OPTIONS', $this->jsOptions);
        BigmenuAsset::register($view);

        return $this->renderItems();
    }

    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $items = [];
        foreach ($this->items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $items[] = $this->renderItem($item);
        }

        $html = Html::beginTag('div', $this->options);
        $html .= Html::checkbox($this->id, false, ['id' => $this->id, 'class' => 'bigmenu-toggle-mobile']);
        $html .= Html::label($this->renderHamburger(), $this->id);
        $html .= Html::tag('ul', implode("\n", $items), ['class' => 'main-bigmenu']);

        if ($this->hasPages)
            $html .= Html::tag('div', "Loading", ['class' => 'bigmenu-page-panel']);

        $html .= Html::endTag('div');

        return $html;
    }

    /**
     * Renders a widget's item.
     * @param string|array $item the item to render.
     * @return string the rendering result.
     * @throws InvalidConfigException
     */
    public function renderItem($item)
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $page = ArrayHelper::getValue($item, 'page', false);
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);


        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        if ($page) {
            $this->hasPages = true;
            $options['data']['bigmenu-page'] = Url::to($page);
            Html::addCssClass($options, 'bigmenu-ajax');
        }

        $toggleButton = "";
        if (empty($items) && !$page) {
            $items = '';

        } else {
            if ($this->dropDownCaret !== '') {
                $label .= ' ' . $this->dropDownCaret;
            }
            if (is_array($items)) {
                if ($this->activateItems) {
                    $items = $this->isChildActive($items, $active);
                }
                $items = $this->renderDropdown($items, $item);
                $toggleButton = Html::checkbox($this->getIterationId(), false, ['class' => 'toggle-submenu-mobile', 'id' => $this->getIterationId()]);
                $toggleButton .= Html::label('<span></span><span></span>', $this->getIterationId(), ['class' => 'toggle-submenu-mobile']);

            }
        }

        if ($this->activateItems && $active) {
            Html::addCssClass($options, 'active');
        }


        $this->sub_counter++;

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $toggleButton . $items, $options);
    }

    public function getIterationId()
    {
        return $this->id . "bm" . $this->sub_counter;
    }

    public function renderHamburger()
    {
        $html = Html::beginTag('span', ['class' => 'hamburger hamburger--' . $this->hamburger]);
        $html .= Html::beginTag('span', ['class' => 'hamburger-box']);
        $html .= Html::tag('span', "", ['class' => 'hamburger-inner']);
        $html .= Html::endTag('span');
        $html .= Html::endTag('span');

        return $html;
    }

    /**
     * Renders the given items as a dropdown.
     * This method is called to create sub-menus.
     * @param array $items the given items. Please refer to [[Dropdown::items]] for the array structure.
     * @param array $parentItem the parent item information. Please refer to [[items]] for the structure of this array.
     * @return string the rendering result.
     * @since 2.0.1
     */
    protected function renderDropdown($items, $parentItem)
    {
        $options = ArrayHelper::getValue($parentItem, 'dropDownOptions', []);

        return Html::ul($items, [
            'item' => function ($item, $index) {

                if ($nestedItems = ArrayHelper::getValue($item, 'items', false)) {
                    if ($nestedItems instanceof \Traversable) {
                        throw new InvalidParamException('Argument $items must be an array or implement Traversable');
                    }
                }

                return $this->renderItem($item);
            }
        ]);

    }

    /**
     * Check to see if a child item is active optionally activating the parent.
     * @param array $items @see items
     * @param boolean $active should the parent be active too
     * @return array @see items
     */
    protected function isChildActive($items, &$active)
    {
        foreach ($items as $i => $child) {
            if (ArrayHelper::remove($items[$i], 'active', false) || $this->isItemActive($child)) {
                Html::addCssClass($items[$i]['options'], 'active');
                if ($this->activateParents) {
                    $active = true;
                }
            }
        }

        return $items;
    }

    /**
     * Checks whether a menu item is active.
     * This is done by checking if [[route]] and [[params]] match that specified in the `url` option of the menu item.
     * When the `url` option of a menu item is specified in terms of an array, its first element is treated
     * as the route for the item and the rest of the elements are the associated parameters.
     * Only when its route and parameters match [[route]] and [[params]], respectively, will a menu item
     * be considered active.
     * @param array $item the menu item to be checked
     * @return boolean whether the menu item is active
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
