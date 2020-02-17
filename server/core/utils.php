<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['csrf_token'])) {
    Session::flash('errors', [
        'Your did not use the cross-site request forgery token'
    ]);
    Router::back();
}

if (isset($_REQUEST['csrf_token'])) {
    if (hash_equals($_REQUEST['csrf_token'], $_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    } else {
        Session::flash('errors', [
            'Your cross-site request forgery token is not valid'
        ]);
        Router::back();
    }
}

function cut ($string, $length) {
    return strlen($string) > $length ? substr($string, 0, $length) . '...' : $string;
}

function request ($key, $default = '') {
    return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
}

foreach ($_REQUEST as $key => $value) {
    Session::flash('old_' . $key, $value);
}

function old ($key, $default = '') {
    return Session::get('old_' . $key, $default);
}

function validate ($values) {
    $errors = Session::get('errors', []);
    foreach ($values as $key => $value) {
        $string = request($key);
        if (is_callable($value)) {
            $error = call_user_func($value, $key, $string);
            if (is_string($error)) {
                $errors[] = $error;
            }
        } else {
            $rules = explode('|', $value);
            foreach ($rules as $rule) {
                $parts = explode(':', $rule);
                $args = isset($parts[1]) ? explode(',', $parts[1]) : [];
                if ($parts[0] == 'required' && $string == '') {
                    $errors[] = 'The ' . $key . ' field is required';
                }
                if ($parts[0] == 'int' && !is_numeric($string) && $string != round($string)) {
                    $errors[] = 'The ' . $key . ' field must be a rounded number';
                }
                if ($parts[0] == 'float' && !is_numeric($string)) {
                    $errors[] = 'The ' . $key . ' field must be a number';
                }
                if ($parts[0] == 'email' && !filter_var($string, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'The ' . $key . ' field must be an email address';
                }
                if ($parts[0] == 'url' && !filter_var($string, FILTER_VALIDATE_URL)) {
                    $errors[] = 'The ' . $key . ' field must be an url';
                }
                if ($parts[0] == 'number_min' && $string < $args[0]) {
                    $errors[] = 'The ' . $key . ' field must be at least ' . $args[0] . ' or higher';
                }
                if ($parts[0] == 'number_max' && $string > $args[0]) {
                    $errors[] = 'The ' . $key . ' field must be a maximum of ' . $args[0] . ' or lower';
                }
                if ($parts[0] == 'number_between' && $string < $args[0] && $string > $args[1]) {
                    $errors[] = 'The ' . $key . ' field must be between ' . $args[0] . ' and ' . $args[1];
                }
                if ($parts[0] == 'confirmed' && $string != request($key . '_confirmation')) {
                    $errors[] = 'The ' . $key . ' fields must be the same';
                }
                if ($parts[0] == 'min' && strlen($string) < $args[0]) {
                    $errors[] = 'The ' . $key . ' field must be at least ' . $args[0] . ' characters long';
                }
                if ($parts[0] == 'max' && strlen($string) > $args[0]) {
                    $errors[] = 'The ' . $key . ' field can be a maximum of ' . $args[0] . ' characters';
                }
                if ($parts[0] == 'size' && strlen($string) != $args[0]) {
                    $errors[] = 'The ' . $key . ' field must be ' . $args[0] . ' characters long';
                }
                if ($parts[0] == 'same' && $string != request($args[0])) {
                    $errors[] = 'The ' . $key . ' field must be the same as the ' . $args[0] . ' field';
                }
                if ($parts[0] == 'different' && $string == request($args[0])) {
                    $errors[] = 'The ' . $key . ' field must be different as the ' . $args[0] . ' field';
                }
                if ($parts[0] == 'exists' && call_user_func($args[0] . '::select', [ (isset($args[1]) ? $args[1] : $key) => $string ])->rowCount() != 1) {
                    $errors[] = 'The ' . $key . ' field must refer to something that exists';
                }
                if ($parts[0] == 'unique' && call_user_func($args[0] . '::select', [ (isset($args[1]) ? $args[1] : $key) => $string ])->rowCount() != 0) {
                    $errors[] = 'The ' . $key . ' field must be unqiue';
                }
            }
        }
    }
    if (count($errors) > 0) {
        Session::flash('errors', $errors);
        Router::back();
    }
}

function dd ($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function view ($_path, $_data = null) {
    if (!is_null($_data)) extract($_data);
    unset($_data);
    if (is_array(Session::get('messages'))) $messages = Session::get('messages');
    if (is_array(Session::get('errors'))) $errors = Session::get('errors');
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

function get_ip () {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

/**
 * Parses a user agent string into its important parts
 *
 * @param string|null $u_agent User agent string to parse or null. Uses $_SERVER['HTTP_USER_AGENT'] on NULL
 * @return string[] an array with browser, version and platform keys
 * @throws \InvalidArgumentException on not having a proper user agent to parse.
 *
 * @author Jesse G. Donat <donatj@gmail.com>
 *
 * @link https://donatstudios.com/PHP-Parser-HTTP_USER_AGENT
 * @link https://github.com/donatj/PhpUserAgent
 *
 * @license MIT
 */
function parse_user_agent( $u_agent = null ) {
	if( $u_agent === null && isset($_SERVER['HTTP_USER_AGENT']) ) {
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
	}

	if( $u_agent === null ) {
		throw new \InvalidArgumentException('parse_user_agent requires a user agent');
    }

    if (IS_MOBILE_APP) {
        return [
            'browser' => 'Android app',
            'version' => '1.0',
            'platform' => 'Android'
        ];
    }

	$platform = null;
	$browser  = null;
	$version  = null;

	$empty = array( 'platform' => $platform, 'browser' => $browser, 'version' => $version );

	if( !$u_agent ) {
		return $empty;
	}

	if( preg_match('/\((.*?)\)/m', $u_agent, $parent_matches) ) {
		preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)
				(?:\ [^;]*)?
				(?:;|$)/imx', $parent_matches[1], $result);

		$priority = array( 'Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11' );

		$result['platform'] = array_unique($result['platform']);
		if( count($result['platform']) > 1 ) {
			if( $keys = array_intersect($priority, $result['platform']) ) {
				$platform = reset($keys);
			} else {
				$platform = $result['platform'][0];
			}
		} elseif( isset($result['platform'][0]) ) {
			$platform = $result['platform'][0];
		}
	}

	if( $platform == 'linux-gnu' || $platform == 'X11' ) {
		$platform = 'Linux';
	} elseif( $platform == 'CrOS' ) {
		$platform = 'Chrome OS';
	}

	preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
				TizenBrowser|(?:Headless)?Chrome|YaBrowser|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|Edg|CriOS|UCBrowser|Puffin|OculusBrowser|SamsungBrowser|
				Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
				Valve\ Steam\ Tenfoot|
				NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
				(?:\)?;?)
				(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
		$u_agent, $result);

	// If nothing matched, return null (to avoid undefined index errors)
	if( !isset($result['browser'][0]) || !isset($result['version'][0]) ) {
		if( preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result) ) {
			return array( 'platform' => $platform ?: null, 'browser' => $result['browser'], 'version' => isset($result['version']) ? $result['version'] ?: null : null );
		}

		return $empty;
	}

	if( preg_match('/rv:(?P<version>[0-9A-Z.]+)/i', $u_agent, $rv_result) ) {
		$rv_result = $rv_result['version'];
	}

	$browser = $result['browser'][0];
	$version = $result['version'][0];

	$lowerBrowser = array_map('strtolower', $result['browser']);

	$find = function ( $search, &$key, &$value = null ) use ( $lowerBrowser ) {
		$search = (array)$search;

		foreach( $search as $val ) {
			$xkey = array_search(strtolower($val), $lowerBrowser);
			if( $xkey !== false ) {
				$value = $val;
				$key   = $xkey;

				return true;
			}
		}

		return false;
	};

	$key = 0;
	$val = '';
	if( $browser == 'Iceweasel' || strtolower($browser) == 'icecat' ) {
		$browser = 'Firefox';
	} elseif( $find('Playstation Vita', $key) ) {
		$platform = 'PlayStation Vita';
		$browser  = 'Browser';
	} elseif( $find(array( 'Kindle Fire', 'Silk' ), $key, $val) ) {
		$browser  = $val == 'Silk' ? 'Silk' : 'Kindle';
		$platform = 'Kindle Fire';
		if( !($version = $result['version'][$key]) || !is_numeric($version[0]) ) {
			$version = $result['version'][array_search('Version', $result['browser'])];
		}
	} elseif( $find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS' ) {
		$browser = 'NintendoBrowser';
		$version = $result['version'][$key];
	} elseif( $find('Kindle', $key, $platform) ) {
		$browser = $result['browser'][$key];
		$version = $result['version'][$key];
	} elseif( $find('OPR', $key) ) {
		$browser = 'Opera';
		$version = $result['version'][$key];
	} elseif( $find('Opera', $key, $browser) ) {
		$find('Version', $key);
		$version = $result['version'][$key];
	} elseif( $find('Puffin', $key, $browser) ) {
		$version = $result['version'][$key];
		if( strlen($version) > 3 ) {
			$part = substr($version, -2);
			if( ctype_upper($part) ) {
				$version = substr($version, 0, -2);

				$flags = array( 'IP' => 'iPhone', 'IT' => 'iPad', 'AP' => 'Android', 'AT' => 'Android', 'WP' => 'Windows Phone', 'WT' => 'Windows' );
				if( isset($flags[$part]) ) {
					$platform = $flags[$part];
				}
			}
		}
	} elseif( $find('YaBrowser', $key, $browser) ) {
		$browser = 'Yandex';
		$version = $result['version'][$key];
	} elseif( $find(array( 'Edge', 'Edg' ), $key, $browser) ) {
		$browser = 'Edge';
		$version = $result['version'][$key];
	} elseif( $find(array( 'IEMobile', 'Midori', 'Vivaldi', 'OculusBrowser', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome', 'HeadlessChrome' ), $key, $browser) ) {
		$version = $result['version'][$key];
	} elseif( $rv_result && $find('Trident', $key) ) {
		$browser = 'MSIE';
		$version = $rv_result;
	} elseif( $find('UCBrowser', $key) ) {
		$browser = 'UC Browser';
		$version = $result['version'][$key];
	} elseif( $find('CriOS', $key) ) {
		$browser = 'Chrome';
		$version = $result['version'][$key];
	} elseif( $browser == 'AppleWebKit' ) {
		if( $platform == 'Android' ) {
			$browser = 'Android Browser';
		} elseif( strpos($platform, 'BB') === 0 ) {
			$browser  = 'BlackBerry Browser';
			$platform = 'BlackBerry';
		} elseif( $platform == 'BlackBerry' || $platform == 'PlayBook' ) {
			$browser = 'BlackBerry Browser';
		} else {
			$find('Safari', $key, $browser) || $find('TizenBrowser', $key, $browser);
		}

		$find('Version', $key);
		$version = $result['version'][$key];
	} elseif( $pKey = preg_grep('/playstation \d/i', $result['browser']) ) {
		$pKey = reset($pKey);

		$platform = 'PlayStation ' . preg_replace('/\D/', '', $pKey);
		$browser  = 'NetFront';
    }

	return array( 'platform' => $platform ?: null, 'browser' => $browser ?: null, 'version' => $version ?: null );
}
