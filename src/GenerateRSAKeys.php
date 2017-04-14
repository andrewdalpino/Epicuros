<?php

namespace AndrewDalpino\Epicuros;

use phpseclib\Crypt\RSA;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRSAKeys extends Command
{
    const FOLDER_UMASK = 0755;
    const PRIVATE_KEY_SUFFIX = '-private.key';
    const PUBLIC_KEY_SUFFIX = '-public.key';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epicuros:generate:rsa {name=epicuros} {folder=/certs} {bits=4096} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate RSA keys for token signing and verifying.';

    /**
     * The RSA implementation.
     *
     * @var  \phpseclib\Crypt\RSA  $rsa
     */
    protected $rsa;

    /**
     * Constructor.
     *
     * @param  \phpseclib\Crypt\RSA  $rsa
     * @return void
     */
    public function __construct(RSA $rsa)
    {
        $this->rsa = $rsa;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = str_replace(' ', '-', $this->argument('name'));

        $folder = storage_path($this->argument('folder')) . '/';

        $bits = intval($this->argument('bits'));

        if (! file_exists($folder)) {
            mkdir($folder, self::FOLDER_UMASK, true);
        }

        $path['private'] = $folder . $name . self::PRIVATE_KEY_SUFFIX;
        $path['public'] = $folder . $name . self::PUBLIC_KEY_SUFFIX;

        if (! $this->option('force')) {
            if (file_exists($path['private']) || file_exists($path['public'])) {
                $this->error('Cannot overwrite existing keys! Use --force if you really know what you\'re doing.');
                return;
            }
        }

        $keys = $this->rsa->createKey($bits);

        file_put_contents($path['private'], $keys['privatekey']);
        file_put_contents($path['public'], $keys['publickey']);

        $this->info($bits . ' bit RSA keys generated successfully in ' . $folder . '.');
    }
}
