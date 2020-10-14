<?php 
include('database.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



function redirect($page){
    header('Location: '.$page);
    exit;
}
function authenticate($username,$password){
    /* 
    This function get username and password , query the database for exsisting user .
    If user exsists it will return a user object, if not retrun null.


    Function vulnerable to SQL Injection , username parameter filed not sanetized which allow to attack bypass authentication.

    PAYLOAD (UserName field - for knownuser)- userName'# 
    PAYLOAD (UserName field - for unknown)- ' or 1=1 limit 1# 

    HOW TO FIX WITH PARAMETERIZED QUERY:
        global $db;
        $query = "SELECT * FROM users WHERE username=:userName AND password=:passWord LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindValue('userName',$username);                         <------ ADD ALSO USERNAME AS PARAMETER 
        $stmt->bindValue('passWord',hash_password($password));
        $stmt->execute();
    */
    global $db;
    $query = "SELECT * FROM users WHERE username='$username' AND password=:passWord LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindValue('passWord',hash_password($password));
    $stmt->execute();
    $result = $stmt->rowcount();
    if ($result==1) {
      return $found_Account=$stmt->fetch();
    }else {
      return null;
    }
}
function is_authenticated(){
    /*
    Function check whether user is logged in or not by checking the SESSION variables.
    if user not logged in he will redirect to the login page.
    */
    if(isset($_SESSION['userId'])){
        return true;
    }else{
        $_SESSION["ErrorMessage"]="Login Required !";
        redirect('../login.php');
    }
}
function password_policy($password){
    /*
    Check password policy from the 'password_policy.json' file and prevent usage of common passwords from 'common_password.txt' file.

    Function get user password as parameter and retrun hashed password if all check was passed , else return false and error message.
    */
    $password_policy =json_decode(file_get_contents('../passwords_policy.json'), true);
    $common_passwords = explode("\n",file_get_contents('../common_passwords.txt'));

    // Check if password is a common password.
    if(in_array($password, $common_passwords)){
        $_SESSION["ErrorMessage"]="Password is too common be creative !!!";
        return false;
    }
    // Check the length of the password.
    elseif(strlen($password)<$password_policy['length']){
        $_SESSION["ErrorMessage"]="Password should be at least ".$password_policy['length'];
        return false;
    }
    // Check if password contain uppercase only if it was defined on the password_policy file .
    elseif($password_policy['uppercase']){
        if(!preg_match('@[A-Z]@', $password)){
            $_SESSION["ErrorMessage"]="Password need to contain at least one uppercase letter";
            return false;
        }
    }
    // Check if password contain specialChars only if it was defined on the password_policy file .
    elseif($password_policy['specialChars']){
        if(!preg_match('@[^\w]@', $password)){
            $_SESSION["ErrorMessage"]="Password need to contain at least one spaciel char";
            return false;
        }
    }
    // Check if password contain lowercase only if it was defined on the password_policy file .
    elseif($password_policy['lowercase']){
        if(!preg_match('@[a-z]@', $password)){
            $_SESSION["ErrorMessage"]="Password need to contain at least one lowercase letter";
            return false;
        }
    }
    // Check if password digit lowercase only if it was defined on the password_policy file .
    elseif($password_policy['digit']){
        if(!preg_match('@[0-9]@', $password)){
            $_SESSION["ErrorMessage"]="Password need to contain at least one digit ";
            return false;
        }
    }

    return hash_password($password);

}
function create_new_user($username,$password,$email){
    /*
    Function get user details and write it to the database.

    This function is Vulnerable to SQLI injection , none of the parameters are sanetized.

    PAYLOAD (UserName field) - ','',''); DELETE FROM users#

    HOW TO FIX USING PARAMETERIZE QUERY:
    global $db;
    $query = "INSERT INTO users(username,email,password)";
    $query.= "VALUES (:userNAme,:eMail,:passWord)";
    $stmt = $db->prepare($query)
    $stmt->bindValue(userNAme,$username);
    $stmt->bindValue(eMail,$email);
    $stmt->bindValue(passWord,$password);
    $stmt->execute();
    return $db->lastInsertId();

    */
    global $db;
    // Store new user
    $query = "INSERT INTO users(username,email,password)";
    $query.= "VALUES ('$username','$email','$password')";
    $db->query($query);

    // Register user password on password_histroy table
    register_password($password,$db->lastInsertId());

    // Return new user object
    return get_user($username);

}
function check_password($password,$userId){
    global $db;
    $query = "SELECT * FROM users WHERE id=:userID AND password=:passWord";
    $stmt = $db->prepare($query);
    $stmt->bindValue('userID',$userId);
    $stmt->bindValue('passWord',hash_password($password));
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result){
        return true;
    }else{
        return false;
    }
}
function update_new_password($password,$userId){
    /*
    Function get password and userId , update the password on user table and password history.
    return True/False if both queries was successfull.
    */
    global $db;
    $query = "UPDATE users SET password=:passWord WHERE id=:userID";
    $stmt = $db->prepare($query);
    $stmt->bindValue('userID',$userId);
    $stmt->bindValue('passWord',$password);
    return ($stmt->execute() && register_password($password,$userId));
}
function create_new_client($firstName,$lastName){
    /*
    Create new client on the clients table.
    get first and last name as parameters , return client id.

    PAYLOAD (FirstName field)- ',''); DELETE FROM clients#

    Function Vulnerable to SQL Injection , none of the parameters are sanetized.
    HOW TO FIX USING PARAMETERIZE QUERY:
        global $db;
        $query = "INSERT INTO clients(firstName,lastName)";
        $query.= "VALUES(:firstNAme,:lastNAme)";
        $stmt = $db->prepare($query)
        $stmt->bindValue('firstNAme',$firstName);
        $stmt->bindValue('lastNAme',$lastName);
        $stmt->execute();
        return $db->lastInsertId();

    */ 
    global $db;
    $query = "INSERT INTO clients(firstName,lastName)";
    $query.= "VALUES('$firstName','$lastName')";
    $db->query($query);
    return $db->lastInsertId();

}
function get_user($userName){
    /*
    Function get username as input , query the database for the user and return a user object.
    */
    global $db;
    $query = "SELECT * FROM users WHERE username=:userName LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindValue('userName',$userName);
    $stmt->execute();
    $result = $stmt->rowcount();
    if ($result==1) {
      return $found_Account=$stmt->fetch();
    }else {
      return null;
    }
}
function hash_password($password){
    /*
    Function get password as parameter and return her hashed value.
    */
    $key = $password;
    return hash_hmac('sha1',$password."THIS_IS_SALT!!!!",$key);
}
function check_password_history($password,$userId){

    /*
    Function get password and userId as parameters , Check if the new password is match to one of the last N passwords.
    return True/False
    */
    global $db;
    
    $password_policy =json_decode(file_get_contents('../passwords_policy.json'), true);

    // Query the db for the last N passwords.
    $query="SELECT * FROM passwords_history WHERE userId=:userID order by id desc limit ".$password_policy['password_history'];
    $stmt = $db->prepare($query);
    $stmt->bindValue('userID',$userId);
    $stmt->execute();
    $result =  $stmt->fetchAll();

    // Push the passwords to array.
    $old_passwords=array();
    foreach($result as $res){
        array_push($old_passwords,$res['password']);
    }

    // Return if array contains the new password.
    return in_array($password,$old_passwords);

}
function register_password($password,$userId){
    /*
    Function get password and userId as parameters , write the password to the password_history tables.
    return True/False if query was ok.
    */
    global $db;
    $query = "INSERT INTO passwords_history(userId,password) VALUES(:userID,:passWord)";
    $stmt = $db->prepare($query);
    $stmt->bindValue('userID',$userId);
    $stmt->bindValue('passWord',$password);
    return($stmt->execute());
}
function get_login_attempts($username){
    /*
    Function get username as parameter , query the database for userId and his login_attempts.
    return user Object with 3 fileds (id,username,login_attempts). 
    */
    global $db;
    $query = "SELECT id,username,login_attempts FROM users WHERE username='$username' LIMIT 1 ";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetch();
}
function set_login_attempts($username,$login_attempt){
    /*
    Function get username and amount of attempts , update the attempts on the database.
    */
    global $db;
    $query = "UPDATE users SET login_attempts=:loginAttempt WHERE username=:userName";
    $stmt = $db->prepare($query);
    $stmt->bindValue('userName',$username);
    $stmt->bindValue('loginAttempt',$login_attempt);
    $stmt->execute();
}
function set_token($userName,$value){
    global $db;
    $query = "UPDATE users SET token=:value WHERE username=:userName";
    $stmt = $db->prepare($query);
    $stmt->bindValue('userName',$userName);
    $stmt->bindValue('value',$value);
    return $stmt->execute();
    
}
function check_token($token){
    global $db;
    $query = "SELECT username FROM users WHERE token=:token LIMIT 1";
    $stmt =  $db->prepare($query);
    $stmt->bindValue('token',$token);
    $stmt->execute();
    $res = $stmt->fetch();
    if($res){
        return $res["username"];
    }else{
        return false;
    }
}
function send_token($userName){
    $user = get_user($userName);
    $token = sha1(uniqid());
    if(!set_token($userName,$token)){
        return false;
    }
    return send_email($user,$token);
}
function send_email($user,$token){
    require('vendor/autoload.php');
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 0;                                       
        $mail->isSMTP();                                            
        $mail->Host       = '';                     // SMTP server                      
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = '';                     // SMTP username
        $mail->Password   = '';                     // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port       = 587;                                   

        //Recipients
        $mail->setFrom('communicationtldmailer@gmail.com', 'Mailer');
        $mail->addAddress($user['email'], $user['username']);       // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Rest Token';
        $mail->Body    = '<h1>This is your reset token - <b>'.$token.'</b></h1>';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function check_email($email){
    global $db;
    $query = "SELECT * FROM users WHERE email=:Email LIMIT 1";
    $stmt =  $db->prepare($query);
    $stmt->bindValue('Email',$email);
    $stmt->execute();
    $res = $stmt->fetch();
    if($res){
        return true;
    }else{
        return false;
    }
}

function get_client($firstName,$lastName){
    /*
    Function get username as input , query the database for the user and return a user object.
    */
    global $db;
    $query = "SELECT * FROM clients WHERE firstname=:firstName AND lastname=:lastName LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindValue('firstName',$firstName);
    $stmt->bindValue('lastName',$lastName);
    $stmt->execute();
    $result = $stmt->rowcount();
    if ($result==1) {
      return $found_Account=$stmt->fetch();
    }else {
      return null;
    }
}

?>