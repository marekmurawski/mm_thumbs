<?php
/* Security measure */
if (!defined('IN_CMS')) { exit(); }

?>
<h1><?php echo __('mmThumbs'); ?></h1>
<h2>Requirements</h2>
<div style="background-color: #DDD; padding: 16px; border: 1px solid #888;">
<ul style="color: #888">
<?php
 if (function_exists('imageconvolution')) {
    echo '<li>GD Library:<br/><span style="margin-left:32px; color: green">GD library detected</span></li>';
  } else {
    echo '<li>GD Library:<br/><span style="margin-left:32px; color: red">GD library not found!</b></li>';
  }
  if (version_compare(PHP_VERSION, '5.1.0') >= 0) {
    echo '<li>PHP Version:<br/><span style="margin-left:32px; color: green">PHP version is higher than 5.1.0, your version: ' . PHP_VERSION . '</span></li>';
  }  else {
    echo '<li>PHP Version:<br/><span style="margin-left:32px; color: red">PHP version lower than 5.1.0, your version: ' . PHP_VERSION . '</span></li>';
  }
 if (USE_MOD_REWRITE) {
    echo '<li>Use MOD_REWRITE:<br/><span style="margin-left:32px; color: green">MOD_REWRITE is turned ON</span></li>';
  } else {
    echo '<li>Use MOD_REWRITE:<br/><span style="margin-left:32px; color: red">MOD_REWRITE is turned OFF</span></li>';
  }
 if (file_exists(CMS_ROOT.'/.htaccess')) {
    echo '<li>[CMS_ROOT]/.htaccess exists:<br/><span style="margin-left:32px; color: green">'.CMS_ROOT.'/.htaccess file found! URL rewriting seems to work</span></li>';
  } else {
    echo '<li>[CMS_ROOT]/.htaccess exists:<br/><span style="margin-left:32px; color: red">'.CMS_ROOT.'/.htaccess file not found! Setup your URL rewriting!</span></li>';
  }
 if (file_exists(CMS_ROOT.'/thmm/.htaccess')) {
    echo '<li>[CMS_ROOT]/thmm/.htaccess exists:<br/><span style="margin-left:32px; color: green">'.CMS_ROOT.'/thmm/.htaccess file found!</span></li>';
  } else {
    echo '<li>[CMS_ROOT]/thmm/.htaccess exists:<br/><span style="margin-left:32px; color: red">'.CMS_ROOT.'/thmm/.htaccess file not found! Try re-enabling the plugin</span></li>';
  }
 if (file_exists(CMS_ROOT.'/thmm/index.php')) {
    echo '<li>[CMS_ROOT]/thmm/index.php exists:<br/><span style="margin-left:32px; color: green">'.CMS_ROOT.'/thmm/index.php file found!</span></li>';
  } else {
    echo '<li>[CMS_ROOT]/thmm/index.php exists:<br/><span style="margin-left:32px; color: red">'.CMS_ROOT.'/thmm/index.php file not found! Try re-enabling the plugin</span></li>';
  }
  
  
?>
</ul>
</div>

<div style="background-color: #FDB; padding: 16px; margin-left: 32px; margin-top: 16px;">
  <h3>Installation troubleshooting</h3>
<p>
  Depending on your file permission settings it can be impossible for plugin to create <b>[CMS_ROOT]/thmm</b> directory and copy neccessary files into it.
</p>
<p>
  Try to manually (using your ftp client) copy this directory:<br/>
  <b><span style="color: blue;"><?php echo PLUGINS_ROOT; ?></span>/mm_thumbs/lib/thmm</b> directory on your server <br/>
  to <br/>
  <b><span style="color: blue;"><?php echo CMS_ROOT; ?></span>/thmm</b> directory (side by side with <b>/wolf</b> directory)
</p>
<p>And then re-enable the plugin in Administration panel of Wolf CMS</p>
</div>

<div style="background-color: #FDB; padding: 16px; margin-left: 32px; margin-top: 16px;">
  <h3>Special URL rewriting case</h3>
<p>
  If you're using a method of URL rewriting of Wolf CMS residing in subdirectory, for example in <b>.../public_html/wolfcms/</b> <br/>
  <b>http://www.yoursite.com/wolfcms/</b> subdirectory, 
  <br/>but publicly it is accessible via: <br/>
  <b>http://www.yoursite.com/</b> <br/><br/>
  <i>This method is described in this forum topic <a href="http://www.wolfcms.org/forum/topic307.html">http://www.wolfcms.org/forum/topic307.html</a></i>
</p>
<p>
  You need to edit your <b>.../public_html/.htaccess</b> <i>(the DOMAIN ROOT .htaccess, not the one in Wolf root dir)</i> like this:
</p>
<pre>
  RewriteEngine On

  # the following line should be inserted 
  # just after RewriteEngine On statement

  RewriteRule ^thmm(.*)$ wolfcms/thmm/index.php$1 [L]
