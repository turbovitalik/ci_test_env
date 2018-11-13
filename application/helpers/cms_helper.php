<?php

/**
 * @param $message
 * @param bool $url
 */
function flash_success($message, $url = FALSE)
{

    $CI =& get_instance();

    $CI->session->set_flashdata('success', $message);

    if ($url) redirect($url);

}

/**
 * @param $message
 * @param bool $url
 */
function flash_error($message, $url = FALSE)
{

    $CI =& get_instance();

    $CI->session->set_flashdata('error', $message);

    if ($url) redirect($url);

}

function lang($line)
{
    /*
    if($_SERVER['REMOTE_ADDR'] == '91.225.123.142' && $line == 'js'){

    }
    */
    /*
    if (!isset(get_instance()->lang->language[$line])) {

        $lang = [];
        $lang_path = APPPATH . 'language/english/' . LANGUAGE_DEFAULT_FILE . '_lang.php';

        if (file_exists($lang_path)) {
            include $lang_path;
            get_instance()->lang->language = array_merge($lang,get_instance()->lang->language);
        }
    }
    */
    $line = get_instance()->lang->line($line);
    return $line;
}

function meta_header($header = 'title')
{
    $CI =& get_instance();
    $segs = $CI->uri->segment_array();

    $pagetitle = '';
    if ($segs[2] == 'open')
    {
        $pagetitle = lang($segs[3]);
        if (($pagetitle))
        {
            $pagetitle .= ' - ';
        }
    }
    $title = (($pagetitle) ? ($pagetitle) : '') . lang($segs[2] . '._' . $header);


    return $title;
//    $line = get_instance()->lang->line($line);
//    return $line;
}

function meta_og_image($page = 'main')
{
    $CI =& get_instance();
    $segs = $CI->uri->segment_array();

    $pagetitle = $page;
    if ($segs[2] != '')
    {
        $pagetitle = ($segs[2]);
    }
    if ($segs[2] == 'open')
    {
        $pagetitle = ($segs[3]);
    }

    return $pagetitle;
}


function file_get_contents_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function json_get_contents_curl($url)
{

    $content = FALSE;

    $data = file_get_contents_curl($url);

    if ($data)
        $content = json_decode($data, TRUE);

    return $content;
}


function contains($haystack, $needle)
{
    if (strpos($haystack, $needle) === FALSE)
        return FALSE;
    else
        return TRUE;
}

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i)
    {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}



function mb_pathinfo($filepath)
{
    if ($filepath == '') return FALSE;

    preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $filepath, $m);

    if ( ! $m) return FALSE;
    if ($m[1]) $ret['dirname'] = $m[1];
    if ($m[2]) $ret['basename'] = $m[2];
    if ($m[5]) $ret['extension'] = $m[5];
    if ($m[3]) $ret['filename'] = $m[3];

    return $ret;
}

function pagination($url = FALSE, $total = 0, $per_page = 10)
{
    $CI =& get_instance();

    $CI->load->library('pagination');
    $config = array();

    $config['use_page_numbers'] = FALSE;

    $config['base_url'] = site_url($url);
    $config['per_page'] = $per_page;
    $config['total_rows'] = $total;
    $config['anchor_class'] = 'class="active" ';
    $config['full_tag_open'] = '<ul class="pagination">';
    $config['full_tag_close'] = '</ul>';
    $config['cur_tag_open'] = '<li class="active"><a href="#">';
    $config['cur_tag_close'] = '</a></li>';

    $config['num_tag_open'] = $config['next_tag_open'] = $config['prev_tag_open'] = $config['first_tag_open'] = $config['last_tag_open'] = '<li>';
    $config['num_tag_close'] = $config['next_tag_close'] = $config['prev_tag_close'] = $config['first_tag_close'] = $config['last_tag_close'] = '</li>';

    $config['prev_link'] = '&laquo;';
    $config['next_link'] = '&raquo;';
    $config['last_link'] = 'в конец &rsaquo;';
    $config['first_link'] = '&lsaquo; в начало';

    $CI->pagination->initialize($config);
    $pagination = $CI->pagination->create_links();
    return $pagination;
}

function clear_string($string)
{
    $string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);

    return $string;
}

// удаление запрещённых ников
function remove_bad_word($string)
{
    $string = strtolower($string);
    $string = str_replace(['admin', 'moder', 'moderator', 'adm', 'm0der', 'admln', 'fuck'], '', $string);
    return $string;
}


function admin_spy_log($comment = "")
{

    $CI =& get_instance();

    $ip = $CI->input->ip_address();

    $steam_id = $CI->user->get_steam_id();

    file_put_contents(APPPATH . 'logs/spy_' . date("Y-m-d") . '.log', date("Y-m-d H:i") . "[$steam_id][$ip] $comment\n", FILE_APPEND);

}

function number_format_drop_zero_decimals($n, $n_decimals = 2)
{
    return ((floor($n) == round($n, $n_decimals)) ? number_format($n) : number_format($n, $n_decimals));
}


