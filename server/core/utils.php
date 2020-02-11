<?php

function cut ($string, $length) {
    return strlen($string) > $length ? substr($string, 0, $length) . '...' : $string;
}

function slug ($string) {
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($string)), '-');
}

function dd ($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function view ($_path, $_data = null) {
    if (!is_null($_data)) extract($_data);
    unset($_data);
    ob_start();
    eval('unset($_path) ?>' . preg_replace(
        ['/@view\((.*)\)/', '/@(.*)/', '/{{(.*)}}/U', '/{!!(.*)!!}/U'],
        ['<?php echo view($1) ?>', '<?php $1 ?>', '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\') ?>', '<?php echo $1 ?>'],
        file_get_contents(ROOT . '/views/' . str_replace('.', '/', $_path) . '.html')
    ));
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
