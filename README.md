## Yii Framework Widget to embed Soundcloud html5 Player into Yii Framework web apps.

### Instalation

* Copy __**yiiSoundcloudPlayerWidget.php**__ into your __**/protected/extensions/**__

### Changelog

* 0.0.3
    2. Code Optimization
* 0.0.2 
    1. Added **devel** parameter to print out or not, the cURL and Soundcloud API errors 
    2. Cache support for high-traffic web apps and Soundcloud api polite usage :) - params **cache** and **cacheTime**
    (see Full Parameters Example for usage)
* 0.0.1 
    1. Initial Version

### How To Use

Inside your View file call the widget with:

#### Minimal Parameters Single Url

```php
<?php $this->widget('ext.yiisoundcloudplayerwidget', array(           
    'url' => 'http://www.soundcloud.com/cutloosemusic'  // you can put here a profile, group, playlist or track url
)); 
?>  
```

#### Minimal Parameters Multi Url

```php
<?php $this->widget('ext.yiisoundcloudplayerwidget', array(           
    'url' => array('http://www.soundcloud.com/cutloosemusic', // this is a profile
        "http://soundcloud.com/hybrid-species/she-wants-revenge-take-the" // this a direct link to a track
    ),          
)); 
?>  
```

#### Full Parameters

```php
<?php $this->widget('ext.yiisoundcloudplayerwidget', array(
    'devel'         => false,       // default is true. if true all curl and api errors will be printed out, if any.
    'cache'         => false,       // default is true. will use Yii cache system ( Data Caching ).
    'cacheTime'     => 600,         // default is 5 minutes (300 seconds) to keep data in cache server.
    'maxwidth'      => 50,          // default I believe is 100. maxwidth in px.
    'maxheight'     => 305,         // default is 81 for tracks and 305 for all others.
    'color'         => 'ffaa66',    // default is Soundcloud color. hex triplet for player primary color.
    'auto_play'     => false,       // default is false.                
    'show_comments' => false,       // default is true. TimeBased comments on waveform.
    'iframe'        => true,        // default is true => html5 player. false => old Adobe Flash player.        
    'url' => array('http://www.soundcloud.com/cutloosemusic', // this is a profile
        "http://soundcloud.com/hybrid-species/she-wants-revenge-take-the" // this a direct link to a track
    ),      
)); 
?>
```  