<?php

// A function that minifies CSS data
function minify_css ($data){
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://cssminifier.com/raw',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [ 'Content-Type: application/x-www-form-urlencoded' ],
        CURLOPT_POSTFIELDS => http_build_query([ 'input' => $data ])
    ]);
    $minified_data = curl_exec($curl);
    curl_close($curl);
    return $minified_data;
}

// A function that minifies JavaScript data
function minify_js ($data) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://javascript-minifier.com/raw',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [ 'Content-Type: application/x-www-form-urlencoded' ],
        CURLOPT_POSTFIELDS => http_build_query([ 'input' => $data ])
    ]);
    $minified_data = curl_exec($curl);
    curl_close($curl);
    return $minified_data;
}

// This routes minifies all the resources from the resources folder
Router::get('/debug/compile', function () {
    $paths = glob(ROOT . '/resources/*');
    foreach ($paths as $path) {
        $pathinfo = pathinfo($path);
        if ($pathinfo['extension'] == 'css') {
            $data = minify_css(run_template(file_get_contents($path)));
            file_put_contents(ROOT . '/public/' . $pathinfo['filename'] . '.min.css', $data);
        }
        if ($pathinfo['extension'] == 'js') {
            $data = minify_js(run_template(file_get_contents($path)));
            file_put_contents(ROOT . '/public/' . $pathinfo['filename'] . '.min.js', $data);
        }
        if ($pathinfo['extension'] == 'json') {
            $data = json_encode(json_decode(run_template(file_get_contents($path))));
            file_put_contents(ROOT . '/public/' . $pathinfo['filename'] . '.json', $data);
        }
    }
    return 'All resources are compiled successfull';
});

// A function that drops a model and all depened on it
$models = [];
$dropped_models = [];
function drop_model($model) {
    global $models, $dropped_models;

    if (!in_array($model, $dropped_models)) {
        foreach ($models as $other_model) {
            if (in_array($model, $other_model::dependencies())) {
                drop_model($other_model);
            }
        }

        echo 'Drop ' . $model . '<br>';
        $model::drop();
        $dropped_models[] = $model;
    }
}

// A function that creates a model and all its depencies
$created_models = [];
function create_model($model) {
    global $created_models;

    if (!in_array($model, $created_models)) {
        foreach ($model::dependencies() as $other_model) {
            create_model($other_model);
        }

        echo 'Create ' . $model . '<br>';
        $model::create();
        $created_models[] = $model;
    }
}

// This route migrates the database by deleting all models and recreating them
Router::get('/debug/migrate', function () {
    global $models;

    $paths = glob(ROOT . '/models/*');
    foreach ($paths as $path) {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        if ($filename != 'fill') {
            $models[] = $filename;
        }
    }

    foreach ($models as $model) {
        drop_model($model);
    }

    foreach ($models as $model) {
        create_model($model);
    }

    require_once ROOT . '/models/fill.php';

    return 'Database migration run successfull';
});

// This route runs the cron job direct for testing
Router::get('/debug/cron', function () {
    require_once ROOT . '/cron.php';
    return 'Cron job run successfull';
});
