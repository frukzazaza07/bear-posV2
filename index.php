<?php

// First, let's define our list of routes.
// We could put this in a different file and include it in order to separate
// logic and configuration.
$routes = array(
    // front
    '/'      => 'view/pos.php',
    '/view/bill-detail' => 'view/bill-detail.php',
    '/view/user-login' => 'view/user-login.php',
    // '/view/pos' => 'view/pos.php',
    '/view/company-login' => 'view/company-login.php', 
    '/view/admin-menu/add-product' => 'view/admin-menu/add-product.php',
    // api
    '/engine/api/cancelBills' => '/engine/api/cancelBills',
    '/engine/api/checkCompanyUserPermission' => '/engine/api/checkCompanyUserPermission',
    '/engine/api/companyLogin' => '/engine/api/companyLogin',
    '/engine/api/insertBills' => '/engine/api/insertBills',
    '/engine/api/loadBill' => '/engine/api/loadBill',
    '/engine/api/loadBillMessage' => '/engine/api/loadBillMessage',
    '/engine/api/loadBills' => '/engine/api/loadBills',
    '/engine/api/loadCompany' => '/engine/api/loadCompany',
    '/engine/api/loadDailySales' => '/engine/api/loadDailySales',
    '/engine/api/loadDiscounts' => '/engine/api/loadDiscounts',
    '/engine/api/loadPaymentsType' => '/engine/api/loadPaymentsType',
    '/engine/api/loadProducts' => '/engine/api/loadProducts',
    '/engine/api/loadTypeProduct' => '/engine/api/loadTypeProduct',
    '/engine/api/returnToFontend' => '/engine/api/returnToFontend',
    '/engine/api/socialLogin' => '/engine/api/socialLogin',
    '/engine/api/usersLogin' => '/engine/api/usersLogin',
    '/engine/api/usersRegister' => '/engine/api/usersRegister',
);

// This is our router.
function router($routes)
{
    $mainFolder = "/bear";
    $actualLink = $_SERVER["REQUEST_URI"];
    $checkRequest = $_SERVER["REQUEST_METHOD"];
    // Iterate through a given list of routes.
    if ($checkRequest != "GET" && $checkRequest != "POST") {
        return "Method don't match.";
    }
    foreach ($routes as $path => $content) {
        $path = $mainFolder . $path;
        if ($path == $actualLink) {
            require_once($content);
            die();
        }
    }

    // This can only be reached if none of the routes matched the path.
    http_response_code(404);
}

// Execute the router with our list of routes.
router($routes);
