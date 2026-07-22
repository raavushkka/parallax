<?
include('./assets/connect/connect.php');
include('./assets/connect/head.php');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Параллакс</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="./assets/js/main.js" defer></script>
    <link rel="shortcut icon" href="./assets/img/home/fav.svg" type="image/x-icon">
</head>

<body>
    <div class="wrapper">
        <?
        include('./components/header.php');
        ?>
        <main>
            <?
            if (isset($_GET['page'])) {
                $page =  $_GET['page'];
                if ($page == 'home') {
                    include('./pages/home.php');
                } elseif ($page == 'team') {
                    include('./pages/team.php');
                } elseif ($page == 'catalog') {
                    include('./pages/catalog.php');
                } elseif ($page == 'certificate') {
                    include('./pages/certificate.php');
                } elseif ($page == 'phPage') {
                    include('./pages/phPage.php');
                } elseif ($page == 'productPage') {
                    include('./pages/productPage.php');
                } elseif ($page == 'reg') {
                    include('./pages/reg.php');
                } elseif ($page == 'auto') {
                    include('./pages/auto.php');
                } elseif ($page == 'lk') {
                    include('./pages/lk.php');
                } elseif ($page == 'lkZakaz') {
                    include('./pages/lkZakaz.php');
                } elseif ($page == 'lkZapis') {
                    include('./pages/lkZapis.php');
                } elseif ($page == 'lkPh') {
                    include('./pages/lkPh.php');
                } elseif ($page == 'lkPortfolioPh') {
                    include('./pages/lkPortfolioPh.php');
                } elseif ($page == 'lkZapisPh') {
                    include('./pages/lkZapisPh.php');
                } elseif ($page == 'adminLk') {
                    include('./pages/adminLk.php');
                } elseif ($page == 'adminPh') {
                    include('./pages/adminPh.php');
                } elseif ($page == 'adminCertificate') {
                    include('./pages/adminCertificate.php');
                } elseif ($page == 'adminPhotos') {
                    include('./pages/adminPhotos.php');
                } elseif ($page == 'adminPackets') {
                    include('./pages/adminPackets.php');
                } elseif ($page == 'adminCategory') {
                    include('./pages/adminCategory.php');
                } elseif ($page == 'adminZakaz') {
                    include('./pages/adminZakaz.php');
                } elseif ($page == 'adminUsers') {
                    include('./pages/adminUsers.php');
                } elseif ($page == 'editLkPh') {
                    include('./pages/forms/editLkPh.php');
                } elseif ($page == 'addPortfolio') {
                    include('./pages/forms/addPortfolio.php');
                } elseif ($page == 'editZapis') {
                    include('./pages/forms/editZapis.php');
                } elseif ($page == 'addPh') {
                    include('./pages/forms/addPh.php');
                } elseif ($page == 'addCertificat') {
                    include('./pages/forms/addCertificat.php');
                } elseif ($page == 'addPhoto') {
                    include('./pages/forms/addPhoto.php');
                } elseif ($page == 'addPacket') {
                    include('./pages/forms/addPacket.php');
                } elseif ($page == 'editLkAdmin') {
                    include('./pages/forms/editLkAdmin.php');
                } elseif ($page == 'editCertificate') {
                    include('./pages/forms/editCertificate.php');
                } elseif ($page == 'editPacket') {
                    include('./pages/forms/editPacket.php');
                } elseif ($page == 'editPhoto') {
                    include('./pages/forms/editPhoto.php');
                } elseif ($page == 'editLkUser') {
                    include('./pages/forms/editLkUser.php');
                } elseif ($page == 'cart') {
                    include('./pages/cart.php');
                } elseif ($page == 'editPortfolio') {
                    include('./pages/forms/editPortfolio.php');
                } elseif ($page == 'error403') {
                    include('./pages/error403.php');
                } else {
                    include('./pages/error404.php');
                }
            } else {
                include('./pages/home.php');
            }
            ?>
            <?
            include('./components/footer.php');
            ?>
        </main>
        <div class="noise-wrapper">
            <div class="noise"></div>
        </div>
    </div>
</body>

</html>