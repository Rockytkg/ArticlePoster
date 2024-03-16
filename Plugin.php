<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 为文章生成海报，原作者 MoLeft：http://www.moleft.cn/，本插件由浅梦修改
 *
 * @package ArticlePoster
 * @author MoLeft
 * @author 浅梦
 * @version 1.1.0
 * @link https://letanml.xyz/
 */
class ArticlePoster_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        // 检查GD库是否启用
        if (!extension_loaded('gd')) {
            throw new Exception('插件激活失败，因为GD库没有启用');
        }

        Helper::addRoute('ArticlePosterAction_make', '/ArticlePoster/make', 'ArticlePoster_Action', 'make');
        Typecho_Plugin::factory('Widget_Archive')->header = array('ArticlePoster_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('ArticlePoster_Plugin', 'footer');
        return '插件已激活，请设置相关信息';
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
        Helper::removeRoute('ArticlePosterAction_make');
        return '插件已禁用';
    }

    /**
     * 获取插件配置面板
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $options = Helper::options();
        $sitename = new Typecho_Widget_Helper_Form_Element_Text(
            'sitename',
            null,
            $options->title,
            _t('网站名称'),
            _t('请填写网站名称，避免海报排版错误请控制长度')
        );
        $form->addInput($sitename);

        $siteNameSize = new Typecho_Widget_Helper_Form_Element_Text(
            'siteNameSize',
            null,
            30,
            _t('网站名称字体大小'),
            _t('请填写网站名称字体大小,过大会导致排版错误')
        );
        $form->addInput($siteNameSize);

        $introduction = new Typecho_Widget_Helper_Form_Element_Text(
            'introduction',
            null,
            $options->description,
            _t('网站介绍'),
            _t('请填写网站介绍，避免海报排版错误请控制长度')
        );
        $form->addInput($introduction);

        $introductionSize = new Typecho_Widget_Helper_Form_Element_Text(
            'introductionSize',
            null,
            15,
            _t('网站介绍字体大小'),
            _t('请填写网站介绍字体大小,过大会导致排版错误')
        );
        $form->addInput($introductionSize);

        $author = new Typecho_Widget_Helper_Form_Element_Text(
            'author',
            null,
            '',
            _t('博主名称'),
            _t('请填写博主名称')
        );
        $form->addInput($author);

        $authorSize = new Typecho_Widget_Helper_Form_Element_Text(
            'authorSize',
            null,
            17,
            _t('博主名称字体大小'),
            _t('请填写博主名称字体大小,过大会导致排版错误')
        );
        $form->addInput($authorSize);

        $qq = new Typecho_Widget_Helper_Form_Element_Text(
            'qq',
            null,
            '',
            _t('博主扣扣'),
            _t('请填写博主扣扣，以显示头像')
        );
        $form->addInput($qq);

        $content = new Typecho_Widget_Helper_Form_Element_Text(
            'content',
            null,
            '',
            _t('自定义摘要字段'),
            _t('请填写自定义摘要字段，留空则使用文章摘要）')
        );
        $form->addInput($content);

        $contentSize = new Typecho_Widget_Helper_Form_Element_Text(
            'contentSize',
            null,
            15,
            _t('自定义摘要字段字体大小'),
            _t('请填写自定义摘要字段字体大小,过大会导致排版错误')
        );
        $form->addInput($contentSize);

        $titleSize = new Typecho_Widget_Helper_Form_Element_Text(
            'titleSize',
            null,
            30,
            _t('文章标题字体大小'),
            _t('请填写文章标题字体大小,过大会导致排版错误')
        );
        $form->addInput($titleSize);

        $headimage = new Typecho_Widget_Helper_Form_Element_Text(
            'headimage',
            null,
            'https://tu.ltyuanfang.cn/api/fengjing.php',
            _t('海报头部图片'),
            _t('请填写海报头部图片的URL，推荐填写随机图片API')
        );
        $form->addInput($headimage);

        $button = new Typecho_Widget_Helper_Form_Element_Textarea(
            'button',
            null,
            '<button class="article-poster-button">海报</button>',
            _t('自定义按钮样式'),
            _t('根据自己模板的按钮样式来自定义分享按钮的样式，在class里面加入<b style="color: #ff0000;">article-poster-button</b>即可使用')
        );
        $form->addInput($button);
    }

    /**
     * 个人用户的配置面板
     *
     * @param Form $form
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 插件实现方法
     */
    public static function render()
    {
    }

    public static function button($cid)
    {
        $options = Helper::options();
        $config = $options->plugin('ArticlePoster');
        echo '<!-- ArticlePoster -->';
        echo $config->button;
        echo '<div data-id="' . $cid . '" class="article-poster action action-poster"><div class="poster-popover-mask" data-event="poster-close"></div><div class="poster-popover-box"><a class="poster-download" data-event="poster-download" data-url="">下载海报</a><img class="article-poster-images"/></div></div>';
    }

    public static function header()
    {
        $options = Helper::options();
        echo '<link rel="stylesheet" href="' . $options->pluginUrl . '/ArticlePoster/css/core.css">';
    }

    public static function footer()
    {
        echo '<script src="' . Helper::options()->pluginUrl . '/ArticlePoster/js/core.js"></script>';
    }
}
