<?php

namespace Roots\AcornFseHelper;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AcornFseHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'acorn-fse-helper');

            $this->commands([
                Console\Commands\FseInitCommand::class,
            ]);
        }

        // Auto-inject Vite assets for FSE themes
        add_action('wp_head', function () {
            $entryPoints = apply_filters('acorn/fse/vite_entrypoints', [
                'resources/css/app.css',
                'resources/js/app.js',
            ]);

            echo \Illuminate\Support\Facades\Vite::withEntryPoints($entryPoints)->toHtml();
        });

        Blade::directive('blocks', fn () => '<?php ob_start(); ?>');
        Blade::directive('endblocks', fn () => '<?php echo do_blocks(ob_get_clean()); ?>');
        Blade::directive('blockpart', fn ($blocks) => "<?php collect({$blocks})->each(fn (\$block) => block_template_part(\$block)); ?>");
    }
}
