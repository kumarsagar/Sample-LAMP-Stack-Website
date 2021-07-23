<?php

/**
 * Used to store website configuration information.
 *
 * @var string or null
 */
function config($key = '')
{
    $config = [
        'name' => 'Simple LAMP App',
        'site_url' => '',
        'pretty_uri' => false,
        'nav_menu' => [
            "Charlie" => "Charlie's Bite",
            "countWords" => 'Count Words',
            "SignUp"    =>  "Sign Up", 
            "SignIn"     =>  'Sign In',
            "ResetPassword" => "Reset Password",
            "ChangePassword" => "Change Password",
            "LogOut"          => "Log Out"
        ],
        'template_path' => 'template',
        'content_path' => 'content',
        'version' => '1.0',
    ];

    return isset($config[$key]) ? $config[$key] : null;
}
