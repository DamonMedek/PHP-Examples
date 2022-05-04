<?php
session_start();
$servername = "localhost";
$dbroot = "";
$dbpassword = "";
$dbname = "";

// Create connection
$conn = new mysqli($servername, $dbroot, $dbpassword, $dbname);
// Check connection
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://ipnpb.paypal.com/cgi-bin/webscr');
//curl_setopt($ch, CURLOPT_URL, 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "cmd=_notify-validate&" . http_build_query($_POST));
$response = curl_exec($ch);
curl_close($ch);

if ($response == "VERIFIED" && $_POST['receiver_email'] == "Damon@gmail.com")
{
    $cEmail = $_POST['payer_email'];
    $name = $_POST['first_name'] . " " . $_POST['last_name'];
    $price = $_POST['mc_gross'];
    $currency = $_POST['mc_currency'];
    $item = $_POST['item_name'];
    $txn_id = $_POST['txn_id'];
    $paymentStatus = $_POST['payment_status'];
    $paypalmember = $_POST['custom'];
    $paypal_date = $_POST['payment_date'];
    $timestamp = strtotime($paypal_date);
    $mysql_formatted = date("Y-m-d H:i:s", $timestamp);

    if ($currency == "USD" && $paymentStatus == "Completed")
    {

        $sql = "INSERT INTO donations (paypal_email, txn_id, amount, createdtime)
				VALUES ('$cEmail', '$txn_id', '$price', '$mysql_formatted')";

        if ($conn->query($sql) === true)
        {
            echo "Record updated successfully";
        }
        else
        {
            echo "Error updating record: " . $conn->error;
        }
    }

    $conn->close();
}
?>