</pre>
<p>and then <b>DELETE .../public_html/wolfcms/thmm/.htaccess</b> or RENAME it to for example <b>_.htaccess</b></p>
</div>
<h2>Usage</h2>
<p>
  This plugin installs SLIR script in ROOT directory of your Wolf CMS installation. 
  After successful installation of this plugin you can access thumbnails of your
  photos with:
</p>
<p>
  <b><?php echo URL_PUBLIC; ?><span style="color: blue">thmm</span>/</b><span style="color: red">parameters</span><span style="color: green">/path/to/your/image.jpg</span>
</p>
<p>
  Or with short URL starting with slash:
</p>
<p>
  <b><?php echo URI_PUBLIC; ?><span style="color: blue">thmm</span>/</b><span style="color: red">parameters</span><span style="color: green">/path/to/your/image.jpg</span>
</p>
<pre>
   To use, place an img tag with the src pointing to the path of "/thmm/"
   followed by the parameters, followed by the path to the source
   image to resize. All parameters follow the pattern of a one-letter code and
   then the parameter value:
       - Maximum width = w           eg. /thmm/w100/.........
       - Maximum height = h          eg. /thmm/h100/.........
       - Crop ratio = c              eg. /thmm/w100-c1:2/....
       - Quality = q                 eg. /thmm/w200-q85/.....
       - Background fill color = b   eg. /thmm/bF80/.........
       - Progressive = p (for JPG)   eg. /thmm/h100-p/.......

   Resizing a JPEG to a max width of 100 pixels and a max height of 100 pixels
   with proportions 
   <b style="color: blue">&lt;img src="/thmm/w100-h100/path/to/image.jpg" alt="Alt text" /&gt;</b>
  
   Resizing and cropping a JPEG into a square:
   <b style="color: blue">&lt;img src="/thmm/w100-h100-c1:1/path/to/image.jpg" alt="Alt text" /&gt;</b>
  
</pre>
<h3>File manager integration</h3>
<p>By default this plugin also integrates with file manager. All images will have thumbnails shown instead of icons.</p>
<p>If you want to turn it off, edit <b>mm_thumbs/index.php</b> and set </b>$integrate_with_file_manager = false;</b></p>
<h2>Playground area</h2>
<p>Try out typing parameters for this sample image (800x600px) to see live effects of various parameters. You can also click "Randomize!" to create random set of parameters.</p>

<p style="font-weight: bold;">
  &lt;img src="<?php echo URI_PUBLIC; ?>thmm/<input id="params" value="w80"><?php echo '/wolf/plugins/mm_thumbs/images/sample.jpg' ?>" /&gt;
</p>
<p>
  <input type="button" id="resetImage" value="Reset Image">
  <input type="button" id="randomParams" value="Randomize!">
</p>

<div style="background-color: #FDB; padding: 8px; margin-top: 8px; margin-bottom: 8px;">
Sample image URI: <span id="imageuri" style="font-weight: bold; border: 1px solid red"></span>
</div>

<img id="sample" src="/thmm/w80/wolf/plugins/mm_thumbs/images/sample.jpg">

<script type="text/javascript">
// <![CDATA[
    var defaultParams = 'w200-h200-c1:1';
    var defaultImage = '<?php echo URI_PUBLIC; ?>' + 'thmm/'+ defaultParams + '<?php echo '/wolf/plugins/mm_thumbs/images/sample.jpg' ?>';
    
    String.prototype.trimLastMinus=function(){return this.replace(/-$/,'');};
    
    makeRandomParams = function () {
      var newWidth  = 'w' + parseInt(20+Math.floor((Math.random()*58)+1)*10);
      var newHeight = 'h' + parseInt(20+Math.floor((Math.random()*78)+1)*10);
      var newCrop   = 'c' + parseInt(Math.floor((Math.random()*5)+1)) + ':' + parseInt(Math.floor((Math.random()*5)+1));
      var newQuality= 'q' + parseInt(Math.floor((Math.random()*100)+1));
      
      var useWidth  = (Math.random() > 0.5);
      var useHeight = (Math.random() > 0.5);
      var useCrop    = (Math.random() > 0.4);
      var useQuality= (Math.random() > 0.5);
      
      //alert (doCrop);
      
      var paramString = '';
      if (useWidth)   paramString += newWidth + '-';
      if (useHeight)  paramString += newHeight + '-';
      if (useCrop)    paramString += newCrop + '-';
      if (useQuality) paramString += newQuality;
      
      out = paramString.trimLastMinus();
      
      return out;
    }
    
    $(document).ready(function() {
        $('#params').keyup(function() { 
          var newUri = '<?php echo URI_PUBLIC; ?>' + 'thmm/' + $('#params').val() + '<?php echo '/wolf/plugins/mm_thumbs/images/sample.jpg' ?>';
          $('#sample').attr('src', newUri); 
          $('#imageuri').html(newUri); 
          
        });

        $('#resetImage').click(function() { 
          $('#sample').attr('src', defaultImage); 
          $('#params').val(defaultParams); 
        });

        $('#randomParams').click(function() { 
          $('#params').val(makeRandomParams());
          $('#params').trigger('keyup');
        });
        
        $('#resetImage').trigger('click');
        $('#params').trigger('keyup');
    });
// ]]>
</script>