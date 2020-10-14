# Comunication_LTD

In this project, I was asked to build a vulnerable web app for an imaginary company called Comunication_ltd and demonstrate the attacks and mitigations. All the attacks and payload I used are documented in the code comments as well as screenshots.

# Project Requirements:

```
Part 1:

Create a Vunlerable web app which include the following functionalities:

  Registration page:
    - create new users.
    - check password complixity using configuration file.
    - password will be stoe on database using HMAC+salt.
    - force HTTPS.
  
  Change password screed:
    - insert old password.
    - create new password will required complixty from the configuration file.
    - force HTTPS.
    
  Login page:
    - username filed .
    - password filed.
    - check wheter user exists on database , if not return error.
    - validate user credentials .
    - force HTTPS.
    
   Forgot Password:
    - user activate this functionality.
    - system generates random token hashed with SHA-1.
    - token is send to user email and redirect the user to enter token screen.
    - user enter the token , if valid - redirect to reset password screen.
    - force HTTPS.
  
  System Dashboard:
    - create new clients.
    - display client to screen.
    
  Password configuration file:
    - password lenght 10.
    - complex password using [Capitals,small letters,symbols,digits].
    - forbid common passwords.
    - forbid use of last 3 passwords.
    - user blocked after 3 login attempts.


Part 2:

  1. Demonstrate Stored XXS attack on system dashboard.
  2. Demonstrate SQL Injection on login page,system dashboard,registration page.
  3. Provide solution against the XXS.
  4. Provide solution against SQLI.

```
  
# Installation.

1. Download xammp with MySql and PHP 7.4.8  https://www.apachefriends.org/download.html .

2. After you finish installation start apache and my sql servers with SSL enable !

3. clone the repo or download the folder comunication_ltd to xampp/htdocs folder.

3. Go to http://127.0.0.1/phpmyadmin and create new database name "comunication_ltd".

4. import the file comunication_ltd.sql to the new created database.

5. Change the database connection creds if needed in the file include/database.php.

6. Add your SMTP username and password under include/functions.php (function send_mail). 

7. Browse to https://127.0.0.1/comunication_tld/login.php .

8. Enjoy Exploiting the app :) .

