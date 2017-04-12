<?php

namespace AndrewDalpino\Epicuros;

use phpseclib\Crypt\RSA;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRSAKeys extends Command
{
    const STORAGE_FOLDER = '/certs/';
    const FOLDER_UMASK = 0755;
    const PRIVATE_KEY_SUFFIX = '-private.key';
    const PUBLIC_KEY_SUFFIX = '-public.key';
    const RSA_BITS = 4096;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epicuros:generate:keys {name=epicuros} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate RSA keys for JWT signing and verifying.';

    /**
     * The RSA implementation.
     *
     * @var  \phpseclib\Crypt\RSA  $rsa
     */
    protected $rsa;

    /**
     * Create a new command instance.
     *
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
        $folder = storage_path(self::STORAGE_FOLDER);

        if (! file_exists($folder)) {
            mkdir($folder, self::FOLDER_UMASK, true);
        }

        $path['private'] = storage_path($folder . Str::slug($this->argument('name')) . self::PRIVATE_KEY_SUFFIX);
        $path['public'] = storage_path($folder . Str::slug($this->argument('name')) . self::PUBLIC_KEY_SUFFIX);

        if (! $this->option('force')) {
            if (file_exists($path['private']) || file_exists($path['public'])) {
                $this->error('Cannot overwrite existing keys! Use --force if you really know what you\'re doing.');
                return;
            }
        }

        $keys = $this->rsa->createKey(self::RSA_BITS);

        file_put_contents($path['private'], $keys['privatekey']);
        file_put_contents($path['public'], $keys['publickey']);

        $this->info(self::RSA_BITS . 'bit RSA keys generated successfully in' . storage_path(self::STORAGE_FOLDER) . '.');
    }
}
