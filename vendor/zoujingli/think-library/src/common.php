<?php

// +----------------------------------------------------------------------
// | Library for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://library.thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github 仓库地址 ：https://github.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------

use library\tools\Crypt;
use library\tools\Csrf;
use library\tools\Data;
use library\tools\Emoji;
use library\tools\Http;
use library\tools\Node;
use think\Db;
use think\facade\Cache;
use think\facade\Middleware;
use think\facade\Response;
use think\Request;

if (!function_exists('p')) {
    /**
     * 打印输出数据到文件
     * @param mixed $data 输出的数据
     * @param boolean $force 强制替换
     * @param string|null $file 文件名称
     */
    function p($data, $force = false, $file = null)
    {
        if (is_null($file)) $file = env('runtime_path') . date('Ymd') . '.txt';
        $str = (is_string($data) ? $data : (is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true)) . PHP_EOL;
        $force ? file_put_contents($file, $str) : file_put_contents($file, $str, FILE_APPEND);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * 日期格式标准输出
     * @param string $datetime 输入日期
     * @param string $format 输出格式
     * @return false|string
     */
    function format_datetime($datetime, $format = 'Y年m月d日 H:i:s')
    {
        if (empty($datetime)) return '-';
        if (is_numeric($datetime)) {
            return date($format, $datetime);
        } else {
            return date($format, strtotime($datetime));
        }
    }
}

if (!function_exists('sysconf')) {
    /**
     * 设备或配置系统参数
     * @param string $name 参数名称
     * @param boolean $value 无值为获取
     * @return string|boolean
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function sysconf($name, $value = null)
    {
        static $data = [];
        list($field, $raw) = explode('|', "{$name}|");
        $key = md5(config('database.hostname') . '#' . config('database.database'));
        if ($value !== null) {
            Cache::tag('system')->rm("_sysconfig_{$key}");
            list($row, $data) = [['name' => $field, 'value' => $value], []];
            return Data::save('SystemConfig', $row, 'name');
        }
        if (empty($data)) {
            $data = Cache::tag('system')->get("_sysconfig_{$key}", []);
            if (empty($data)) {
                $data = Db::name('SystemConfig')->column('name,value');
                Cache::tag('system')->set("_sysconfig_{$key}", $data, 60);
            }
        }
        if (isset($data[$field])) {
            if (strtolower($raw) === 'raw') {
                return $data[$field];
            } else {
                return htmlspecialchars($data[$field]);
            }
        } else {
            return '';
        }
    }
}

if (!function_exists('systoken')) {
    /**
     * 生成CSRF-TOKEN参数
     * @param string $node
     * @return string
     */
    function systoken($node = null)
    {
        $csrf = Csrf::buildFormToken(Node::get($node));
        return $csrf['token'];
    }
}

if (!function_exists('http_get')) {
    /**
     * 以get模拟网络请求
     * @param string $url HTTP请求URL地址
     * @param array $query GET请求参数
     * @param array $options CURL参数
     * @return boolean|string
     */
    function http_get($url, $query = [], $options = [])
    {
        return Http::get($url, $query, $options);
    }
}

if (!function_exists('http_post')) {
    /**
     * 以get模拟网络请求
     * @param string $url HTTP请求URL地址
     * @param array $data POST请求数据
     * @param array $options CURL参数
     * @return boolean|string
     */
    function http_post($url, $data, $options = [])
    {
        return Http::post($url, $data, $options);
    }
}

if (!function_exists('data_save')) {
    /**
     * 数据增量保存
     * @param \think\db\Query|string $dbQuery 数据查询对象
     * @param array $data 需要保存或更新的数据
     * @param string $key 条件主键限制
     * @param array $where 其它的where条件
     * @return boolean
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function data_save($dbQuery, $data, $key = 'id', $where = [])
    {
        return Data::save($dbQuery, $data, $key, $where);
    }
}

if (!function_exists('data_batch_save')) {
    /**
     * 批量更新数据
     * @param \think\db\Query|string $dbQuery 数据查询对象
     * @param array $data 需要更新的数据(二维数组)
     * @param string $key 条件主键限制
     * @param array $where 其它的where条件
     * @return boolean
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function data_batch_save($dbQuery, $data, $key = 'id', $where = [])
    {
        return Data::batchSave($dbQuery, $data, $key, $where);
    }
}

if (!function_exists('encode')) {
    /**
     * 加密 UTF8 字符串
     * @param string $content
     * @return string
     */
    function encode($content)
    {
        return Crypt::encode($content);
    }
}

if (!function_exists('decode')) {
    /**
     * 解密 UTF8 字符串
     * @param string $content
     * @return string
     */
    function decode($content)
    {
        return Crypt::decode($content);
    }
}

if (!function_exists('emoji_encode')) {
    /**
     * 编码 Emoji 表情
     * @param string $content
     * @return string
     */
    function emoji_encode($content)
    {
        return Emoji::encode($content);
    }
}

if (!function_exists('emoji_decode')) {
    /**
     * 解析 Emoji 表情
     * @param string $content
     * @return string
     */
    function emoji_decode($content)
    {
        return Emoji::decode($content);
    }
}

if (!function_exists('emoji_clear')) {
    /**
     * 清除 Emoji 表情
     * @param string $content
     * @return string
     */
    function emoji_clear($content)
    {
        return Emoji::clear($content);
    }
}

// 注册跨域中间键
Middleware::add(function (Request $request, \Closure $next, $header = []) {
    if (($origin = $request->header('origin', '*')) !== '*') {
        $header['Access-Control-Allow-Origin'] = $origin;
        $header['Access-Control-Allow-Methods'] = 'GET,POST,PATCH,PUT,DELETE';
        $header['Access-Control-Allow-Headers'] = 'Authorization,Content-Type,If-Match,If-Modified-Since,If-None-Match,If-Unmodified-Since,X-Requested-With';
        $header['Access-Control-Expose-Headers'] = 'User-Token-Csrf';
    }
    if ($request->isOptions()) {
        return Response::create()->code(204)->header($header);
    } else {
        return $next($request)->header($header);
    }
});

// 注册系统常用指令
\think\Console::addDefaultCommands([
    'library\command\Sess',
    'library\command\task\Stop',
    'library\command\task\State',
    'library\command\task\Start',
    'library\command\task\Reset',
    'library\command\sync\Admin',
    'library\command\sync\Plugs',
    'library\command\sync\Config',
    'library\command\sync\Wechat',
    'library\command\sync\Service',
]);

// 动态加载模块配置
if (function_exists('think\__include_file')) {
    $root = rtrim(str_replace('\\', '/', env('app_path')), '/');
    foreach (glob("{$root}/*/sys.php") as $file) \think\__include_file($file);
}
