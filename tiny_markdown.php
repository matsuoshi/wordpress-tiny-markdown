<?php
/*
Plugin Name: Tiny Markdown
Description: tiny markdown parser (based on parsedown)
Author: h.matsuo
*/

require_once __DIR__ . '/parsedown/parsedown.php';

/**
 * Tiny Markdown
 */
class TinyMarkdown
{
    protected $pd;

    public function __construct()
    {
        // クウォート等の自動変換をOFFに
        remove_filter('the_content', 'wpautop');
        remove_filter('the_content', 'wptexturize');
        remove_filter('comment_text', 'wptexturize');

        // markdown 初期化
        $this->pd = new Parsedown();
        $this->pd->setBreaksEnabled(true); // 改行だけで改行する

        // markdown呼び出しフィルタ設定
        add_filter('the_content', array(&$this, 'parsedown'));
        add_filter('comment_text', array(&$this, 'parsedown'));

        // 管理画面の投稿時、テキストエディタしか使えなくする
        add_filter('wp_default_editor', function () {
                return 'html';
            });

        add_action('admin_print_styles', function () {
                echo <<< _STYLE_
<style>
    #content-tmce { display:none; }
    #ed_toolbar input { visibility: hidden; }
</style>
_STYLE_;
            });
    }

    public function parsedown($content)
    {
        return '<div class="markdown">' . $this->pd->text($content) . '</div>';
    }
}

new TinyMarkdown();
