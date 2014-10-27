<?php

Form::macro(
    'textareaField',
    function ($name, $title, $fieldAttributes = array()) {
        $html = Form::field('textarea', $name, $title, $fieldAttributes);
        return Form::wrap($name, $title, $html);
    }
);

Form::macro(
    'colorField',
    function ($name, $title, $fieldAttributes = array(), $wrap = true) {
        $html = Form::field('color', $name, $title, $fieldAttributes);
        return $wrap ? Form::wrap($name, $title, $html) : $html;
    }
);

Form::macro(
    'textField',
    function ($name, $title, $fieldAttributes = array()) {
        $html = Form::field('text', $name, $title, $fieldAttributes);
        return Form::wrap($name, $title, $html);
    }
);

Form::macro(
    'dateField',
    function ($name, $title, $fieldAttributes = array()) {
        $html = Form::field('date', $name, $title, $fieldAttributes);
        return Form::wrap($name, $title, $html);
    }
);

Form::macro(
    'selectField',
    function ($name, $title, $options, $fieldAttributes = array()) {
        $html = Form::field('select', $name, $title, $fieldAttributes, ['values' => $options]);
        return Form::wrap($name, $title, $html);
    }
);

Form::macro(
    'checkboxField',
    function ($name, $title, $fieldAttributes = array()) {
        $html = Form::field('checkbox', $name, $title, $fieldAttributes);
        return Form::wrap($name, $title, $html);
    }
);

Form::macro(
    'imageField',
    function ($name, $title, $type = 'default', $fieldAttributes = array(), $wrap = true) {
        $html = Form::field('image', $name, $title, $fieldAttributes, ['type' => $type]);

        return $wrap ? Form::wrap($name, $title, $html) : $html;
    }
);

Form::macro(
    'fileField',
    function ($name, $title, $type = 'default', $fieldAttributes = array(), $wrap = true) {
        $html = Form::field('file', $name, $title, $fieldAttributes, ['type' => $type]);

        return $wrap ? Form::wrap($name, $title, $html) : $html;
    }
);

Form::macro(
    'geopointField',
    function ($name, $title, $fieldAttributes = array(), $settings = array()) {
        $html = Form::field('geopoint', $name, $title, $fieldAttributes);
        return Form::wrap($name, $title, $html, $settings);
    }
);

Form::macro(
    'field',
    function ($fieldType, $name, $title, $fieldAttributes = array(), $options = array()) {

        $errors = $errors = Session::get('errors', new Illuminate\Support\MessageBag);
        $classes = array_key_exists('class', $fieldAttributes) ? (array) $fieldAttributes['class'] : array();
        $value = Form::getValueAttribute($name);

        $classes[] = 'form-control';

        if ($errors->first($name)) {
            $classes[] = 'has-error';
        }

        $fieldAttributes['class'] = implode(' ', $classes);

        $html = '';
        switch ($fieldType) {
            case 'text':
                $html .= Form::text($name, null, $fieldAttributes);
                break;
            case 'date':
                $date = $value ?: 'today';
                $html .= '<div class="bfh-datepicker" data-format="y-m-d" data-name="' . $name . '" data-date="' . $date . '"></div>';
                break;
            case 'textarea':
                $html .= Form::textarea($name, null, array_merge(['rows' => 4], $fieldAttributes));
                break;
            case 'select':
                $html .= Form::select($name, $options['values'], null, $fieldAttributes);
                break;
            case 'checkbox':
                $html .= '<label class="checkbox checkbox-inline">' . Form::checkbox($name, 1, null) . '</label>';
                break;
            case 'image':
                $fieldAttributes['class'] .= ' hidden image-input';
                $button = '<i class="glyphicon glyphicon-picture"></i>';
                $button .= empty($fieldAttributes['button']) ? '' : ' ' . $fieldAttributes['button'];

                $src = $value ? '/assets/image/' . $options['type'] . '/' . $value : null;
                $html = '
                    <div class="media upload-container">
                        <span class="pull-left">
                            <a class="fancybox" href="' . $src . '"><img class="media-object" style="max-height: 200px;max-width:360px" src="' . $src . '" /></a>
                            ' . Form::text($name, null, $fieldAttributes) . '
                        </span>
                        <div class="media-body">
                            <span class="btn btn-primary btn-xs fileinput-button">
                                ' . $button . '
                                <span></span>
                                <input type="file" class="imageupload" data-type="' . $options['type'] . '">
                            </span>
                            <a href="#" class="btn btn-primary btn-xs js-upload-image-remove" title="Удалить"><span class="glyphicon glyphicon-remove"></span></a>
                        </div>
                    </div>';
                break;
            case 'file':
                $fieldAttributes['class'] .= ' hidden file-input';
                $src = $value ? '/assets/image/' . $options['type'] . '/' . $value : null;
                $html = '
                    <div class="media upload-container">
                        <span class="pull-left">
                            <a class="upload-result" href="' . $src . '">' . $value . '</a>
                            ' . Form::text($name, null, $fieldAttributes) . '
                        </span>
                        <div class="media-body">
                            <span class="btn btn-primary btn-xs fileinput-button">
                                <i class="fa fa-paperclip"></i>
                                <span></span>
                                <input type="file" class="fileupload" multiple="" data-type="' . $options['type'] . '">
                            </span>
                            <a href="#" class="btn btn-primary btn-xs js-upload-file-remove" title="Удалить"><span class="glyphicon glyphicon-remove"></span></a>
                        </div>
                    </div>';
                break;
            case 'geopoint':
                $html = '<div class="map-container">';
                $html .= '<div class="map" style="width: 100%;height:400px"></div>';
                $html .= Form::text($name, is_array($value) ? implode(':', $value) : $value, $fieldAttributes);
                $html .= '</div>';

                break;
            case 'color':
                $fieldAttributes['class'] .= ' js-color-field';
                $html = Form::text($name, null, $fieldAttributes);

                break;
            default:
                $fieldAttributes['class'] .= ' hidden';
                $html .= Form::text($name, null, $fieldAttributes);
        }

        return $html;
    }
);

