<?
if (isset($_SESSION['USER'])) {
    if ($USER['role'] == 2) {
?>
        <header>
            <a href="?page=home" class="">
                <div class="logo1">
                    <img src="assets/img/home/logo.svg" alt="#">
                </div>
            </a>
            <a href="?page=home" class="mobile-logo">
                <div class="logo-img">
                    <img src="assets/img/home/logo.svg" alt="#">
                </div>
            </a>
        </header>
    <?
    } elseif ($USER['role'] == 3) {
    ?>
        <header>
            <a href="?page=home" class="">
                <div class="logo1">
                    <img src="assets/img/home/logo.svg" alt="#">
                </div>
            </a>
            <a href="?page=home" class="mobile-logo">
                <div class="logo-img">
                    <img src="assets/img/home/logo.svg" alt="#">
                </div>
            </a>
        </header>
    <?
    } elseif ($USER['role'] == 1) {
    ?>
        <header>
            <div class="container">
                <a href="?page=home" class="mobile-logo">
                    <div class="logo-img">
                        <img src="assets/img/home/logo.svg" alt="#">
                    </div>
                </a>

                <nav class="nav_block">
                    <nav class="nav_left">
                        <a href="?page=home">главная</a>
                        <a href="?page=team">команда</a>
                        <a href="?page=catalog">фотосессии</a>
                    </nav>

                    <a href="?page=home" class="">
                        <div class="logo1">
                            <img src="assets/img/home/logo.svg" alt="#">
                        </div>
                    </a>

                    <nav class="nav_right">
                        <a href="?page=certificate">сертификаты</a>
                        <div class="dropdown">
                            <button onclick="myFunction(event)" class="dropbtn">профиль</button>
                            <div id="myDropdown" class="dropdown-content">
                                <a href="?page=lk">профиль</a>
                                <a href="?page=lkZapis">записи</a>
                                <a href="?page=lkZakaz">заказы</a>
                                <a href="?exit">выйти</a>
                            </div>
                        </div>
                        <!-- В хедере добавь ссылку на корзину -->
                        <a href="?page=cart">корзина</a>
                    </nav>
                </nav>

                <div class="burger" onclick="openMenu(event)"><img src="assets/img/home/burger.svg" alt=""></div>
            </div>

            <div class="menu-section">
                <button class="close-btn" onclick="closeMenu()"><img src="assets/img/home/Path.svg" alt=""></button>
                <ul class="mobile-menu">
                    <li><a href="?page=home" onclick="closeMenu()">главная</a></li>
                    <li><a href="?page=team" onclick="closeMenu()">команда</a></li>
                    <li><a href="?page=catalog" onclick="closeMenu()">фотосессии</a></li>
                    <li><a href="?page=certificate" onclick="closeMenu()">сертификаты</a></li>
                    <li class="events-menu-container">
                        <a href="#" class="events-menu-btn" onclick="toggleEventsSubmenu(event)">профиль</a>
                        <ul class="events-submenu" id="events-submenu">
                            <li><a href="?page=lk" onclick="closeMenu()">профиль</a></li>
                            <li><a href="?page=lkZapis" onclick="closeMenu()">записи</a></li>
                            <li><a href="?page=lkZakaz" onclick="closeMenu()">заказы</a></li>
                            <li><a href="?page=" onclick="closeMenu()">выйти</a></li>
                        </ul>
                    </li>
                    <li><a href="?page=cart" onclick="closeMenu()">корзина</a></li>
                </ul>
            </div>

            <div id="overlay" onclick="closeMenu()"></div>
        </header>
    <?
    }
    ?>

<?
} else {
?>
    <header>
        <div class="container">
            <a href="?page=home" class="mobile-logo">
                <div class="logo-img">
                    <img src="assets/img/home/logo.svg" alt="#">
                </div>
            </a>

            <nav class="nav_block">
                <nav class="nav_left">
                    <a href="?page=home">главная</a>
                    <a href="?page=team">команда</a>
                    <a href="?page=catalog">фотосессии</a>
                </nav>

                <a href="?page=home" class="">
                    <div class="logo1">
                        <img src="assets/img/home/logo.svg" alt="#">
                    </div>
                </a>

                <nav class="nav_right">
                    <a href="?page=certificate">сертификаты</a>
                    <a href="?page=auto">войти</a>
                    <a href="?page=auto">корзина</a>
                </nav>
            </nav>

            <div class="burger" onclick="openMenu(event)"><img src="assets/img/home/burger.svg" alt=""></div>
        </div>

        <div class="menu-section">
            <button class="close-btn" onclick="closeMenu()"><img src="assets/img/home/Path.svg" alt=""></button>
            <ul class="mobile-menu">
                <li><a href="?page=home" onclick="closeMenu()">главная</a></li>
                <li><a href="?page=team" onclick="closeMenu()">команда</a></li>
                <li><a href="?page=catalog" onclick="closeMenu()">фотосессии</a></li>
                <li><a href="?page=certificate" onclick="closeMenu()">сертификаты</a></li>
                <li class="events-menu-container">
                    <a href="#" class="events-menu-btn" onclick="toggleEventsSubmenu(event)">профиль</a>
                    <ul class="events-submenu" id="events-submenu">
                        <li><a href="?page=lk" onclick="closeMenu()">профиль</a></li>
                        <li><a href="?page=lkZapis" onclick="closeMenu()">записи</a></li>
                        <li><a href="?page=lkZakaz" onclick="closeMenu()">заказы</a></li>
                        <li><a href="?page=" onclick="closeMenu()">выйти</a></li>
                    </ul>
                </li>
                <li><a href="?page=cart" onclick="closeMenu()">корзина</a></li>
            </ul>
        </div>

        <div id="overlay" onclick="closeMenu()"></div>
    </header>
<?
}
?>