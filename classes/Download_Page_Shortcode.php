<?php

/**
 * Download_Page_Shortcode contains all functions neccessary to handle the shortcodes.
 */
class Download_Page_Shortcode {

    public function do_shortcode( $atts, $content = NULL ) {
        $xml = simplexml_load_file( DOWNLOAD_PAGE_PLUGIN_DIR.'data/downpage.xml' );

        $output .= '<form id="download-form">' . PHP_EOL;
        $output .= '<label for="dist_version"> Verze: </label>' . PHP_EOL;
        $output .= '<select id="dist_version">'.self::get_all_download_options($xml,'cz').'</select>' . PHP_EOL;
        $output .= '<span id="download_buttons">' . PHP_EOL;
        $output .= '<button id="btn64">64-bit ISO</button><button id="btn32">32-bit ISO</button><button id="btn64t">64-bit torrent</button><button id="btn32t">32-bit torrent</button>' . PHP_EOL;
        $output .= '</span>' . PHP_EOL;
        $output .= '</form>' . PHP_EOL;

        $output .= '<table id="hash-table">' . PHP_EOL;
        $output .= '<thead><tr><th>Název</th><th>Architektura</th><th>SHA256 Hash</th></tr></thead>' . PHP_EOL;
        $output .= '<tbody>'.self::get_all_hashes_table($xml).'</tbody>' . PHP_EOL;
        $output .= '</table>' . PHP_EOL;

        wp_enqueue_style( 'download_page_css', plugins_url( 'css/style.css', DOWNLOAD_PAGE_PLUGIN_FILE ) );
        wp_enqueue_script( 'download_page_script', plugins_url( 'js/download-page-script.js', DOWNLOAD_PAGE_PLUGIN_FILE ) );
        return $output;
    }

    private function get_all_download_options( $xml, $countryCode ) {
        $options = '';
        foreach ( $xml->release as $release ) {
            if ( $release->build == "testing" ) {
                $download_url = $xml->rcUrl;
            } else {
                $download_url = $release->build == "debian" ? $xml->debianUrl : $xml->mainUrl;
            }
            $download_url = str_replace('{ver}', $release->version, $download_url);
            $download_url = str_replace('{build}', $release->build, $download_url);
            $download_url = str_replace('{env}', $release->environment, $download_url);
            $download_url = str_replace('{codename}', $release->codename, $download_url);
            $download_url = str_replace('{country}', $xml->mirrorlist->$countryCode, $download_url);
            $download_url = self::check_release_subversion($release, $download_url);

            $torrent_url = '';
            if($release["torrent"] == "yes") {
                $torrent_url = $xml->torrentUrl;
                $torrent_url = str_replace('{rc}', $release->build == "testing" ? "-beta" : "", $torrent_url);
                $torrent_url = str_replace('{ver}', $release->version, $torrent_url);
                $torrent_url = str_replace('{env}', $release->environment, $torrent_url);
                $torrent_url = str_replace('{codename}', $release->codename, $torrent_url);
                $torrent_url = self::check_release_subversion($release, $torrent_url);

                if ($release->build == "debian") {
                    $torrent_url = str_replace('-dvd', "", $torrent_url);
                }
            }
            $options .= '<option data-download-url="'.$download_url.'" data-torrent-url="'.$torrent_url.'">'.$release->name.'</option>' . PHP_EOL;
        }
        return $options;
    }

    /**
     * Zkontroluje existenci subverze. Jestliže je přítomna, doplní její hodnotu do URL,
     * jinak zástupný symbol nahradí prázdným řetězcem.
     */
    private function check_release_subversion( $release, $url ) {
        if ( strcmp( $release->update, "0") != 0 ) {
            $url = str_replace('{update}', "-".$release->update, $url);
            $url = str_replace('-dvd', "", $url);
        } else {
            $url = str_replace('{update}', "", $url);
        }
        if ( strcmp( $release->subversion, "0") != 0 ) {
            $url = str_replace('{subver}', ".".$release->subversion, $url);
            $url = str_replace('-dvd', "", $url);
        } else {
            $url = str_replace('{subver}', "", $url);
        }
        return $url;
    }

    private function get_all_hashes_table( $xml ) {
        $output = '';
        foreach($xml->release as $release) {
            $md5x64 = $release->md5->x64;
            $md5x86 = $release->md5->x86;

            $output .= '<tr>';
            $output .= '<td rowspan="2">'.$release->name.'</td>';
            $output .= '<td>64bit</td><td>'.$md5x64.'</td>';
            $output .= '</tr>';
            $output .= '<tr class="center">';
            $output .= '<td>32bit</td><td>'.$md5x86.'</td>';
            $output .= '</tr>';
        }
        return $output;
    }

}

