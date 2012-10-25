$(document).ready(function() {
  function strpos (haystack, needle, offset) {
  var i = (haystack+'').indexOf(needle, (offset || 0));
  return i === -1 ? false : i;
  }
  
  $('table#files-list').find('td > img').each(function(){
    //var extension = $(this).attr('src').split('.').pop();
    var fileType = $(this).attr('src').split('/').pop();
     if (fileType == 'file-image-16.png') {
       oldSrc = $(this).siblings('a').attr('href');
       pos = oldSrc.indexOf('/file_manager/view/')+19;
       oldSrc = oldSrc.substr(pos);
       newSrc = '/thmm/w48-h48-c1:1/public/' + oldSrc;
       //alert(newSrc);
       $(this).attr('src',newSrc);
       $(this).attr('valign','middle');

     } else {
       $(this).attr('style','margin: 16px');
       $(this).attr('valign','middle');       
     }
  })
  
})