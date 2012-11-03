# Yii Framework Widget to embed Soundcloud html5 Player into Yii Framework web apps.

### Instalation

* Copy yiiSoundcloudPlayerWidget.php into your /protected/extensions

### How To Use

Inside your View file call the widget with:

#### Minimal Parameters Single Url

```php
<?php $this->widget('ext.yiisoundcloudplayerwidget', array(           
    'url' => 'http://www.soundcloud.com/cutloosemusic'  // you can put here a profile, group, playlist or track url     
)); ?>  

#### Minimal Parameters Multi Url

```php
<?php $this->widget('ext.yiisoundcloudplayerwidget', array(           
    'url' => array('http://www.soundcloud.com/cutloosemusic', // this is a profile
        "http://soundcloud.com/hybrid-species/she-wants-revenge-take-the" // this a direct link to a track
    ),          
)); ?>  

#### Full Parameters

```php
<?php $this->widget('ext.yiisoundcloudplayerwidget', array(
    //'maxwidth'      => 100,         // default I believe is 100. maxwidth in px.
    'maxheight'     => 305,         // default is 81 for tracks and 305 for all others.
    'color'         => 'ff0066',    // default is Soundcloud color. hex triplet for player primary color.
    'auto_play'     => false,        // default is false.                
    'show_comments' => false,       // default is true. TimeBased comments on waveform.
    'iframe'        => true,       // default is true => html5 player. false => old Adobe Flash player.                
    'url' => array('http://www.soundcloud.com/cutloosemusic', // this is a profile
        "http://soundcloud.com/hybrid-species/she-wants-revenge-take-the" // this a direct link to a track
    ),      
)); ?>  