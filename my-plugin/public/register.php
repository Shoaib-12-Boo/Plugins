<?php
    if(isset($_POST['register'])){
        global $wpdb;
        $fname = $wpdb->escape($_POST['user_fname']);
        $lname = $wpdb->escape($_POST['user_lname']);
        $username = $wpdb->escape($_POST['username']);
        $email = $wpdb->escape($_POST['user_email']);
        $pass = $wpdb->escape($_POST['user_pass']);
        $con_pass = $wpdb->escape($_POST['user_con_pass']);

        if($pass == $con_pass){
            //wp_insert_user
            //wp_create_user
            //$result = wp_create_user($username , $pass , $email);
            $user_date = array(
            'user_login' => $username,
            'user_email' => $email,
            'first_name' => $fname,
            'last_name' => $lname,
            'display_name' => $fname.' ' .$lname,
            'user_pass' => $pass
            );
            
            $result = wp_insert_user($user_date);
        
            if(!is_wp_error($result)){
                echo 'User Created ID:' .$result;
                add_user_meta($result, 'type' , 'Faculty');
            }
            else{
                echo $result->get_error_message();
            }
        }
        else{
            echo 'Password must be matched!';
        }
    }
?>
<!-- 
<form action="<//?php echo get_the_permalink();?>" method="post">
    Fisrt Name: <input Type="text" name="user_fname" id="user_fname"></br>

    Last Name: <input Type="text" name="user_lname" id="user_lname"></br>

    Username: <input Type="text" name="username" id="username"><br>

    Email: <input Type="email" name="user_email" id="user-email"><br>

    Password: <input Type="password" name="user_pass" id="user_pass"><br>

    Confrim Password <input Type="password" name="user_con_pass" id="user_con_pass"></br>

    <input Type="submit" class="button" name="register" value="register" >
</form> -->

<div class="form-wrapper">
    <div class="login-form">
        <form action="<?php echo get_the_permalink();?>" method="post">
            Username: <input Type="text" name="username" id="login-username"><br>
            Password: <input Type="password" name="pass" id="login_pass"><br>
            <input type="submit" name="login" value="login">
        </form>
    </div>
    <div class="regi-form"> 
        <form action="<?php echo get_the_permalink();?>" method="post">
            Fisrt Name: <input Type="text" name="user_fname" id="user_fname"></br>

            Last Name: <input Type="text" name="user_lname" id="user_lname"></br>

            Username: <input Type="text" name="username" id="username"><br>

            Email: <input Type="email" name="user_email" id="user-email"><br>

            Password: <input Type="password" name="user_pass" id="user_pass"><br>

            Confrim Password <input Type="password" name="user_con_pass" id="user_con_pass"></br>

            <input Type="submit" class="button" name="register" value="register" >
        </form>
    </div>
</div>