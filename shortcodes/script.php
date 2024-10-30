<?php
#iframe shortcode class
$bringhub_shortcode_script = new bringhub_shortcode_script;
class bringhub_shortcode_script
{

    #initiate
    public function __construct()
    {
        add_shortcode('bringhub', array($this, 'view'));
    }
    #create the shortcode content
    public function view($atts)
    {
        global $bringhubdata, $bringhub_settings, $post;

        if (supports_bringhub($post->post_type) && is_single() && strpos($post->post_content, '[disable_bringhub]') === false) {
            $html = '';
            $html = bringhub_get_template('script.php', $atts);
        }

        return $html;
    }
}