Form::macro(
    'wrap',
    function ($name, $title, $fieldHtml, $settings = array()) {
        $errors = $errors = Session::get('errors', new Illuminate\Support\MessageBag);

        $html = '';
        $html .= '<div class="form-group">';
        $html .= Form::label($name, $title, array('class' => 'col-sm-3 control-label'));
        $html .= '<div class="col-sm-9">';

        $html .= $fieldHtml;

        if (array_key_exists('help', $settings)) {
            $html .= '<span class="help-block">' . $settings['help'] . '</span>';
        }

        $html .= $errors->first($name, '<p class="text-danger">:message</p>');
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
);

Form::macro('submitField', function($title = 'Сохранить'){
    return '
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <button type="submit" class="btn btn-primary">' . $title . '</button>
            </div>
        </div>';
});

Form::macro('formAddAnotherField', function($href, $title = 'Добавить ещё'){
    return '<div class="row">
            <div class="col-sm-offset-3 col-sm-9">
                <a href="' . $href . '" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> ' . $title . '</a>
            </div>
        </div>';
});

/**
 * @param int $n
 * @param str $form1 форма использующаяся в словосочетании с числительным 1 (1 яблоко, 1 квартира)
 * @param str $form2 форма использующаяся в словосочетании с числительным 2 (2 яблока, 2 квартиры)
 * @param str $form5 форма использующаяся в словосочетании с числительным 5 (5 яблок, 5 квартир)
 * @return str
 */
function plural_ru($n, $form1, $form2, $form5) {
    $n = abs($n) % 100;
    $n1 = $n % 10;

    if ($n > 10 && $n < 20) {
        return $form5;
    } elseif ($n1 > 1 && $n1 < 5) {
        return $form2;
    } elseif ($n1 == 1) {
        return $form1;
    } else {
        return $form5;
    }
}

function image_src($filename, $type) {
    return "/assets/image/{$type}/{$filename}";
}

function file_src($filename, $type) {
    return "/assets/file/{$type}/{$filename}";
}

function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
}
function mb_ucfirst($string, $encoding) {
    $strlen = mb_strlen($string, $encoding);
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, $strlen - 1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}

/**
 * Возвращает int, если не null, или null если null
 *
 * @param $value
 * @return int|null
 */
function nullable_int($value) {
    return is_null($value) ? null : (int) $value;
}