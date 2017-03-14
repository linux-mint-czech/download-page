jQuery(function() {
    var data = {};
    var combo = jQuery('select[name=dist_version]');
    var combot = jQuery('select[name=dist_version_torrent]');
    combo.find('option').eq(0).attr('selected', 'selected');
    combot.find('option').eq(0).attr('selected', 'selected');
    
    jQuery('#btn32').click(function(e) {
      data.url = combo.val();
      data.arch = '32bit';
      doDownload(e, data);
    });
    
    jQuery('#btn64').click(function(e) {
      data.url = combo.val();
      data.arch = '64bit';
      doDownload(e, data);
    });
    
    jQuery('#btn32t').click(function(e) {
      data.url = combot.val();
      data.arch = '32bit';
      doDownload(e, data);
    });
    
    jQuery('#btn64t').click(function(e) {
      data.url = combot.val();
      data.arch = '64bit';
      doDownload(e, data);
    });
});
  
function doDownload(e, data) {
    url = data.url;
    url = url.replace('{arch}', data.arch);
    e.preventDefault();  //stop the browser from following
    window.location.href = url;
}

