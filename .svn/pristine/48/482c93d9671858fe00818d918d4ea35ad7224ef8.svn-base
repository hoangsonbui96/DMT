<?php

/**
 * List Function Support Display Or Process in UI
 *
 * @author AKB TungNT
 * @version 1.0 (2020.04.10)
 */

defined('FOMAT_DISPLAY_DAY') || define('FOMAT_DISPLAY_DAY', 'd/m/Y');
defined('FOMAT_DISPLAY_CREATE_DAY') || define('FOMAT_DISPLAY_CREATE_DAY', 'd/m/Y H:i:s');
defined('FOMAT_DISPLAY_MONTH') || define('FOMAT_DISPLAY_MONTH', 'm/Y');
defined('FOMAT_DISPLAY_TIME') || define('FOMAT_DISPLAY_TIME', 'H:i');
defined('FOMAT_DISPLAY_DATE_TIME') || define('FOMAT_DISPLAY_DATE_TIME', 'd/m/Y H:i');
defined('CHECK_PASSWORD') || define('CHECK_PASSWORD', '/^(?=.*[!@#$?%^*()_+}{&quot;:;&gt;.&lt;,\/=-])(?=.*[0-9])(?=.*[A-Za-z]).{8,}$/');

if (!function_exists('FomatDateDisplay')) {

    function FomatDateDisplay($dateInput, $fomatDate, $defaultDisplay = '') {

        if (!isset($dateInput) || '' === $dateInput) {
            return $defaultDisplay;
        }

        try {
            return \Carbon\Carbon::parse($dateInput)->format($fomatDate);
        }
        catch (\Exception $err) {
            return $defaultDisplay;
        }
    }
}

if (!function_exists('ApprovedDisplayHtml')) {

    function ApprovedDisplayHtml($approvedValue, $defaultDisplay = '', $cssClass = '', $attrExtend = '') {

        if (!isset($approvedValue) || '' === $approvedValue) {
            return $defaultDisplay;
        }

        $htmlFomat = '<span class="label %s %s" %s>%s</span>';

        switch ($approvedValue) {
            case 0:
                return sprintf($htmlFomat, 'label-default', $cssClass, $attrExtend, 'Chưa duyệt');
            case 1:
                return sprintf($htmlFomat, 'label-success', $cssClass, $attrExtend, 'Đã duyệt');
            case 2:
                return sprintf($htmlFomat, 'label-danger', $cssClass, $attrExtend, 'Đã hủy');
            default:
                return $defaultDisplay;
        }
    }
}

if (!function_exists('AddSpecial')) {

    function AddSpecial($special) {

        $special = $special . '';
        $arrParam = func_get_args();
        array_shift($arrParam);

        if (count($arrParam) > 0) {
            $arrParam = array_filter($arrParam);
        }

        return count($arrParam) > 0 ? implode($special, $arrParam) : '';
    }
}

if (!function_exists('GenHtmlOption')) {

    function GenHtmlOption($data, $value, $display, $selected = '', $cssClass = '', $attr = '', $attr_values = '') {

        $html = '';

        if (!isset($data) || !isset($value)|| !isset($display)) {
            return $html;
        }

        $fomat = '<option value="%s" %s %s %s>%s</option>';
        $arrSelected = array();

        if (isset($selected)) {
            $arrSelected = is_array($selected) ? $selected : [$selected];
        }

        foreach ($data as $item) {
            $valueOption = is_array($item) ? $item[$value] : $item->$value;
            $displayOption = is_array($item) ? $item[$display] : $item->$display;
            $cssOption = '' !== $cssClass ? sprintf('class="%s"', $cssClass) : '';
            $selectOption = in_array($valueOption, $arrSelected) ? 'selected' : '';
            $attr_value = '' != $attr ? sprintf('%s="%s"', $attr, $item->$attr_values ? $item->$attr_values : '') : '';
            $html .= sprintf($fomat, $valueOption, $cssOption, $selectOption, $attr_value, $displayOption) . "\n";
        }

        return $html;
    }
}

if (!function_exists('GetStartMoth')) {

    function GetStartMoth() {
        return \Carbon\Carbon::now()->startOfMonth()->format(FOMAT_DISPLAY_DAY);
    }
}

if (!function_exists('GetEndMoth')) {

    function GetEndMoth() {
        return \Carbon\Carbon::now()->endOfMonth()->format(FOMAT_DISPLAY_DAY);
    }
}

if (!function_exists('GetRouter')) {

    function GetRouter($name) {
        return \Route::has($name) ? route($name) : '';
    }
}
