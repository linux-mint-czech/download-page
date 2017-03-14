<?php

/**
 * Download_Page_Shortcode contains all functions neccessary to handle the shortcodes.
 */
class Download_Page_Shortcode {

    public function enqueue_script() {
        wp_enqueue_script( 'download_page_script', DOWNLOAD_PAGE_PLUGIN_DIR . 'js/download-page-script.js' );
    }

    public function do_shortcode( $atts, $content = NULL ) {
        add_action( 'wp_enqueue_scripts', array('Download_Page_Shortcode', 'enqueue_script') );

        $countryCode = "cz";
        $xml = simplexml_load_file('http://'.$_SERVER['HTTP_HOST'].'/downpage.xml');

        $output = '<div id="content" class="custom">' . PHP_EOL;

        $output .= '<div class="download-form">' . PHP_EOL;
        $output .= '<h1>'.$xml->title.'</h1>' . PHP_EOL;
        $output .= '<table border="0" cellspacing="0" cellpadding="0" class="download-table">' . PHP_EOL;

        $output .= '<tr>' . PHP_EOL;
        $output .= '<td width="50%" class="desc"><div class="description">'.$xml->description.'</div></td>' . PHP_EOL;
        $output .= '<td><select name="dist_version">'.self::get_all_download_options($xml).'</select></td>' . PHP_EOL;
        $output .= '<td class="download-td-small"><div><button id="btn64">64-bit</button></div><div><button id="btn32">32-bit</button></div></td>' . PHP_EOL;
        $output .= '</tr>' . PHP_EOL;

        $output .= '<tr>' . PHP_EOL;
        $output .= '<td width="50%" class="desc"><div class="description"> Torrenty: </div></td>' . PHP_EOL;
        $output .= '<td><select name="dist_version_torrent">'.self::get_all_torrent_options($xml).'</select></td>' . PHP_EOL;
        $output .= '<td class="download-td-small"><div><button id="btn64t">64-bit</button></div><div><button id="btn32t">32-bit</button></div></td>' . PHP_EOL;
        $output .= '</tr>' . PHP_EOL;

        $output .= '</table>' . PHP_EOL;
        $output .= '</div>' . PHP_EOL;

        $output .= '<table id="hash-table">' . PHP_EOL;
        $output .= '<thead><tr><th>Název</th><th>Architektura</th><th>SHA256 Hash</th></tr></thead>' . PHP_EOL;
        $output .= '<tbody>'.self::get_all_hashes_table($xml).'</tbody>' . PHP_EOL;
        $output .= '</table>' . PHP_EOL;

        $output .= '</div>' . PHP_EOL;

        return $output;
    }

    private function get_all_download_options( $xml ) {
        $options = '';
        foreach ( $xml->release as $release ) {
            if ( $release->build == "testing" ) {
                $url = $xml->rcUrl;
            } else {
                $url = $release->build == "debian" ? $xml->debianUrl : $xml->mainUrl;
            }
            $url = str_replace('{ver}', $release->version, $url);
            $url = str_replace('{build}', $release->build, $url);
            $url = str_replace('{env}', $release->environment, $url);
            $url = str_replace('{codename}', $release->codename, $url);
            $url = str_replace('{country}', $xml->mirrorlist->$countryCode, $url);

            /**
             * Zkontroluje existenci subverze. Jestliže je přítomna, doplní její hodnotu do URL,
             * jinak zástupný symbol nahradí prázdným řetězcem.
             */
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
            $options .= '<option value="'.$url.'">'.$release->name.'</option>' . PHP_EOL;
        }
        return $options;
    }

    private function get_all_torrent_options( $xml ) {
        $options = '';
        foreach($xml->release as $release) {
            if($release["torrent"] == "yes") {
                $url = $xml->torrentUrl;
                $url = str_replace('{rc}', $release->build == "testing" ? "-beta" : "", $url);
                $url = str_replace('{ver}', $release->version, $url);
                $url = str_replace('{env}', $release->environment, $url);
                $url = str_replace('{codename}', $release->codename, $url);

                /**
                 * Zkontroluje existenci subverze. Jestliže je přítomna, doplní její hodnotu do URL,
                 * jinak zástupný symbol nahradí prázdným řetězcem.
                 */
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

                if ($release->build == "debian") {
                    $url = str_replace('-dvd', "", $url);
                }
                $options .= '<option value="'.$url.'">'.$release->name.'</option>';
            }
        }
        return $options;
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

