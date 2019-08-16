<?php

namespace App\Console\Commands\Twitter;

use App\Facades\Twitter;
use App\Models\Word;
use Illuminate\Console\Command;

class PostWordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:word:random';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post a random word to Twitter';

    /**
     * Create a new command instance.
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
        $word = Word::inRandomOrder()
            ->first();

        if (!empty($word)) {
            $replaces = [
                'origin' => $word->origin,
                'locale' => $word->locale,
                'category' => strtolower($word->category->name),
                'line' => str_repeat(PHP_EOL, 2),
            ];

            $templates = [
                __('Apakah kamu tahu padanan dari istilah :origin? Jawabannya adalah :locale (:category). #padanan #glosarium', $replaces),
                __('Istilah :origin dikenal sebagai :locale dalam bahasa Indonesia. #padanan #glosarium', $replaces),
                __('Padanan :origin dalam :category adalah :locale. #padanan #glosarium', $replaces),
                __('Padanan dalam bidang :category:line:origin = :locale:line#padanan #glosarium', $replaces),
                __('Dalam bahasa Indonesia, :origin berarti :locale. #padanan #glosarium', $replaces),
                __('Tahukan kamu kalo padanan dari istilah :origin dalam bidang :category adalah :locale? #padanan #glosarium', $replaces),
                __('Apa itu :origin?:lineDalam bahasa Indonesia dikenal sebagai :locale (:category).:line#padanan #glosarium', $replaces),
                __(':origin = :locale (:category) #padanan #glosarium', $replaces),
                __('Dalam bahasa Indonesia, istilah :origin dipadankan sebagai :locale (:category). #padanan #glosarium', $replaces),
                __('Kalau dalam bahasa asing disebut :origin, maka dalam bahasa Indonesia disebut :locale. Istilah ini umum ada pada bidang :category. #padanan #glosarium', $replaces),
            ];

            Twitter::send(collect($templates)->random());
        }
    }
}
