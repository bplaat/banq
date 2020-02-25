<?php

// A function whichs minifies the given HTML code
function minify_html ($data) {
    return preg_replace(
        [ '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s' ],
        [ '>', '<', '\\1' ],
        $data
    );
}

// A function whichs runs the template with the data given
function run_template ($_template, $_data = null) {
    if (!is_null($_data)) extract($_data);
    unset($_data);
    if (is_array(Session::get('messages'))) $messages = Session::get('messages');
    if (is_array(Session::get('errors'))) $errors = Session::get('errors');
    ob_start();
    eval('unset($_template) ?>' . preg_replace(
        ['/@view\((.*)\)/', '/\\\@/', '/@(.*)/', '/\$\$\$/', '/{{(.*)}}/U', '/{!!(.*)!!}/U'],
        ['<?php echo view($1) ?>', '$$$', '<?php $1 ?>', '@', '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\') ?>', '<?php echo $1 ?>'],
        $_template
    ));
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

// A wrapper function whichs loads a view runs the template with the data and returns the minified HTML code
function view ($_path, $_data = null) {
    return minify_html(run_template(file_get_contents(ROOT . '/views/' . str_replace('.', '/', $_path) . '.html'), $_data));
}
