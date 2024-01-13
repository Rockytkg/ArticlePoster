<?php
include __DIR__ . '/inc/phpQrcode.class.php';
include __DIR__ . '/inc/poster.class.php';
/**
 * 通过cURL获取内容
 * @param string $url 要访问的URL。
 * @return string 返回从URL获取的内容。如果cURL执行失败，则返回false。
 */
header("Content-type: text/html; charset=utf-8");
function get_curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $ua = 0, $nobody = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $httpheader[] = "Accept:*/*";
    $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
    $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
    $httpheader[] = "Connection:close";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if ($header) {
        curl_setopt($ch, CURLOPT_HEADER, true);
    }
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }
    if ($referer) {
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if ($ua) {
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    } else {
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0");
    }
    if ($nobody) {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
    }
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}

/**
 * 生成海报
 * @param array $postData 文章信息数组
 * @return array 海报生成结果
 */
function generatePoster($postData)
{
    // 从 postData 中提取信息
    $info = array(
        'sitename' => $postData['sitename'], // 站点名称
        'siteintr' => $postData['introduction'], // 站点简介
        'artclename' => $postData['title'], // 文章标题
        'artclecont' => $postData['content'], // 文章摘要
        'artcletime' => $postData['time'], // 文章发布时间
        'artcleauth' => $postData['author'], // 文章作者
        'artcleqq' => $postData['qq'], // 文章作者QQ
        "qrCodeData" => QRcode::pngData($postData['link'], 13), // 文章链接
        "read_time" => $postData['readingTime'], // 预计阅读时间
    );

    // 根据 $info 配置生成海报
    $config = array(
        'bg_url' => __DIR__ . '/img/background.png',
        'text' => array(
            array(
                'text' => date("d", strtotime($info['artcletime'])),
                'left' => 70,
                'top' => 450,
                'width' => 650,
                'fontSize' => 80,
                'fontColor' => '255,255,255',
                'angle' => 0,
            ),
            array(
                'text' => '-------------',
                'left' => 70,
                'top' => 470,
                'width' => 650,
                'fontSize' => 15,
                'fontColor' => '255,255,255',
                'angle' => 0,
            ),
            array(
                'text' => date("Y", strtotime($info['artcletime'])) . '/' . date("m", strtotime($info['artcletime'])),
                'left' => 80,
                'top' => 490,
                'width' => 650,
                'fontSize' => 20,
                'fontColor' => '255,255,255',
                'angle' => 0,
            ),
            array(
                'text' => $info['artclename'],
                'left' => 50,
                'top' => 650,
                'width' => 650,
                'fontSize' => 30,
                'fontColor' => '0,0,0',
                'angle' => 0,
            ),
            array(
                'text' => $info['artclecont'],
                'left' => 50,
                'top' => 700,
                'width' => 650,
                'fontSize' => 15,
                'fontColor' => '85,85,85',
                'angle' => 0,
            ),
            array(
                'text' => '文章编辑：' . $info['artcleauth'],
                'left' => 170,
                'top' => 940,
                'width' => 650,
                'fontSize' => 17,
                'fontColor' => '0,0,0',
                'angle' => 0,
            ),
            array(
                'text' => '预计阅读：' . $info["read_time"] . '分钟',
                'left' => 170,
                'top' => 970,
                'width' => 650,
                'fontSize' => 17,
                'fontColor' => '0,0,0',
                'angle' => 0,
            ),
            array(
                'text' => '---------------------------------------------------------------------------',
                'left' => 0,
                'top' => 1050,
                'width' => 750,
                'fontSize' => 15,
                'fontColor' => '128,128,128',
                'angle' => 0,
            ),
            array(
                'text' => $info['sitename'],
                'left' => 100,
                'top' => 1170,
                'width' => 325,
                'fontSize' => 30,
                'fontColor' => '0,0,0',
                'angle' => 0,
            ),
            array(
                'text' => $info['siteintr'],
                'left' => 100,
                'top' => 1210,
                'width' => 325,
                'fontSize' => 15,
                'fontColor' => '85,85,85',
                'angle' => 0,
            ),
            array(
                'text' => '扫一扫，看文章',
                'left' => 540,
                'top' => 1250,
                'width' => 130,
                'fontSize' => 10,
                'fontColor' => '85,85,85',
                'angle' => 0,
            ),
        ),
        'image' => array(
            array(
                'url' => 0,
                'stream' => get_curl('https://tu.ltyuanfang.cn/api/fengjing.php'),
                'left' => 0,
                'top' => 0,
                'right' => 0,
                'bottom' => 0,
                'width' => 750,
                'height' => 550,
                'radius' => 0,
                'opacity' => 100
            ),
            array(
                'url' => 0,
                'stream' => get_curl('https://q1.qlogo.cn/g?b=qq&nk=' . $info['artcleqq'] . '&s=640'),
                'left' => 50,
                'top' => 900,
                'right' => 0,
                'bottom' => 0,
                'width' => 100,
                'height' => 100,
                'radius' => 50,
                'opacity' => 100
            ),
            array(
                'url' => 0,
                'stream' => $info["qrCodeData"],
                'left' => 520,
                'top' => 1100,
                'right' => 0,
                'bottom' => 0,
                'width' => 130,
                'height' => 130,
                'radius' => 0,
                'opacity' => 100
            ),
        )
    );

    // 海报生成逻辑
    poster::setConfig($config);
    $res = poster::make();
    poster::clear();

    if (!$res) {
        return array('code' => 400, 'msg' => poster::$errMsg);
    } else {
        return array('code' => 200, 'msg' => '生成成功', 'img' => base64_encode($res));
    }
}
