<?php

namespace AndrewDalpino\Epicuros;

use Illuminate\Console\Command;

class GenerateSharedKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epicuros:generate:shared {bits=512}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a shared key for token signing and verifying.';

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bits = $this->argument('bits');

        $this->info('Generating a key of ' . $bits . ' bits:');

        $this->line($this->generate($bits));
    }

    /**
     * @param  int  $bits
     * @return string
     */
    protected function generate(int $bits) : string
    {
        return bin2hex(random_bytes(round($bits / 8)));
    }
}