/**
 * @desc  Purge Cache on CF
 * @link https://api.cloudflare.com/#zone-purge-individual-files-by-url-and-cache-tags
 * @param   string $path path that goes after the URL fx. "/user/login"
 * @param   array $json If you need to send some json with your request. For me delete requests are always blank
 * @return  Obj    $result HTTP response from REST interface in JSON decoded.
 */
function curl_purge_cache($zone = FALSE, $json = array('files' => []))
{
    if ( ! defined('CLOUDFLATE_ZONE') || ! defined('CLOUDFLATE_AUTH_EMAIL') || ! defined('CLOUDFLATE_AUTH_KEY'))
    {
        return FALSE;
    }

    if ( ! $zone) $zone = CLOUDFLATE_ZONE;

    $url = 'https://api.cloudflare.com/client/v4/zones/' . $zone . '/purge_cache';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Auth-Email: " . CLOUDFLATE_AUTH_EMAIL,
        "X-Auth-Key: " . CLOUDFLATE_AUTH_KEY
    ]);

    $result = curl_exec($ch);
    $result = json_decode($result);
    curl_close($ch);

    return $result;
}

// округление с плавующей точкой в меньшую сторону
function floor_float($sum)
{
    return floor($sum * 100) / 100;
}

function get_from_cookie($key, $default = FALSE)
{
    $CI =& get_instance();

    $data = $CI->input->cookie($key, TRUE);

    if ($data == NULL) return $default;

    return $data;
}

function is_admin_ips()
{
    $CI =& get_instance();
    $ips = (array)$CI->config->item('admin_ips');
    if(empty($ips))
    {
        return FALSE;
    }
    return in_array($CI->input->ip_address(), $ips);
}

function remove_directory($path)
{
    $files = glob($path . '/*');
    foreach ($files as $file)
    {
        is_dir($file) ? remove_directory($file) : unlink($file);
    }
    rmdir($path);
    return;
}


/*
    Вывод ссылки на нужный поддомен игры
    @param string game короткое название игры
    @param string path путь после домена
    @param bool cache кешировать файл по его дате правки
    @return string ссылка
*/
function base_domain($game = 'csgo', $path='', $cache=false)
{
    $CI =& get_instance();
    
    $projects = $CI->config->item('projects');
    $games = [];

    foreach($projects as $key => $project)
    {
        if($project['enabled'] == TRUE)
        {
            $games[$key] = ltrim($project['url'], '/');
        }
    }
    
    if($cache == TRUE && (contains($path, '.css') || contains($path, '.js')))
    {
        $file_version = '';

        if(file_exists(FCPATH . '../public/' . $path))
        {
            $file_version = filemtime(FCPATH . '../public/' . $path);

            return $games[$game] . ltrim($path, '/') . '?v=' . $file_version;
        }

        if(file_exists(FCPATH . $path))
        {
            $file_version = filemtime(FCPATH . $path);

            return $games[$game] . ltrim($path, '/') . '?v=' . $file_version;
        }

    }

    return $games[$game] . ltrim($path, '/');
}


/*
    Функция случайности с приоритетом
    @author Dmitriy Nyashkin
    @param (array) arr  - Array with priority key 
    @param (string) priority_key  - Array key for use priority
    @return array - single array
*/
function random_priority($arr, $priority_key = 'priority')
{
    $keys = array();
    foreach($arr as $key=>$val)
        $keys = array_merge($keys, array_fill(0, intval($val[$priority_key]), $key));
    return $arr[ $keys[ array_rand($keys) ] ];
}

/*
    Функция для назначания ключам массива идентификатор для удобной выборки

    @param (array) $array массив значений
    @param (string) $key_col ключ массива который будет идентификатором
*/
function reassign_key($array, $key_col = 'id', $val_col = FALSE)
{
    $new_array = [];

    foreach($array as $val)
    {
        if($val_col)
        {
            $new_array[$val[$key_col]] = $val[$val_col];
        }
        else
        {
            $new_array[$val[$key_col]] = $val;
        }
    }

    return $new_array;
}


/*
    определение победы или проигрыша с учётом шанса и семени

    @param $chance (int) шанс победы
    @param $seed (string) семя случайности (не обязательно)
    @return (bool)
*/
function random_chance_with_seed($chance, $seed = FALSE, $get_value=FALSE)
{
    if($seed)
    {
        mt_srand(crc32($seed));
    }
    else 
    {
        mt_srand(crc32(microtime()));
    }
    
    if($get_value == TRUE) {
        return mt_rand(1, 100);
    }

    return (mt_rand(1, 100) <= $chance);
}


function arr_by_key($data, $key){

    $result = [];
    foreach ($data as $item){
        if(!isset($item[$key])){
            throw new Exception('error key or data');
        }
        $result[$item[$key]] = $item;
    }

    return $result;
}

function arr_keys($data, $key){

    $result = [];
    foreach ($data as $item){
        if(!isset($item[$key])){
            throw new Exception('error key or data');
        }
        $result[] = $item[$key];
    }

    return $result;
}

function var_dump_pretty($data){
    print("<pre>".print_r($data,true)."</pre>");
}

function cyr_icon($price = NULL)
{
    if($price) return '<i class="fa fa-diamond" aria-hidden="true"></i> ' . $price;
    return '<i class="fa fa-diamond" aria-hidden="true"></i>';
}