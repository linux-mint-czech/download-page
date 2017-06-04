jQuery(function() {
    var combo = jQuery('select[id=dist_version]');
    combo.change(downloadComboChangeListener);
    initHashTable(combo);

    jQuery('#btn32').click(function(e) {
      doDownload(e, combo.find('option:selected').data('download-url'), '32bit');
    });

    jQuery('#btn64').click(function(e) {
      doDownload(e, combo.find('option:selected').data('download-url'), '64bit');
    });

    jQuery('#btn32t').click(function(e) {
      doDownload(e, combo.find('option:selected').data('torrent-url'), '32bit');
    });

    jQuery('#btn64t').click(function(e) {
      doDownload(e, combo.find('option:selected').data('torrent-url'), '64bit');
    });
});

function downloadComboChangeListener(e) {
    var combo = jQuery(e.target);
    var dist = combo.find('option:selected').text();
    jQuery('#hash-table tbody td[rowspan=2]').each(function() {
      if ( jQuery(this).text() == dist ) {
          jQuery(this).parent().show();
          jQuery(this).parent().next().show();
      } else {
          jQuery(this).parent().hide();
          jQuery(this).parent().next().hide();
      }
    });
}

function initHashTable(combo) {
    var e = {};
    e.target = combo;
    downloadComboChangeListener(e);
}

function doDownload(e, url, arch) {
    e.preventDefault();
    window.location.href = url.replace('{arch}', arch);
}

