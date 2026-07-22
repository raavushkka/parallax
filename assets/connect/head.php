<?
session_start();
if (isset($_SESSION['USER'])) {
    $id = $_SESSION['USER'];

    $sql = "SELECT user.*, cart.id AS cartId FROM user LEFT JOIN cart 
    ON cart.id_user = user.id WHERE user.id = '$id'";
    $USER = $connect->query($sql)->fetch();
    $cartId = $USER['cartId'];
    $user_id = $USER['id'];
}

if (isset($_GET['exit'])) {
    unset($_SESSION['USER']);
    echo '<script> document.location.href="?page=home"</script>';
}
