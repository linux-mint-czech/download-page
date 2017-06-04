jQuery(function() {
    var data = {};
    var combo = jQuery('select[id=dist_version]');
    
    jQuery('#btn32').click(function(e) {
      data.url = combo.find('option:selected').data('download-url');
      data.arch = '32bit';
      doDownload(e, data);
    });
    
    jQuery('#btn64').click(function(e) {
      data.url = combo.find('option:selected').data('download-url');
      data.arch = '64bit';
      doDownload(e, data);
    });
    
    jQuery('#btn32t').click(function(e) {
      data.url = combo.find('option:selected').data('torrent-url');
      data.arch = '32bit';
      doDownload(e, data);
    });
    
    jQuery('#btn64t').click(function(e) {
      data.url = combo.find('option:selected').data('torrent-url');
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

