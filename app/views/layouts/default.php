<!--
This is the default layout. It is automatically put on every page.
  -->
<!DOCTYPE html>
<html>
    <head>
        <title>Example Website</title>
        <link href="/css/styles.css" type="text/css" rel="stylesheet">
    </head>
    <body>
    
            <?php if(Session::isLoggedIn()):
                $user = Session::getCurrentUser();
            ?>
                Welcome, <?php echo h($user['username']);?>. <a href="/members/logout">Logout</a>
            <?php else:?>
                You are not logged in. <a href="/members/login">Login</a>
            <?php endif;?>
            
            <h1>Example Website</h1>
            
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/help">Help</a></li>
                <li><a href="/posts">Posts</a></li>
                <li><a href="/posts/add">Add Post</a></li>
            </ul>
            
            <?php echo Session::flash();?>
            <?php echo Session::flash('error');?>
            <?php echo Session::flash('info');?>
            <?php echo Session::flash('success');?>
            <?php echo Session::flash('warning');?>
            <?php echo $content;?>
            <!-- Copyright 2014 Michael -->
    </body>
</html>
