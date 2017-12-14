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
        // 改行だけで改行する + URLをリンクしない
        $this->pd->setBreaksEnabled(true)->setUrlsLinked(false);

        // markdown呼び出しフィルタ設定
        add_filter('the_content', array(&$this, 'parsedown'));
        add_filter('comment_text', array(&$this, 'parsedown'));
    }

    public function parsedown($content)
    {
        // embed時の崩れ対応
        $content = str_replace('</blockquote><iframe', "</blockquote>\n<iframe", $content);

        // parse
        $content = '<div class="markdown">' . $this->pd->text($content) . '</div>';

        // url link
        $content = preg_replace('#(?<!href="|">)(https?://[^\s<]+)\b#i', '<a href="$1">$1</a>', $content);

        return $content;
    }
}

new TinyMarkdown();
