/**
 * Add the theme assets as editor styles.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    bundle('app')->editorStyles();
});
