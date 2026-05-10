<?php
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function bands() {
    return ['160m','80m','60m','40m','30m','20m','17m','15m','12m','10m','6m','2m','70cm'];
}

function modes() {
    return ['SSB','CW','FT8','FT4','RTTY','PSK31','FM','AM'];
}

function default_rst_for_mode($mode) {
    return strtoupper($mode) === 'CW' ? '599' : '59';
}

function adif_field($name, $value) {
    if ($value === null || $value === '') return '';
    $value = (string)$value;
    return '<' . strtoupper($name) . ':' . strlen($value) . '>' . $value . ' ';
}

function normalize_call($call) {
    return strtoupper(trim((string)$call));
}

function valid_choice($value, $allowed, $default) {
    return in_array($value, $allowed, true) ? $value : $default;
}
