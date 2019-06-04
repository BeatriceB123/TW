<?php
    include_once '../includes/apiCall.php';
    include_once '../includes/unsetCookies.php';

    function setResponseCookie($response)
    {
        setcookie("response", $response, time() + 360, "/");
        header("Location: ./new_account.php");
    }


    if(isset($_POST['returnToMainPage']))
    {
        unsetCookiesForAddNewAccount(); 
        header("Location: ./main_page.php");
    }
    else if(isset($_POST['tryToCreateAccount']))
    {

        setcookie("response","", time() + 360, "/");

        $siteName        = $_POST['siteName']; 
        $username        = $_POST['username']; 
        $password        = $_POST['password']; 
        $passwordConfirm = $_POST['passwordConfirm'];
        $address         = $_POST['address'];
        $comments        = $_POST['comments'];
        $reminderDate    = $_POST['reminderDate'];
        
        setcookie("siteName", $_POST['siteName'], time() + 360, "/"); //6 minute
        setcookie("username", $_POST['username'], time() + 360, "/");
        setcookie("address",  $_POST['address'],  time() + 360, "/");
        setcookie("comments", $_POST['comments'], time() + 360, "/");
        setcookie("reminderDate", $_POST['reminderDate'], time() + 360, "/");

        //prima data verificam sa fie toate campurile completate
        if(empty($siteName))
            setResponseCookie("Enter a name for your site"); 
        elseif (empty($username))
            setResponseCookie("Enter username for your account.");  
        elseif (empty($address))
            setResponseCookie("Enter address for your account.");
        elseif (empty($reminderDate))
        //     setResponseCookie("Enter a reminder date for your account.");
        // elseif (empty($password) )
            setResponseCookie("Enter password for your account.");
        elseif (empty($passwordConfirm))
            setResponseCookie("Confirm password.");
        else
        {
            //apoi verificam sa aiba formate bune
            if(!filter_var($address, FILTER_VALIDATE_EMAIL))
                setResponseCookie("Incorrect address");
            elseif($passwordConfirm != $password)
                setResponseCookie("Passwords doesn't match"); 
            else
            {
                //apoi punem in BD, indiferent daca mai e existent un cont asemanator sau nu 
                $accountCreateApi = 'http://localhost/TWPM/api/account/create.php'; 

                $hashedePwd = password_hash($password, PASSWORD_DEFAULT);
                //pentru extrgere:  $pwdCheck = password_verify($password, $$['pwdUsers']); - true/false
                
                $id_categorie  = $_COOKIE['selectedCategoryID']; 
                $id_utilizator = $_COOKIE['userID'];

                $account_array =  array(
                    "id_categorie" => $id_categorie,
                    "id_utilizator" => $id_utilizator,
                    "username" =>  $username,
                    "parola" =>  $hashedePwd,
                    "adresa_site" =>  $address,
                    "nume_site" => $siteName,
                    "comentarii" =>  $comments,
                    "data_expirare" => $reminderDate
                );
        
                $make_call = ApiCall('POST', $accountCreateApi, json_encode($account_array));
                
                $response = json_decode($make_call, true);

                setResponseCookie($response['message']);
            }
        }
        header("Location: ./new_account.php");
    }

?>