<?php
require_once("connect.php");
class authUsers
{
    public $setUserFieldRegister = [
        "users_username",
        "users_password",
        "users_firstname",
        "users_lastname",
        "users_address",
        "users_province",
        "users_amphur",
        "users_tambon",
        "users_tel",
        "users_email",
        "users_active",
    ];
    public $setCompanyUserFieldRegister = [
        "company_users_id",
        "company_users_username",
        "company_users_password",
        "company_users_firstname",
        "company_users_lastname",
        "company_users_nickname",
        "company_users_address",
        "company_users_province",
        "company_users_amphur",
        "company_users_tambon",
        "company_users_tel",
        "company_users_email",
        "company_users_active",
        "company_id",
        "company_branch_id",
        "position_id",
    ];
    public $userUsername = "";
    public $userPassword = "";
    public $socialLogin = "";

    public function userLogin()
    {
        try {
            // มาเช็ค login facebook
            $connectDb = new connectDb();
            // $userJson = json_decode($usersData);
            $sql = "SELECT " . implode(",", $this->setUserFieldRegister) . " , users_active, users_id
                    FROM users
                    WHERE 
                    (users_username = :usersUsername 
                    AND users_password = :usersPassword)
                    OR social_login = :socialLogin";

            $stmt = $connectDb->conn->prepare($sql);
            $stmt->bindParam(':usersUsername', $this->userUsername);
            $stmt->bindParam(':usersPassword', $this->userPassword);
            $stmt->bindParam(':socialLogin', $this->socialLogin);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    public function userRegister($usersData)
    {
        try {
            // return $usersData;
            $connectDb = new connectDb();
            // $userJson = json_decode($usersData);
            // $usersData = $this->setUserField($userJson);
            $insert_values = array();

            foreach ($usersData as $d) {
                $question_marks[] = '('  . $this->placeholders('?', sizeof($d)) . ')';
                $insert_values = array_merge($insert_values, array_values($d));
            }
            $sql = "INSERT INTO users (" . implode(",", $this->setUserFieldRegister) . ") VALUES " .
                implode(',', $question_marks);
            $stmt = $connectDb->conn->prepare($sql);
            $stmt->execute($insert_values);

            return true;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    private function placeholders($text, $count = 0, $separator = ",")
    {
        $result = array();
        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }

    public function companyLogin()
    {
        try {
            $connectDb = new connectDb();
            // $userJson = json_decode($usersData);
            $sql = "SELECT " . implode(",", $this->setCompanyUserFieldRegister) . "
                    FROM company_users 
                    WHERE 
                    company_users_username = :usersUsername 
                    AND company_users_active = 1 ";
            // AND company_users_password = :usersPassword
            $stmt = $connectDb->conn->prepare($sql);
            $stmt->bindParam(':usersUsername', $this->userUsername);
            // $stmt->bindParam(':usersPassword', $this->userPassword);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }
    public function checkLogin()
    {
        // start check session login
        if (!isset($_SESSION["usersLogin"]) || !$_SESSION["usersLogin"]["loginStatus"]) header("location: http://localhost/bear/view/company-login");
        // end check session login
    }
    public function checkLoginPermission()
    {
        // start check session login
        if (!isset($_SESSION["usersLogin"]) || !$_SESSION["usersLogin"]["loginStatus"] || $_SESSION["usersLogin"]["positionId"] > 3) header("location: http://localhost/bear/view/company-login");
        // end check session login
    }
}
