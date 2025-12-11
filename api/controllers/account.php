<?php
require_once ROOT . "content/content.php";
require_once ROOT . "functions/users.php";
require_once ROOT . "functions/account.php";
require_once "controllers/user.php";
class ApiAccount extends account
{
    public $user;
    public function __construct()
    {
        // Call parent constructor to set the name
        parent::__construct();
        $this->user = new ApiUser;
    }

    function getCategories()
    {
        $cat = [];
        $categories = $this->getall("category", select: "ID, name", fetch: "all");
        if ($categories != "") {
            $cat = $categories->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->apiMessage("Categories fetched", 200, $cat);
    }
    function getPlatfroms()
    {
        $plat = [];
        $platforms = $this->getall("platform", select: "ID, name, CONCAT('http://cruisetechlogs.com/app/assets/images/icons/', icon) AS icon, login_url", fetch: "all");
        if ($platforms != "") {
            $plat = $platforms->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->apiMessage("Platforms fetched", 200, $plat);
    }

    function fetchAccounts()
    {
        $start = $_GET['page'] ?? 1;
        $start = (int)$start - 1;
        $limit = 50;
        $start = $limit * (int)$start;
        $category = $_GET['category'] ?? "all";
        $platform = $_GET['platform'] ?? "";
        $acct = [];
        if (isset($_GET['search']) && $_GET['search'] != "") {
            $_GET['s'] =  $_GET['search'];
        }

        $accounts = $this->fetch_account((int)$start, $platform, $limit, 1, $category, "ID, platformID, categoryID, title, preview_link, amount, description");
        if ($accounts != "") $acct = $accounts->fetchAll(PDO::FETCH_ASSOC);
        return $this->apiMessage("Account fetched", 200, $acct);
    }

    function getLogins()
    {
        if (isset($_GET['ID'])) {
            $logins = $this->getall("logininfo", "ID = ? and (sold_to = ? or sold_to = ?)", [htmlspecialchars($_GET['ID']), "", $this->user->userID], "ID, accountID, login_details, username, preview_link, sold_to");
            if(!is_array($logins)) return $this->apiMessage("Login not found", 400);
            if($logins['sold_to'] != $this->user->userID && isset($logins['login_details'])) {
              unset($logins['login_details']);
            }
            if(isset($logins['sold_to'])) unset($logins['sold_to']);
            return $this->apiMessage("Login fetched", 200, $logins);
        }

        if (!isset($_GET['accountID']) || $_GET['accountID'] == "") {
            return $this->apiMessage("Account ID not passed", 401);
        }
        
        $accountID = htmlspecialchars($_GET['accountID']);
        if (isset($_GET['username'])) {
            $logins = $this->getall("logininfo", "accountID = ? and username = ? and sold_to = ?", [$accountID, htmlspecialchars($_GET['username']), ""], "ID, accountID, username, preview_link");
            return $this->apiMessage("Logins fetched", 200, !is_array($logins) ? [] : $logins);
        }
        $start = $_GET['page'] ?? 1;
        $start = (int)$start - 1;
        $limit = 50;
        $start = $limit * (int)$start;
        $exclude = [];
        if (isset($_POST['exclude']) && $_POST['exclude'] != "") {
            $exclude = explode(',', htmlspecialchars($_POST['exclude']));
        }
        $logins = $this->fetchLoginsByAccountID($accountID, $limit, $start, $exclude);
        if ($logins != "") $logins = $logins->fetchAll(PDO::FETCH_ASSOC);
        return $this->apiMessage("Logins fetched", 200, $logins ?? []);
    }
    function buyAccount()
    {
        if (!isset($_GET['accountID']) || $_GET['accountID'] == "") {
            return $this->apiMessage("Account ID not passed", 401);
        }

        $qty = $_POST['qty'] ?? 1;
        $logins = [];
        if (isset($_POST['choices']) && $_POST['choices'] != "") {
            $logins = explode(',', htmlspecialchars($_POST['choices']));
        }
        try {
            $buy = $this->buy_account($this->user->userID, htmlspecialchars($_GET['accountID']), $qty, $logins, true);
            if (!is_array($buy)) {
                return $this->handleJsonMessage($buy);
            }
            return $this->apiMessage("successful", 200, $buy);
        } catch (\Throwable $th) {
            $this->apiMessage("Something went wrong $th", 500);
        }
    }
}