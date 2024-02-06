<?php

include __DIR__ . '/service/api.php';
/**
 * 文章海报生成
 */
class ArticlePoster_Action extends Typecho_Widget implements Widget_Interface_Do
{
    private $db;
    private $info;

    public function __construct()
    {
        $pluginOptions = Typecho_Widget::widget('Widget_Options')->plugin('ArticlePoster');
        $infoKeys = ['sitename', 'siteNameSize', 'introduction', 'introductionSize', 'author', 'authorSize', 'qq', 'contentSize', 'titleSize', 'headimage'];
        foreach ($infoKeys as $key) {
            $this->info[$key] = $pluginOptions->{$key};
        }

        $this->db = Typecho_Db::get();
    }

    /**
     * 生成海报
     */
    public function make()
    {
        $cid = filter_input(INPUT_GET, 'cid', FILTER_VALIDATE_INT, [
            'options' => [
                'default' => null, // 或其他默认值
                'min_range' => 1
            ]
        ]);

        if ($cid === null) {
            $this->export('参数错误或缺少参数', 400);
            return; // 提早返回避免进一步处理
        }

        // 防止路径遍历：确保$cid仅为数字
        $cid = intval($cid); // 重复验证确保$cid为整数

        // 构建安全路径，确保文件名仅包含安全字符
        $imgPath = __DIR__ . '/poster/cid-' . basename($cid) . '.jpg';
        if (file_exists($imgPath)) {
            $imgData = file_get_contents($imgPath);
            $base64 = "data:image/jpeg;base64," . base64_encode($imgData);
            $this->export('生成成功', 200, $base64);
        } else {
            $post = array_merge($this->getPostInfo($cid), $this->info);

            $img = @generatePoster($post);

            if ($img['code'] !== 200) {
                $this->export('生成失败', 500);
            } else {
                $imgUrl = $this->saveImage($img['img'], $cid);
                $this->export('生成成功', 200, $imgUrl);
            }
        }
    }


    /**
     * 获取文章信息
     * @param int $cid 文章cid
     * @return array 文章信息数组
     */
    private function getPostInfo($cid)
    {
        $post = Typecho_Widget::widget('Widget_Abstract_Contents')->filter($this->db->fetchRow($this->db->select()->from('table.contents')->where('table.contents.cid = ?', $cid)));
        if (!$post) return false;

        $archive = Typecho_Widget::widget('Widget_Archive', 'pageSize=1&type=post', 'cid=' . $cid);
        $archive->to($permalink);

        $excerpt = Typecho_Common::subStr(preg_replace('/\s+/', ' ', strip_tags($archive->isMarkdown ? $archive->markdown($post['text']) : $archive->autoP($post['text']))), 0, 200, "...");
        $content = $this->getPostSummary($excerpt, $cid);

        return array(
            'title' => $post['title'],
            'content' => $content,
            'time' => date("Y-m-d H:i:s", $post['created']),
            'link' => $permalink->permalink,
            'readingTime' => round(mb_strlen(strip_tags($post['text']), 'UTF-8') / 450, 1)
        );
    }

    /**
     * 获取文章摘要
     * @param string $postText 文章默认摘要内容
     * @param int $cid 文章cid
     * @return string 文章摘要
     */
    private function getPostSummary($postText, $cid)
    {
        $articlePosterContent = Typecho_Widget::widget('Widget_Options')->plugin('ArticlePoster')->content;

        if (empty($articlePosterContent)) {
            return $postText;
        } else {
            $field = $this->db->fetchRow($this->db->select('str_value')->from('table.fields')->where('cid = ? AND name = ?', $cid, $articlePosterContent)->limit(1));
            return mb_substr(!empty($field['str_value']) ? $field['str_value'] : $postText, 0, 200) . '...';
        }
    }

    /**
     * 保存图片
     * @param string $imgData base64编码的图片
     * @param int $cid 文章cid
     * @return string base64图片地址
     */
    function saveImage($imgData, $cid)
    {
        $imgName = 'cid-' . $cid . '.jpg';
        $imgPath = __DIR__ . '/poster/' . $imgName;
        // 检查文件夹是否存在
        if (!is_dir(__DIR__ . '/poster')) {
            mkdir(__DIR__ . '/poster');
        }
        file_put_contents($imgPath, $imgData);
        return "data:image/jpeg;base64," . base64_encode($imgData);
    }

    /**
     * 返回信息
     * @param int $status 状态码
     * @param string $msg 信息
     * @param string $data 数据
     */
    public function export($msg, $status = 200, $data = null)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        $response = array(
            'code' => $status,
            'msg' => $msg
        );
        if ($data !== null) {
            $response['data'] = $data;
        }
        $json = json_encode($response, JSON_UNESCAPED_UNICODE);
        echo $json;
        exit;
    }

    public function action()
    {
        $this->on($this->request);
    }
}
