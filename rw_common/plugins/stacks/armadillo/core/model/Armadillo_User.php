<?php

class Armadillo_User
{
    protected $id;
    public $realname;
    protected $email;
    public $username;
    private $password;
    protected $role;

    public function setID( $newID )
    {
        $this->id = $newID;
    }

    public function getID()
    {
        return $this->id;
    }

    public function setRealname( $newName )
    {
        $this->realname = $newName;
    }

    public function getRealname()
    {
        return $this->realname;
    }

    public function setEmail( $newEmail )
    {
        $this->email = $newEmail;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setUsername( $newUsername )
    {
        $this->username = $newUsername;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword( $newPassword )
    {
        $this->password = $newPassword;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setRole( $newRole )
    {
        $this->role = $newRole;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setLanguage( $newLanguage )
    {
        $this->language = $newLanguage;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public static function encryptPassword( $passwordToEncrypt )
    {
        /** PBKDF2 Implementation (described in RFC 2898)
         *  via article by Andrew Johnson on itnewb.com
         *
         *  @param string p password
         *  @param string s salt
         *  @param int c iteration count (use 1000 or higher)
         *  @param int kl derived key length
         *  @param string a hash algorithm
         *
         *  @return string derived key
        */
        function pbkdf2( $p, $s, $c, $kl, $a = 'sha256' )
        {
            $hl = strlen(hash($a, null, true)); # Hash length
            $kb = ceil($kl / $hl);              # Key blocks to compute
            $dk = '';                           # Derived key

            # Create key
            for ($block = 1; $block <= $kb; $block ++) {
                # Initial hash for this block
                $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
                # Perform block iterations
                for ( $i = 1; $i < $c; $i ++ )
                    # XOR each iterate
                    $ib ^= ($b = hash_hmac($a, $b, $p, true));
                $dk .= $ib; # Append iterated block
            }

            # Return derived key of correct length

            return substr($dk, 0, $kl);
        }

        //NOTE: If you have already published your site, and created content via Armadillo's online
        //web interface, DO NOT CHANGE the salt below, otherwise you will not be able to login!
        $salt = '^VI8n)q~uyJ8Iled/1}FR<%-[=1I-g?p88qDd]qeW`&UKgZZyF_@;T/x3G=kG~a+';

        $hash = pbkdf2($passwordToEncrypt, $salt, 1000, 32);

        return $hash;
    }

    public static function checkPassword( $passwordToCheck )
    {
        $passwordToCheck = Armadillo_User::encryptPassword($passwordToCheck);
        if ( $passwordToCheck == Armadillo_User::getPassword() ) {
            return TRUE;
        } else { return FALSE; }
    }

    public static function usernameExists( $username )
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $username = $dbLink->real_escape_string($username);
            $usernameQuery = "SELECT * FROM armadillo_user WHERE username='$username'";
            $dbLink->query($usernameQuery);
            if ($dbLink->affected_rows > 0) { return TRUE; } else { return FALSE; }

        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function checkNewUserDetails($newUserDetails, $pageParams)
    {
        $armadillo = Slim::getInstance();
        $notification = '';
        $errors = 0;

        if ($pageParams['contentState'] == 'new') {
            if ( empty($newUserDetails['username']) ) {
                $notification .= '&middot ' . Armadillo_Language::msg('ARM_USER_CREATE_USERNAME_REQUIRED') . '<br/>';
                ++$errors;
            }
            if ( Armadillo_User::usernameExists($newUserDetails['username']) ) {
                $notification .= '&middot ' . Armadillo_Language::msg('ARM_USER_CREATE_USERNAME_TAKEN') . '<br/>';
                ++$errors;
            }
            if ( empty( $newUserDetails['password'] ) or empty( $newUserDetails['confirmPassword'] ) ) {
                $notification .= '&middot ' . Armadillo_Language::msg('ARM_USER_CREATE_PASSWORD_REQUIRED') . '<br/>';
                ++$errors;
            }
        }
        if ($newUserDetails['password'] !== $newUserDetails['confirmPassword']) {
            $notification .= '&middot ' . Armadillo_Language::msg('ARM_USER_CREATE_PASSWORD_MISMATCH') . '<br/>';
            ++$errors;
        }
        if ($errors > 0) {
            $armadillo->flash("notification", "$notification");
            if ($pageParams['contentState'] = 'new') {
                session_write_close();
                $armadillo->redirect('./new/');
            } else { $armadillo->redirect('./edit/' . $newUserDetails['id'] . '/'); }
            exit;
        } else {
            $notification = '';
            array_pop($pageParams);

            return $pageParams;
        }
    }

    public static function loginUser( $username, $password, $armadilloURL )
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $username = $dbLink->real_escape_string($username);
            // Set defaults, in case db hasn't been updated yet
            $failedAttempts = 0;
            $allowLoginAfter = time();
            $allowedLoginAttempts = 10;
            $blockedLoginTimeframe = 120; //in seconds
            $userIP = $_SERVER['REMOTE_ADDR'];

            // User details
            $query = "SELECT * FROM armadillo_user WHERE username='$username'";
            // Armadillo options
            $optionsQuery = "SELECT * FROM armadillo_options LIMIT 1";

            // Execute user details query
            $result = $dbLink->query($query);

            if (!$result) {
                $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_LOGIN_DB_ERROR') . htmlentities($dbLink->error, ENT_QUOTES));
            } elseif ($dbLink->affected_rows === 0) {
                $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_LOGIN_USERNAME_INCORRECT'));
            } elseif ($dbLink->affected_rows === 1) {
                // Grab user details from returned results
                $row = $result->fetch_array();

                // Get db version from options so we can override defaults if user settings are present
                $optionsResult = $dbLink->query($optionsQuery);
                $optionsRow = $optionsResult->fetch_array();

                if ( $optionsResult && $optionsRow['armadillo_build_version'] >= 291 ) {
                    // Saved Armadillo security settings
                    $allowedLoginAttempts = $optionsRow['allowed_login_attempts'];
                    $blockedLoginTimeframe = $optionsRow['blocked_login_timeframe'];
                    // User login attempt info stored in db user table
                    $failedAttempts = $row['failed_login_attempts'];
                    $allowLoginAfter = $row['login_allowed_after'];
                }

                if ( $allowLoginAfter > time() ) {
                    $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_FAILED_LOGIN_ATTEMPTS_EXCEEDED'));
                } else {
                    
                    if ( Armadillo_User::encryptPassword( $password ) === $row['password'] && ( $failedAttempts <= $allowedLoginAttempts || $allowLoginAfter < time() ) ) {
                        $sessionID = session_id();
                        if ( empty($sessionID) ) { session_start(); }
                        $_SESSION = array();
                        $_SESSION['bootMe'] = time()+28800;
                        $_SESSION['loggedIn'] = TRUE;
                        $_SESSION['username'] = $username;
                        $_SESSION['userID'] = $row['id'];
                        $_SESSION['role'] = $row['role'];
                        $lang = isset($row['language']) ? $row['language'] : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                        $_SESSION['language'] = $lang;
                        $_SESSION['armURL'] = $armadilloURL;

                        //Set cookies so we can determine if we are logged in when on pages throughout the site, not just the dashboard area
                        setrawcookie('armadillo[bootMe]', time()+28800, time()+28800, '/');
                        setrawcookie('armadillo[loggedIn]', 'TRUE', time()+28800, '/');
                        setrawcookie('armadillo[username]', $username, time()+28800, '/');
                        setrawcookie('armadillo[userID]', $row['id'], time()+28800, '/');
                        setrawcookie('armadillo[role]', $row['role'], time()+28800, '/');
                        setrawcookie('armadillo[language]', $lang, time()+28800, '/');
                        setrawcookie('armadillo[armURL]', $armadilloURL, time()+28800, '/');
                        
                        if ($optionsResult) {
                            
                            $_SESSION['armBuildVersion'] = $optionsRow['armadillo_build_version'];

                            if ( $optionsRow['armadillo_build_version'] >= 291 ) {
                                // Login successful, so reset failed attempts to zero
                                $failedAttempts = 0;
                                // Update user login attempt info
                                $loginAttemptsQuery = "UPDATE armadillo_user SET failed_login_attempts=$failedAttempts , login_allowed_after=$allowLoginAfter , user_IP='$userIP' WHERE username='$username'";
                                // Reset failed login attempts to zero
                                $dbLink->query($loginAttemptsQuery);
                            }

                            if ( $optionsRow['armadillo_build_version'] >= 213 ) {
                                // Store settings if present
                                $_SESSION['dateFormat'] = isset($optionsRow['blog_date_format']) ? $optionsRow['blog_date_format'] : 'WMDY';
                                $_SESSION['siteLanguage'] = $optionsRow['site_language'];
                            } else {
                                // Defaults
                                $_SESSION['dateFormat'] = 'WMDY';
                                $_SESSION['siteLanguage'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                            }

                            if ( $optionsRow['armadillo_build_version'] >= 724 ) {
                                // Store settings if present
                                $_SESSION['editorType'] = $optionsRow['editor_type'];
                                setrawcookie('armadillo[editorType]', $optionsRow['editor_type'], time()+28800, '/');
                            } else {
                                // Defaults
                                $_SESSION['editorType'] = 'richtext';
                                setrawcookie('armadillo[editorType]', 'richtext', time()+28800, '/');
                            }

                            if ( $optionsRow['armadillo_build_version'] >= 738 ) {
                                $_SESSION['enableBlogContent'] = $optionsRow['enable_blog_content'];
                                $_SESSION['enablePageContent'] = $optionsRow['enable_page_content'];
                                $_SESSION['enableSoloContent'] = $optionsRow['enable_solo_content'];
                                $_SESSION['blogURL'] = isset($optionsRow['blog_url']) ? $optionsRow['blog_url'] : '';
                            } else {
                                $_SESSION['enableBlogContent'] = true;
                                $_SESSION['enablePageContent'] = true;
                                $_SESSION['enableSoloContent'] = true;
                                $_SESSION['blogURL'] = '';
                            }
                        }

                    } else {
                        unset($_SESSION['bootMe']);
                        unset($_SESSION['loggedIn']);
                        unset($_SESSION['username']);
                        unset($_SESSION['userID']);
                        unset($_SESSION['role']);
                        unset($_SESSION['language']);
                        unset($_SESSION['armURL']);
                        unset($_SESSION['armBuildVersion']);
                        unset($_SESSION['dateFormat']);
                        unset($_SESSION['siteLanguage']); 
                        unset($_SESSION['editorType']);
                        unset($_SESSION['enableBlogContent']);
                        unset($_SESSION['enablePageContent']);
                        unset($_SESSION['enableSoloContent']);
                        unset($_SESSION['blogURL']);
                        unset($_SESSION['selectedBlog']);

                        //delete cookies
                        setrawcookie('armadillo[bootMe]', '', time()-3600, '/');
                        setrawcookie('armadillo[loggedIn]', 'FALSE', time()-3600, '/');
                        setrawcookie('armadillo[username]', '', time()-3600, '/');
                        setrawcookie('armadillo[userID]', '', time()-3600, '/');
                        setrawcookie('armadillo[role]', '', time()-3600, '/'); 
                        setrawcookie('armadillo[language]', '', time()-3600, '/'); 
                        setrawcookie('armadillo[armURL]', '', time()-3600, '/'); 
                        setrawcookie('armadillo[editorType]', '', time()-3600, '/');               

                        if ( $optionsRow['armadillo_build_version'] >= 291 ) {
                            // Increase counter of failed login attempts and blocked timeframe, if any
                            $failedAttempts = $allowLoginAfter < time() && $failedAttempts > $allowedLoginAttempts ? 1 : ++$failedAttempts;
                            $allowLoginAfter = $failedAttempts >= $allowedLoginAttempts ? time() + $blockedLoginTimeframe : $allowLoginAfter;
                            // Update user login attempt info
                            $loginAttemptsQuery = "UPDATE armadillo_user SET failed_login_attempts=$failedAttempts , login_allowed_after=$allowLoginAfter , user_IP='$userIP' WHERE username='$username'";
                            $dbLink->query($loginAttemptsQuery);
                        }

                        if ( $failedAttempts >= $allowedLoginAttempts || $allowLoginAfter > time() ) {
                            $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_FAILED_LOGIN_ATTEMPTS_EXCEEDED'));
                        } else {
                            $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_LOGIN_DETAILS_INCORRECT'));
                        } 
                    }
                }
            }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function logoutUser()
    {
        $armadillo = Slim::getInstance();
        unset($_SESSION['bootMe']);
        unset($_SESSION['loggedIn']);
        unset($_SESSION['username']);
        unset($_SESSION['userID']);
        unset($_SESSION['role']);
        unset($_SESSION['language']);
        unset($_SESSION['armURL']);
        unset($_SESSION['armBuildVersion']);
        unset($_SESSION['dateFormat']);
        unset($_SESSION['siteLanguage']);
        unset($_SESSION['editorType']);
        unset($_SESSION['enableBlogContent']);
        unset($_SESSION['enablePageContent']);
        unset($_SESSION['enableSoloContent']);
        unset($_SESSION['blogURL']);
        unset($_SESSION['selectedBlog']);

        //delete cookies
        setrawcookie('armadillo[bootMe]', '', time()-3600, '/');
        setrawcookie('armadillo[loggedIn]', 'FALSE', time()-3600, '/');
        setrawcookie('armadillo[username]', '', time()-3600, '/');
        setrawcookie('armadillo[userID]', '', time()-3600, '/');
        setrawcookie('armadillo[role]', '', time()-3600, '/'); 
        setrawcookie('armadillo[language]', '', time()-3600, '/'); 
        setrawcookie('armadillo[armURL]', '', time()-3600, '/');
        setrawcookie('armadillo[editorType]', '', time()-3600, '/');

        $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_LOGOUT_SUCCESSFUL'));
    }

    public static function listUsers( $rawData='' )
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $query = "SELECT * FROM armadillo_user";

            $result = $dbLink->query($query);

            if (!$result) {
                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_USER_DISPLAY_SUMMARY_DB_ERROR') . $dbLink->error);
            }

            $list = array();

            if ( empty($result) ) { return $list; } else {
                while ( $row = $result->fetch_array() ) {
                    $list[] = array('id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'username' => $row['username'], 'role' => $row['role']);
                }

                if ( $rawData == 'true' ) {
                    return $list;
                } else {
                    //Users Summary Container
                    $userSummary = "<div id='userSummary'><table id='summaryList' class='table table-striped table-bordered responsive nowrap' cellspacing='0' width='100%'>";

                    //Users List Title Row
                    // $userSummary .= "<div class='titleRow'><div class='userName'>" . Armadillo_Language::msg('ARM_USER_SUMMARY_NAME_LABEL') . "</div><div class='userLogin'>" . Armadillo_Language::msg('ARM_USER_SUMMARY_LOGIN_ID_LABEL') . "</div><div class='userEmail'>" . Armadillo_Language::msg('ARM_USER_SUMMARY_EMAIL_LABEL') . "</div><div class='userRole'>" . Armadillo_Language::msg('ARM_USER_SUMMARY_ROLE_LABEL') . "</div><div class='userActions'>" . Armadillo_Language::msg('ARM_USER_SUMMARY_ACTIONS_LABEL') . "</div></div>";
                    // $userSummary .= "<div class='clearer'></div>";

                    $userSummary .= "<thead><tr><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_NAME_LABEL') ."</th><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_LOGIN_ID_LABEL') . "</th><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_EMAIL_LABEL') . "</th><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_ROLE_LABEL') . "</th><th class='disabled'>" . Armadillo_Language::msg('ARM_USER_SUMMARY_ACTIONS_LABEL') . "</th></tr></thead>"
                                    . "<tbody>";
                                    //. "<tfoot><tr><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_NAME_LABEL') ."</th><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_LOGIN_ID_LABEL') . "</th><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_EMAIL_LABEL') . "</th><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_ROLE_LABEL') . "</th><th>" . Armadillo_Language::msg('ARM_USER_SUMMARY_ACTIONS_LABEL') . "</th></tr></tfoot>"
                                    

                    $rowNumber = 0;

                    foreach ($list as $user) {
                        $rowNumber++;
                        $rowClass = ( $rowNumber % 2 ) ? 'oddRow' : 'evenRow';

                        $id = $user['id'];
                        $name = $user['name'];
                        $username = $user['username'];
                        $email = $user['email'];
                        $role = $user['role'];

                        $userActions = ($_SESSION['userID'] !== $id && $_SESSION['role'] === 'admin') ? "<a href='edit/$id/' class='btn btn-primary btn-sm' title='" . Armadillo_Language::msg('ARM_EDIT_TEXT') . "'><i class='fa fa-pencil-square-o fa-lg'></i></a><a href='delete/$id/' class='btn btn-danger btn-sm' title='" . Armadillo_Language::msg('ARM_DELETE_TEXT') . "'><i class='fa fa-times fa-lg'></i></a>" : "<a href='edit/$id/' class='btn btn-primary btn-sm' title='" . Armadillo_Language::msg('ARM_EDIT_TEXT') . "'><i class='fa fa-pencil-square-o fa-lg'></i></a>";

                        //Add content to User Summary
                        // $userSummary .= "<div class='userRow " . $rowClass . "'>";
                        // $userSummary .= "<div class='userName'>$name</div><div class='userLogin'><a href='edit/$id/'>$username</a></div><div class='userEmail'>$email</div><div class='userRole'>$role</div><div class='userActions'>$userActions</div>";
                        // $userSummary .= "<div class='clearer'></div>";
                        // $userSummary .= "</div>";

                        $userSummary .= "<tr><td>$name</a></td><td><a href='edit/$id/'>$username</a></td><td>$email</td><td>$role</td><td>$userActions</td></tr>";
                    }
                    // Close users summary table and HTML container
                    $userSummary .= "</tbody></table></div><div class='clearer'></div>";
                    echo $userSummary;
                }
            }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function numberOfAdminUsers()
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $query = "SELECT role, COUNT(role) FROM armadillo_user WHERE role= 'admin'";

            $result = $dbLink->query($query);

            if ($result) {
                $row = $result->fetch_array();
                $numberOfAdmins = $row['COUNT(role)'];

                return $numberOfAdmins;
            } else { return 1; }
        }
    }

    public static function getUser( $id )
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $id = $dbLink->real_escape_string($id);

            $query = "SELECT * FROM armadillo_user WHERE id = $id";

            $result = $dbLink->query($query);

            if (!$result) {
                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_USER_DISPLAY_DETAILS_DB_ERROR') . $dbLink->error);
            }

            $row = $result->fetch_array();
            $userInfo = array('id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'username' => $row['username'], 'role' => $row['role'], 'language' => $row['language']);

            return $userInfo;
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function retrieveUserAccounts( $email, $token )
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $email = $dbLink->real_escape_string($email);
            $token = $dbLink->real_escape_string($token);

            $query = "SELECT id, username FROM armadillo_user WHERE email='$email' AND token='$token'";

            $result = $dbLink->query($query);

            $userAccounts = array();

            if ( !$result or empty($result) ) { return $userAccounts; } else {
                while ( $row = $result->fetch_array() ) {
                    $userAccounts[] = array('id' => $row['id'], 'username' => $row['username']);
                }
            }

            return $userAccounts;
        }
    }

    public function saveUser( $user )
    {
        $armadillo = Slim::getInstance();
        /* Saves/Updates a user, making sure to first escape any submitted content to protect the database from injection attacks. */
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            if ($armadillo->request()->isPut()) { $id = $dbLink->real_escape_string($user->id); }
            $username = $dbLink->real_escape_string($user->username);
            $realname = $dbLink->real_escape_string($user->realname);
            $email = $dbLink->real_escape_string($user->email);
            $role = $dbLink->real_escape_string($user->role);
            $language = $dbLink->real_escape_string($user->language);

            $passwordQuery = '';
            $roleQuery = '';

            if ( !empty($user->password) ) {
                $password = $dbLink->real_escape_string($user->password);
                $password = Armadillo_User::encryptPassword($password);
                $password = $dbLink->real_escape_string($password); //Escape password again, just in case encryption added affected characters
                $user->setPassword($password);

                if ( $armadillo->request()->isPut() ) {
                    $passwordQuery = ", password='$password'";
                }
            }

            $loggedInUser = self::getUser($_SESSION['userID']);

            if ($armadillo->request()->isPost()) {
                //Prevent unauthorized people from trying to create new users
                if ( $loggedInUser['role'] != 'admin' || strtolower($loggedInUser['username']) != strtolower($_SESSION['username']) ) {           
                    $query = '';
                } else {
                    $query = "INSERT INTO armadillo_user SET
                                username='$username',
                                name='$realname',
                                email='$email',
                                role='$role',
                                language='$language',
                                password='$password'";
                }
            }

            if ($armadillo->request()->isPut()) {
                
                if ( $loggedInUser['role'] != 'admin' && $loggedInUser['role'] != $role ) {
                    //Prevent people from trying to change their role to something else if they aren't an admin
                    $roleQuery = '';
                } else {
                    //Make sure there's always at least one admin user, don't save role otherwise
                    if ($id == $_SESSION['userID']) {
                        $roleQuery = Armadillo_User::numberOfAdminUsers() > 1 ? ", role='$role'" : '';
                    } else { $roleQuery = ", role='$role'"; }
                }
                
                $query = "UPDATE armadillo_user SET
                            username='$username',
                            name='$realname',
                            email='$email',
                            language='$language'" . $roleQuery . $passwordQuery . " WHERE id='$id'";
                
            }

            $result = $query != '' ? $dbLink->query($query) : '';

            if ($result == '') {
                //$armadillo->flash('notification', 'You\'re IP address has been logged. Any unauthorized activity will be submitted to the authorities for investigation.');
            } else if (!$result) {
                $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_SAVE_FAILED') . $dbLink->error);
            } else {
                if ($_SESSION['userID'] == $user->id) { $_SESSION['language'] = $language; }
                $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_SAVE_SUCCESSFUL'));
            }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function deleteUser( $id )
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $id = $dbLink->real_escape_string($id);

            $currentUser = $dbLink->real_escape_string($_SESSION['userID']);

            if ($_SESSION['role'] === 'admin') { // Make sure only a logged in admin is trying to delete the user.
                if ($currentUser !== $id) {
                    /* Check if there is at least one admin (the currently logged in user, if nothing else).
                    If so, proceed with the deletion, but first reassign all created content by that user
                    to the admin whom is deleting the user. SIDE NOTE: We already run checks to prevent the
                    currently logged in user from deleting their own account. */
                    $adminsQuery = "SELECT role, COUNT(role) FROM armadillo_user WHERE role= 'admin'";

                    $adminsResult = $dbLink->query($adminsQuery);

                    if (!$adminsResult) {
                        $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_USER_DELETE_FAILED'));
                        exit();
                    } else {
                        $row = $adminsResult->fetch_array();
                        $numberOfAdmins = $row['COUNT(role)'];
                    }

                    if ($numberOfAdmins >= 1) {
                        $updatePostsQuery = "UPDATE armadillo_post SET userid='$currentUser' WHERE userid='$id'";

                        $postsQueryresult = $dbLink->query($updatePostsQuery);

                        if (!$postsQueryresult) {
                            $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_USER_DELETE_REASSIGN_CONTENT_FAILED') . $dbLink->error);
                            exit();
                        }

                        $deleteQuery = "DELETE FROM armadillo_user WHERE id='$id'";

                        $deleteResult = $dbLink->query($deleteQuery);

                        if (!$deleteResult) {
                            $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_USER_DELETE_FAILED') . $dbLink->error);
                        } else { $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_USER_DELETE_SUCCESSFUL')); }
                    }
                } else { $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_USER_DELETE_OWN_ACCOUNT_WARNING')); }
            } else { $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_USER_DELETE_ADMIN_REQUIRED')); }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function saveToken($token, $email)
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';
            $token = $dbLink->real_escape_string($token);
            $email = $dbLink->real_escape_string($email);
            $query = "UPDATE armadillo_user SET token='$token' WHERE email='$email'";
            $result = $dbLink->query($query);
            if (!$result) { return FALSE; } else { return TRUE; }
        } else { return FALSE; }
    }
}
