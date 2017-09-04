# Yii2 bigmenu widget. Multilevel menu, PURE CSS

> Any code contribution is welcome

Bigmenu renders a PURE CSS multilevel menu with advanced functions.
   It works like default nav widget, but dropdowns are pure css.
   It also **allows large menu items loaded vía ajax** using `page` parameter.
   It has no limit on levels, but be careful. A lot of levels are uncomfortable for users.


## Installation
`composer require eseperio/yii2-bigmenu-widget` 


   When an item have `page` property and `items`, the first one will be available on medium-large devices meanwhile on second will be available on mobile devices. 
   
   [Screenshots](#screenshots)

 ```php
   echo Bigmenu::widget([
       'items' => [
           [
               'label' => 'Home',
               'url' => ['site/index'],
               'linkOptions' => [...],
           ],
           [
               'label' => 'Multilevel',
               'items' => [
                    ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Dropdown Header</li>',
                    ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
               ],
           ],
           [
               'label' => 'Login',
               'url' => ['site/login'],
               'visible' => Yii::$app->user->isGuest
           ],
           [
               'label' => 'Login',
               'url' => ['site/login'],
               'visible' => Yii::$app->user->isGuest,
               'page' => ['site/menu']
           ],
       ],
       'options' => ['class' =>'nav-pills'], // set this to nav-tab to get tab-styled navigation
   ]);
   ```
   
## How to use
This widget is based on yii/bootstrap/nav widget [yii\bootstrap\nav](http://www.yiiframework.com/doc-2.0/yii-bootstrap-nav.html).
The main differences are:
1. Allows infinite multilevel. For a better usability don´t use more than 3 levels.
2. Allow render a page as a menu panel. With this you are free to make complex and advanced dropdowns menus.
3. Render of pages in menu is made with ajax, and only one request is made per panel.

To use as simple multilevel pass an array to `items` property. See `yii\bootstrap\nav` documentation.
### Dynamic panels
To use dynamic panels add to the menu item the property "page". This must be a link to the action that renders the page. 

```
Bigmenu::widget([
   'hamburger' => "slide",
   'items' => [
      ['label'=>"Dynamic content", "url"=>["site/index"], "page"=>["site/samplemenupage"] ],
      ...
   ]
])
```

In your controller use eseperio\yii2-bigmenu\widget\BigmenuTrait. Then in your action, to render content call `$this->renderBigmenu($view,$options)`.

Note: The trait only simplifies the task of differenciating render method, between `render` and `renderAjax`. So you can do this in your own controller if you don´t want to use traits.

## Change hamburger
This widget implements [Hamburgers by jonsuh](https://jonsuh.com/hamburgers/). You can change the hamburger like this
```
Bigmenu::widget([
   'hamburger' => "slide",
   'items' => [...]
])
```


## Screenshots   
This first screenshot shows a view rendered and loaded vía ajax automatically with this widget.
![Bigmenu screenshot](https://github.com/Eseperio/yii2-bigmenu-widget/blob/master/Captura%20de%20pantalla%202017-09-04%20a%20las%201.32.02.png?raw=true)
![Bigmenu screenshot](https://github.com/Eseperio/yii2-bigmenu-widget/blob/master/Captura%20de%20pantalla%202017-09-04%20a%20las%201.31.54.png?raw=true)
Mobile friendly
![Bigmenu screenshot](https://github.com/Eseperio/yii2-bigmenu-widget/blob/master/Captura%20de%20pantalla%202017-09-04%20a%20las%201.33.08.png?raw=true)
