<?php

class Tools
{
    public static function getValue($key, $defaultValue = null)
    {
        if (array_key_exists($key, $_GET)) {
            return self::safeValue($_GET[$key]);
        }
        return $defaultValue;
    }

    public static function getValueInt($key, $defaultValue = 0)
    {
        if (array_key_exists($key, $_GET)) {
            return intval($_GET[$key]);
        }
        return intval($defaultValue);
    }

    public static function postValue($key, $defaultValue = null)
    {
        if (array_key_exists($key, $_POST)) {
            return self::safeValue($_POST[$key]);
        }
        return $defaultValue;
    }

    public static function postValueInt($key, $defaultValue = 0)
    {
        if (array_key_exists($key, $_POST)) {
            return intval($_POST[$key]);
        }
        return intval($defaultValue);
    }

    public static function requestValue($key, $defaultValue = null)
    {
        if (array_key_exists($key, $_REQUEST)) {
            return self::safeValue($_REQUEST[$key]);
        }
        return $defaultValue;
    }

    public static function requestValueInt($key, $defaultValue = 0)
    {
        if (array_key_exists($key, $_REQUEST)) {
            return intval($_REQUEST[$key]);
        }
        return intval($defaultValue);
    }

    public static function cookieValue($key, $defaultValue = null)
    {
        if (array_key_exists($key, $_COOKIE)) {
            return self::safeValue($_COOKIE[$key]);
        }
        return $defaultValue;
    }

    public static function cookieValueInt($key, $defaultValue = 0)
    {
        if (array_key_exists($key, $_COOKIE)) {
            return intval($_COOKIE[$key]);
        }
        return intval($defaultValue);
    }

    public static function show($code, $msg, $data)
    {
        $response['code'] = $code;
        $response['msg'] = $msg;
        $response['data'] = $data;
        $responseJson = json_encode($response);
        if (empty(self::getValue('callback'))) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                header('Content-Type: text/plain; charset=UTF-8');
            } else {
                header('Content-Type: application/json; charset=UTF-8');
            }
            echo $responseJson;
            exit;
        }
        $callback = self::getValue('callback');
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9_\.]+$/", $callback)) {
            header('Content-Type: text/html; charset=UTF-8');
            exit('callback param invalid');
        }
        if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            header('Content-Type: text/html; charset=UTF-8');
            $result = '<script>document.domain="zerorei.top";';
            $result .= "parent.{$callback}({$responseJson});</script>";
            echo $result;
        } else {
            if (isset($_SERVER['HTTP_USER_AGENT']) &&
                stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
                header('Content-Type: text/javascript; charset=UTF-8');
            } else {
                header('Content-Type: application/javascript; charset=UTF-8');
            }
            echo "{$callback}({$responseJson});";
        }
        exit;
    }

    private static function safeValue($data)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $resKey = self::safeValue($key);
                $result[$resKey] = self::safeValue($value);
            }
        } else {
            $result = trim($data);
            $result = filter_var($result, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
            if (!get_magic_quotes_gpc()) {
                $result = addslashes($result);
            }
        }
        return $result;
    }

    public static function log($logName, $logContent)
    {
        $logDir = dirname(__FILE__) . '/log/';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . $logName . '.' . date("Ymd") . ".log";
        file_put_contents($logFile, $logContent, FILE_APPEND | LOCK_EX);
    }
}
