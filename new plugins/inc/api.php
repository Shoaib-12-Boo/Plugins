<?php defined('ABSPATH') || die("Nice Try"); 
//  for github data profile get with API
 // header('content_type: application/json');
    // $info = wp_remote_retrieve_body(wp_remote_get('https://api.github.com/users/Shoaib-12-Boo'));
    // $user =json_decode($info);
    
    $post = wp_remote_retrieve_body(wp_remote_post('https://jsonplaceholder.typicode.com/users',
    [
        'body' => [
            'title' => 'This is dummy post data with API...',
            'body' => 'This is body for dummy post data with API ...',
            'user_id'=> 10,
        ],
    ]));
    print_r($post);

     $posts = wp_remote_retrieve_body(wp_remote_get('https://jsonplaceholder.typicode.com/users'));
    $posts =json_decode($posts);

    ?>
    <!-- github user profile data get with Rest API on admin page-->
<!-- <table class="widefat fixwd striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Image</th>
            <th>Company</th>
            <th>Location</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo $user->id; ?></td>
            <td><?php echo $user->name; ?> </td>
            <td><img scr="<?php echo $user->avatar_url; ?>" width="100%" /></td>
            <td><?php echo $user->company; ?></td>
            <td><?php echo $user->location; ?></td>
        </tr>
    </tbody>
</table> -->


<!--  getting posts with API on admin page -->
<table class="widefat fixwd striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>userid</th>
            <th>title</th>
            <th>body</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($posts as $p): ?>
        <tr>
            <td><?php echo $p->id; ?></td>
            <td><?php echo $p->user_id; ?> </td>
            <td><?php echo $p->title; ?></td>
            <td><?php echo $p->body; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
