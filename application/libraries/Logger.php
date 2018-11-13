<?php

Class Logger {
    public static function Log($logText, $log_file = NULL)
    {
        $path = self::get_path($log_file);
        $data = self::set_log_data($logText);
        $fp = fopen($path, "a");
        fwrite($fp, $data);
        fclose($fp);
    }


    public static function LogDebug($logText, $log_file = NULL)
    {
        $path = self::get_path($log_file);
        $data = self::set_log_data($logText, 'DEBUG');
        $fp = fopen($path, "a");
        fwrite($fp, $data);
        fclose($fp);
    }

    public static function LogInfo($logText, $log_file = NULL)
    {
        $path = self::get_path($log_file);
        $data = self::set_log_data($logText, 'INFO');
        $fp = fopen($path, "a");
        fwrite($fp, $data);
        fclose($fp);
    }

    public static function LogWarn($logText, $log_file = NULL)
    {
        $path = self::get_path($log_file);
        $data = self::set_log_data($logText, 'WARN');
        $fp = fopen($path, "a");
        fwrite($fp, $data);
        fclose($fp);
    }

    public static function LogError($logText, $log_file = NULL)
    {
        $path = self::get_path($log_file);
        $data = self::set_log_data($logText, 'ERROR');
        $fp = fopen($path, "a");
        fwrite($fp, $data);
        fclose($fp);
    }

    public static function showLog($log_file = NULL)
    {
        return file(self::get_path($log_file));
    }

    public static function clearLog($log_file = NULL)
    {
        file_put_contents(self::get_path($log_file), "");
    }


    protected static function get_path($log_file = NULL)
    {
        if ( ! self::contains($log_file, '.txt', FALSE))
        {
            $log_file .= '.txt';
        }
        return file_exists(LOG_PATH_DIRECTORY . $log_file) && $log_file ? (LOG_PATH_DIRECTORY . $log_file) : (LOG_PATH);

    }

    protected static function contains($haystack, $needle, $caseSensitive = TRUE, $encoding = 'UTF-8')
    {
        if ($caseSensitive === FALSE)
        {
            return mb_stristr($haystack, $needle, NULL, $encoding) !== FALSE;
        }
        return mb_strstr($haystack, $needle, NULL, $encoding) !== FALSE;
    }

    protected static function set_log_data($logText, $status = ' ')
    {
        // $CI = CI_Controller::get_instance(); . ' Run times:' . $CI->benchmark->elapsed_time('total_execution_time_start','loading_time:_base_classes_end')
        return "[" . date('Y-m-d H:i:s') . "][" . $status . "] " . self::get_instance_info() . $logText . PHP_EOL;
    }

    protected static function get_call_from($path)
    {

        if (preg_match('/application\/(.+)\//', $path, $matches))
        {
            if (isset($matches[1]) && $matches[1] == 'core')
            {
                if (preg_match('/MY_(.+).php/', $path, $core_match))
                {
                    return strtolower($core_match[1]);
                }
            } elseif (isset($matches[1]))
            {
                return strtolower(rtrim($matches[1], 's'));
            }
        }
        return '';
    }

    protected static function get_instance_info()
    {
        $stack = debug_backtrace();

        // [0] last class and method, that means is as same Logger class
        foreach ($stack as $inst_id => $inst)
        {
            //will return the first not Logger class (that will be a class/method that call the log ).
            if ($inst['class'] !== $stack[0]['class'])
            {
                return $instance_info = '[' . $inst['class'] . ' ' . ucfirst(self::get_call_from($stack[$inst_id - 1]['file'])) . '][' . $inst['function'] . '] ';
            }
        }
        return '';
    }

}