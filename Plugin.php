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
 * @version 1.0.9
 * @link https://letanml.xyz/
 */
class ArticlePoster_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
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

        $introduction = new Typecho_Widget_Helper_Form_Element_Text(
            'introduction',
            null,
            $options->description,
            _t('网站介绍'),
            _t('请填写网站介绍，避免海报排版错误请控制长度')
        );
        $form->addInput($introduction);

        $author = new Typecho_Widget_Helper_Form_Element_Text(
            'author',
            null,
            '',
            _t('博主名称'),
            _t('请填写博主名称')
        );
        $form->addInput($author);

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

        $button = new Typecho_Widget_Helper_Form_Element_Textarea(
            'button',
            null,
            '<div class="agree"style="margin-left:40px"><div class="article-poster-button xc-poster-button"><i class="iconfont iconhaibaofenxiang article-poster-button"></i></div><span class="post_ds">海报</span></div>',
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
        $options = Typecho_Widget::widget('Widget_Options')->plugin('ArticlePoster');
        echo '<script src="' . Helper::options()->pluginUrl . '/ArticlePoster/js/core.js"></script>';
    }
}
