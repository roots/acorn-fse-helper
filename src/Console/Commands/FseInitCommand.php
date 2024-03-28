<?php

namespace Roots\AcornFseHelper\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;

class FseInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fse:init
                            {--all : Publish all stubs.}
                            {--with-templates : Publish example block template and part stubs.}
                            {--with-patterns : Publish example block pattern stubs.}
                            {--force : Overwrite any existing files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize full-site editing support in the theme.';

    /**
     * The required Acorn version.
     */
    protected string $version = '4.1.1';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! $this->isValidAcornVersion()) {
            $this->components->error("Full-site editing support requires <fg=red>Acorn {$this->version}</> or higher.");

            return;
        }

        $this->components->info('Initializing <fg=blue>full-site editing</> support in the active theme.');

        $this->task('Enabling <fg=blue>theme support</> for block templates', $this->handleSupport());

        if ($this->shouldPublishTemplates()) {
            $this->task('Publishing the example block <fg=blue>template</> and <fg=blue>part</> stubs', $this->publishTemplates());
        }

        if ($this->shouldPublishPatterns()) {
            $this->task('Publishing the example block <fg=blue>pattern</> stubs', $this->publishPatterns());
        }

        $this->components->info('Full-site editing support has been <fg=blue>added</> to the theme.');

        if (! $this->hasThemeSupport()) {
            $this->components->warn('Please ensure that <fg=blue>theme.json</> is present in the theme directory.');
        }
    }

    /**
     * Handle block template support.
     */
    protected function handleSupport(): bool
    {
        if (current_theme_supports('block-templates')) {
            return false;
        }

        return $this->handleSageSupport() || $this->handleRadicleSupport();
    }

    /**
     * Add block template support back to Sage.
     */
    protected function handleSageSupport(): bool
    {
        if (! file_exists($path = app_path('setup.php'))) {
            return false;
        }

        return $this->commentOut($path, "remove_theme_support('block-templates')");
    }

    /**
     * Add block template support back to Radicle.
     */
    protected function handleRadicleSupport(): bool
    {
        if (
            ! file_exists($path = config_path('theme.php')) ||
            ! in_array('block-templates', config('theme.remove', []))
        ) {
            return false;
        }

        return $this->commentOut($path, "'block-templates'");
    }

    /**
     * Publish the block template stubs.
     */
    protected function publishTemplates(): bool
    {
        $published = false;
        $paths = ['parts', 'templates'];

        foreach ($paths as $path) {
            $files = File::files($this->getStubPath($path));
            $path = $this->getBasePath($path);

            foreach ($files as $file) {
                $name = $file->getFilename();

                if (! file_exists($target = "{$path}/{$name}") || $this->option('force')) {
                    File::put($target, $this->handleReplacements(file_get_contents($file)));

                    $published = true;
                }
            }
        }

        return $published;
    }

    /**
     * Determine if the templates should be published.
     */
    protected function shouldPublishTemplates(): bool
    {
        return $this->option('all')
            || $this->option('with-templates')
            || confirm('<fg=blue>Publish</> example block <fg=blue>part</> and <fg=blue>template</> stubs?', default: true);
    }

    /**
     * Publish the block pattern stubs.
     */
    protected function publishPatterns(): bool
    {
        $published = false;
        $stubs = $this->getStubPath('patterns');
        $path = $this->getBasePath('patterns');

        $files = File::files($stubs);

        foreach ($files as $file) {
            $name = $file->getFilename();

            if (! file_exists($target = "{$path}/{$name}") || $this->option('force')) {
                File::put($target, $this->handleReplacements(file_get_contents($file)));

                $published = true;
            }
        }

        return $published;
    }

    /**
     * Determine if the patterns should be published.
     */
    protected function shouldPublishPatterns(): bool
    {
        return $this->option('all')
            || $this->option('with-patterns')
            || confirm('<fg=blue>Publish</> example block <fg=blue>pattern</> stubs?', default: true);
    }

    /**
     * Handle content replacement patterns.
     */
    protected function handleReplacements(string $content): string
    {
        return str_replace(
            ['{{ quote }}', '{{ textdomain }}'],
            [Inspiring::quotes()->random(), $this->getTextDomain()],
            $content
        );
    }

    /**
     * Check for the existence of `theme.json`.
     */
    protected function hasThemeSupport(): bool
    {
        return wp_theme_has_theme_json();
    }

    /**
     * Render a task message.
     */
    protected function task(string $message, bool $status = true): void
    {
        $status = $status
            ? '<fg=green;options=bold>DONE</>'
            : '<fg=yellow;options=bold>SKIPPED</>';

        $this->components->twoColumnDetail($message, $status);
    }

    /**
     * Comment out the specified string in a file.
     */
    protected function commentOut(string $path, string $string): bool
    {
        if (! file_exists($path)) {
            return false;
        }

        $contents = file_get_contents($path);

        if (
            ! Str::contains($contents, $string) ||
            Str::contains($contents, "// {$string}")
        ) {
            return false;
        }

        $contents = str_replace($string, "// {$string}", $contents);

        return file_put_contents($path, $contents) !== false;
    }

    /**
     * Retrieve the base path.
     */
    protected function getBasePath(string $path = ''): string
    {
        $path = base_path($path);

        if (! file_exists($path)) {
            File::ensureDirectoryExists($path);
        }

        return $path;
    }

    /**
     * Retrieve the stub path.
     */
    protected function getStubPath(string $path = ''): string
    {
        if ($path) {
            $path = Str::start($path, '/');
        }

        return __DIR__."/stubs{$path}";
    }

    /**
     * Retrieve the text domain.
     */
    protected function getTextDomain(): string
    {
        return strtolower(wp_get_theme()->get('TextDomain') ?: 'sage');
    }

    /**
     * Determine if the current Acorn version is supported.
     */
    protected function isValidAcornVersion(): bool
    {
        $version = Str::of($this->getApplication()->getVersion())
            ->after(' ')
            ->before(' ')
            ->toString();

        if (Str::contains($version, 'dev')) {
            return true;
        }

        return version_compare($version, $this->version, '>=');
    }
}
