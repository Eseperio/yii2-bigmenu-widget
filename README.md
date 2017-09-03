# yii2-bigmenu-widget

Bigmenu renders a PURE CSS multilevel menu with advanced functions.
   It works like default nav widget, but dropdowns are pure css.
   It also allows large menu items loaded vía ajax using `page' parameter.
   It has no limit on levels, but be careful. A lot of levels are uncomfortable to users.
  
   If an item have `page` property and items the first one will be available on medium-large devices
   Meanwhile on mobile devices items will be rendered.

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
