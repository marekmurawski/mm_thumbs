Requirements
------------

 - GD Library
 - PHP Version - 5.1.0
 - Use MOD_REWRITE

Usage
-----

This plugin installs SLIR script in ROOT directory of your Wolf CMS installation. After successful installation of this plugin you can access thumbnails of your photos with:

```
http://YOURSITE.COM/thmm/__parameters__/path/to/your/image.jpg
```

Or with short URL starting with slash:

```
/thmm/__parameters__/path/to/your/image.jpg
```

To use, place an img tag with the src pointing to the path of "/thmm/" followed by the parameters, followed by the path to the source image to resize. All parameters follow the pattern of a one-letter code and then the parameter value:

 - **Maximum width** = "w" - *eg. /thmm/w100/...*
 - **Maximum height** = "h" - *eg. /thmm/h100/...*
 - **Crop ratio** = "c" - *eg. /thmm/w100-c1:2/...*
 - **Quality** = "q" - *eg. /thmm/w200-q85/...*
 - **Background fill color** (for transp. PNGs) = "b" - *eg. /thmm/bF80/...*
 - **Progressive** = "p" (for JPEG) - *eg. /thmm/h100-p/...*

Examples
--------

Resizing a JPEG to a max width of 100 pixels and a max height of 100 pixels
with proportions 

```
<img src="/thmm/w100-h100/path/to/image.jpg" alt="Alt text" />
```
  
Resizing and cropping a JPEG into a square:

```
<img src="/thmm/w100-h100-c1:1/path/to/image.jpg" alt="Alt text" />
```

Installation
------------

mmThumbs can be installed into your WolfCMS by uploading it to **CMS_ROOT/wolf/plugins/mm_thumbs/** and enabling it in administration panel.

In case of failure you can try to manually copy **mm_thumbs/lib/thmm** directory on your server to **CMS_ROOT** directory (top level, side by side with **/wolf** directory) 


0.0.1

- first release


License
-------

* GPLv3 license

Disclaimer
----------

While I make every effort to deliver quality plugins for Wolf CMS, I do not guarantee that they are free from defects. They are provided “as is," and you use it at your own risk. I'll be happy if you notice me of any errors.

I'm not really programmer nor web developer, however I like programming PHP and JavaScript. In fact I'm an [architekt](http://marekmurawski.pl).