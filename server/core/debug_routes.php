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

// This route migrates the database by deleting all models and recreating them
Router::get('/debug/migrate', function () {
    $paths = glob(ROOT . '/models/*');
    foreach ($paths as $path) {
        $class = pathinfo($path, PATHINFO_FILENAME);
        call_user_func($class . '::drop');
        call_user_func($class . '::create');
        if (method_exists($class, 'fill')) {
            call_user_func($class . '::fill');
        }
    }
    return 'Database migration run successfull';
});

// This route runs the cron job direct for testing
Router::get('/debug/cron', function () {
    require_once ROOT . '/cron.php';
    return 'Cron job run successfull';
});
