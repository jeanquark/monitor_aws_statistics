<?php namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Controllers\Controller;
use App\CommentReply;
use App\Comment;
use App\Post;
use App\User;
use App\Tag;

use Validator;
use Sentinel;
use View;
use Aws\CloudWatch\CloudWatchClient;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function index() {
        $posts = Post::where('is_published', '=', 0)->get();
        $comments = Comment::where('is_published', '=', 0)->get();
        $replies = CommentReply::where('is_published', '=', 0)->get();
        $tags = Tag::all();
        $users = User::all();

        return View::make('admin.index')
            ->with('posts', $posts)
            ->with('comments', $comments)
            ->with('replies', $replies)
            ->with('tags', $tags)
            ->with('users', $users);
    }


    public function only() {

        return View::make('adminonly');
    }

    public function statistics() {
    $client = CloudWatchClient::factory(array('region'  => 'eu-central-1', 'version' => 'latest')); // [1]
    $cpu = array( 
        array('Name' => 'InstanceId', 'Value' => 'i-eb4ec356'), // [2]
    );
    $cpu1 = $client->getMetricStatistics(array(
        'Namespace' => 'AWS/EC2',
        'MetricName' => 'CPUUtilization', // [3]
        'Dimensions' => $cpu,
        'StartTime' => strtotime('-1 day'),
        'EndTime' => strtotime('now'),
        'Period' => 300,
        'Statistics' => array('Maximum', 'Minimum'),
    ));

    $rds = array(
        array('Name' => 'DBInstanceIdentifier', 'Value' => 'bloghomedb'), // [4]
    );
    $rds1 = $client->getMetricStatistics(array(
        'Namespace' => 'AWS/RDS',
        'MetricName' => 'CPUUtilization',
        'Dimensions' => $rds,
        'StartTime' => strtotime('-1 day'),
        'EndTime' => strtotime('now'),
        'Period' => 300,
        'Statistics' => array('Maximum', 'Minimum'),
    ));

    $cpu_util = $cpu1['Datapoints'];
    $rds_util = $rds1['Datapoints'];

    return View::make('admin.statistics')
	->with('cpu', $cpu)
        ->with('rds', $rds) 
        ->with('cpu_util', $cpu_util)
        ->with('rds_util', $rds_util);
}

}
