<?php
#post type supports bringhub
function supports_bringhub($post_type)
{
    $taxonomies = array();
    $taxonomy_objects = get_object_taxonomies($post_type);

    if ($taxonomy_objects) {
        foreach ($taxonomy_objects as $k => $v) {

            $taxonomies[] = $k;
        }
    }

    if ((in_array('category', $taxonomies) or in_array('post_tag', $taxonomies))) {
        return true;
    } else {
        return false;
    }
}

#Convert taxonomies to js array
function bringhub_convert_tax($taxonomy)
{
    $t = array();

    if (!$taxonomy) {
        return false;
    }

    if (count($taxonomy) > 0) {

        foreach ($taxonomy as $tax) {

            $t[] = strtolower($tax->name);

        }
        return implode(',', $t);
    }
    return false;
}
#locate the template
function bringhub_locate_template($template_name, $template_path = '', $default_path = '')
{

    if (!$template_path) {
        $template_path = BRINGHUB_DIR;
    }

    if (!$default_path) {
        $default_path = BRINGHUB_DIR . 'templates/';
    }

    // Look within passed path within the theme - this is priority.
    $template = locate_template(
        array(
            trailingslashit($template_path) . $template_name,
            $template_name,
        )
    );

    // Get default template/
    if (!$template) {
        $template = $default_path . $template_name;
    }

    if (file_exists(get_stylesheet_directory() . '/bringhub/' . $template_name)) {
        $template = get_stylesheet_directory() . '/bringhub/' . $template_name;
    }

    // Return what we found.
    return apply_filters('bringhub_locate_template', $template, $template_name, $template_path);
}
#get a template and return it as a variable
function bringhub_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
{
    global $obitdata;
    if (!empty($args) && is_array($args)) {
        extract($args);
    }
    // $located = bringhub_locate_template( $template_name, $template_path, $default_path );
    // if ( ! file_exists( $located ) ) {
    //     echo 'Template not found';
    //     return;
    // }
    // Allow 3rd party plugin filter template file from their plugin.
    //$located = apply_filters( 'bringhub_get_template', $located, $template_name, $args, $template_path, $default_path );
    ob_start();
    do_action('bringhub_before_template_part', $template_name, $template_path, $located, $args);
    bringhub_ncu_render($args);
    //include( $located );
    do_action('bringhub_after_template_part', $template_name, $template_path, $located, $args);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function get_default_value($attr) {
    $default = "";
    switch ($attr) {
        case "headline":
            $default = "See It. Shop It.";
            break;
        case "tiles":
            $default = "4";
            break;
        case "tiles-sponsored":
            $default = "4";
            break;
        case "columns":
            $default = "4";
            break;
        case "rows":
            $default = "1";
            break;
        case "taxonomy":
            $default = "1";
    }
    return $default;
}

function bringhub_ncu_attr($attr, $globalSettings, $shortcodes)
{
    if (!empty($shortcodes[$attr])) {
        return "data-bringhub-{$attr}=\"{$shortcodes[$attr]}\"";
    }
    if (!empty($globalSettings[$attr])) {
        return "data-bringhub-{$attr}=\"{$globalSettings[$attr]}\"";
    }

    $default = get_default_value($attr);

    if (!empty($default)) {
        return "data-bringhub-{$attr}=\"{$default}\"";
    }
    return "";
}

function bringhub_value_as_attr($attr, $value) {
    return "data-bringhub-{$attr}=\"{$value}\"";
}

function bringhub_ncu_render($shortcodes)
{
    global $post;
    $globalSettings = get_option('bringhub_settings');
    //echo "<pre>" . json_encode($shortcodes) . "</pre>";
    //echo "<pre>" . json_encode($globalSettings) . "</pre>";
    $time = get_the_time('c');
    $url = get_permalink($post->ID);
    $tiles = bringhub_ncu_attr("tiles", $globalSettings, $shortcodes);
    $headline = bringhub_ncu_attr("headline", $globalSettings, $shortcodes);
    $categories = bringhub_ncu_attr("categories", $globalSettings, $shortcodes);
    $keywords = bringhub_ncu_attr("keywords", $globalSettings, $shortcodes);
    $columns = bringhub_ncu_attr("columns", $globalSettings, $shortcodes);
    $rows = bringhub_ncu_attr("rows", $globalSettings, $shortcodes);
    $selectorcontext = bringhub_ncu_attr("selector-context", $globalSettings, $shortcodes);
    $taxonomy = $globalSettings["taxonomy"];


    if ("" == $keywords && $taxonomy == "1") {
        $keywords =  bringhub_value_as_attr("keywords",bringhub_convert_tax(get_the_tags($post->ID)));
    }

    if ("" == $categories) {
        $categories = bringhub_value_as_attr("categories",bringhub_convert_tax(get_the_category($post->ID)));
    }

    $template = <<<TEMPLATE
<!-- BEGIN Bringhub NCU -->
<div class="bh-post-date" style="display:none">{$time}</div>
<script
    data-bringhub-ncu="v2"
    data-bringhub-app="bringhub"
		{$tiles}
		{$headline}
        {$categories}
        {$keywords}
		{$columns}
        {$rows}
        {$selectorcontext}
		data-bringhub-url="{$url}"
    data-bringhub-selector-date-published=".bh-post-date"
    async src="https://ncu.bringhub.com/async.js"></script>
<!-- END Bringhub NCU -->
TEMPLATE;
    echo $template;
}
