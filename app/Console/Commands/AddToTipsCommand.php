<?php

namespace App\Console\Commands;

use App\Models\DailyTip;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\UserVote;
use Carbon\Carbon;

use Illuminate\Console\Command;

class AddToTipsCommand extends Command
{

    protected $signature = 'Tip:add';

    protected $description = 'Check posts type 2 to add them to Tips database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $posts = Post::where(['type' => 2, 'is_accepted' => true])
            ->where('updated_at', '<', Carbon::now()->addDays(-8))->get();
        foreach ($posts as $post) {
            $vote_id = PostVote::where(['post_id' => $post->id, 'vote' => 'Agree'])->first('id')->id;
            if (UserVote::where('vote_id',  $vote_id)->count() > 500) {
                $votes = ($this->getVotes($post->id));
                foreach ($votes as $vote) {
                    if ($vote['vote'] == 'Agree' && (int)$vote['rate'] >= 75) {
                        DailyTip::create([
                            'tip' => $post->text
                        ]);
                        break;
                    }
                    continue;
                }
            }
        }
    }
}
