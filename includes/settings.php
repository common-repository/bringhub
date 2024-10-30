<?php

class BringhubSettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Bringhub Settings Admin',
            'Bringhub',
            'manage_options',
            'bringhub-settings-admin',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('bringhub_settings');
        ?>
        <div class="wrap">
            <h1>Bringhub Settings</h1>
            <form method="post" action="options.php">
            <?php
// This prints out all hidden setting fields
        settings_fields('bringhub_option_group');
        do_settings_sections('bringhub-settings-admin');
        submit_button();
        ?>
            </form>
        </div>
        <?php
}

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'bringhub_option_group',
            'bringhub_settings',
            array($this, 'sanitize')
        );

        add_settings_section(
            'bringhub_settings_global',
            'NCU Configuration',
            array($this, 'print_section_info'), // Callback
            'bringhub-settings-admin' // Page
        );

        add_settings_field(
            'tiles',
            'Tiles',
            array($this, 'tiles_callback'),
            'bringhub-settings-admin',
            'bringhub_settings_global'
        );

        add_settings_field(
            'headline',
            'Headline',
            array($this, 'headline_callback'),
            'bringhub-settings-admin',
            'bringhub_settings_global'
        );

        add_settings_field(
            'columns',
            'Columns',
            array($this, 'columns_callback'),
            'bringhub-settings-admin',
            'bringhub_settings_global'
        );

        add_settings_field(
            'rows',
            'Rows',
            array($this, 'rows_callback'),
            'bringhub-settings-admin',
            'bringhub_settings_global'
        );

        add_settings_field(
            'selector-context',
            'Selector Context',
            array($this, 'selectorcontext_callback'),
            'bringhub-settings-admin',
            'bringhub_settings_global'
        );


        add_settings_field(
            'taxonomy',
            'Use Taxonomy',
            array($this, 'taxonomy_callback'),
            'bringhub-settings-admin',
            'bringhub_settings_global'
        );

        // add_settings_field(
        //     'ebay',
        //     'Is Ebay',
        //     array($this, 'ebay_callback'),
        //     'bringhub-settings-admin',
        //     'bringhub_settings_global'
        // );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = array();
        if (isset($input['tiles'])) {
            $new_input['tiles'] = absint($input['tiles']);
        }

        if (isset($input['headline'])) {
            $new_input['headline'] = sanitize_text_field($input['headline']);
        }

        if (isset($input['rows'])) {
            $new_input['rows'] = absint($input['rows']);
        }

        if (isset($input['columns'])) {
            $new_input['columns'] = absint($input['columns']);
        }

        if (isset($input['selector-context'])) {
            $new_input['selector-context'] = sanitize_text_field($input['selector-context']);
        }

        $new_input['taxonomy'] = absint($input['taxonomy']);

        // $new_input['ebay'] = absint($input['ebay']);

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    public function tiles_callback()
    {
        printf(
            '<input type="text" id="tiles" name="bringhub_settings[tiles]" value="%s" />',
            isset($this->options['tiles']) ? esc_attr($this->options['tiles']) : get_default_value("tiles")
        );
    }

    public function headline_callback()
    {
        printf(
            '<input type="text" id="headline" name="bringhub_settings[headline]" value="%s" />',
            isset($this->options['headline']) ? esc_attr($this->options['headline']) : get_default_value("headline")
        );
    }

    public function rows_callback()
    {
        printf(
            '<input type="text" id="rows" name="bringhub_settings[rows]" value="%s" />',
            isset($this->options['rows']) ? esc_attr($this->options['rows']) : get_default_value("rows")
        );
    }

    public function columns_callback()
    {
        printf(
            '<input type="text" id="columns" name="bringhub_settings[columns]" value="%s" />',
            isset($this->options['columns']) ? esc_attr($this->options['columns']) : get_default_value("columns")
        );
    }

    public function selectorcontext_callback()
    {
        printf(
            '<input type="text" id="selector-context" name="bringhub_settings[selector-context]" value="%s" />',
            isset($this->options['selector-context']) ? esc_attr($this->options['selector-context']) : get_default_value("selector-context")
        );
    }

    function taxonomy_callback() {
        printf('<input name="bringhub_settings[taxonomy]" id="taxonomy" type="checkbox" value="1" class="code" ' .
            checked(
                1,
                isset($this->options['taxonomy']) ?
                    esc_attr($this->options['taxonomy']) :
                    get_default_value("taxonomy"),
                false
            ). ' /> (Use Tags as keywords)'
        );
    }

    // function ebay_callback() {
    //     printf('<input name="bringhub_settings[ebay]" id="ebay" type="checkbox" value="1" class="code" ' .
    //         checked(
    //             1,
    //             isset($this->options['ebay']) ?
    //                 esc_attr($this->options['ebay']) :
    //                 get_default_value("ebay"),
    //             false
    //         ). ' />'
    //     );
    // }

}

if (is_admin()) {
    $bringhub_settings = new BringhubSettings();
}
