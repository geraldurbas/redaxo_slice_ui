<?php

/**
 * Fügt die benötigen Stylesheets ein
 *
 * @param $params Extension-Point Parameter
 */
function rex_be_script_js_add($params)
{
    global $REX;
    $addon = 'be_style';
    foreach (OOPlugin::getAvailablePlugins($addon) as $plugin) {
        if (file_exists('../' . $REX['MEDIA_ADDON_DIR'] . '/' . $addon . '/plugins/' . $plugin . '/js_main.js')) {
            $params['subject'] .= "\n" . '  <script type="text/javascript" src="../' . $REX['MEDIA_ADDON_DIR'] . '/' . $addon . '/plugins/' . $plugin . '/js_main.js"></script>';
        }
    }
    return $params['subject'];
}
