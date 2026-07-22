<?
if (isset($_SESSION['USER'])) {
    if ($USER['role'] == 1) {
?>
        <div class="personal-cabinet-wrapper mt80">
            <div class="container">
                <div class="personal-cabinet">
                    <nav class="sidebar">
                        <div class="user-info">
                            <div class="avatar">
                                <img src="<?= $USER['img'] ?>" alt="фото профиля">
                            </div>
                            <div class="user-name"><?= $USER['name'] ?></div>
                        </div>

                        <div class="nav-menu">
                            <a href="?page=lk" class="nav-item">профиль</a>
                            <a href="?page=lkZapis" class="nav-item">записи</a>
                            <a href="?page=lkZakaz" class="nav-item">заказы</a>
                            <a href="?exit" class="nav-item">выйти</a>
                        </div>
                    </nav>

                    <div class="cabinet-content">
                        <div class="profile-form ">
                            <h2 class="title">Профиль</h2>
                            <div class="form-group">
                                <label for="name">имя</label>
                                <input type="text" id="name" value="<?= $USER['name'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="surname">фамилия</label>
                                <input type="text" id="surname" value="<?= $USER['surname'] ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">телефон</label>
                                <input type="tel" id="phone" value="<?= $USER['phone'] ?>">
                            </div>
                            <a href="?page=editLkUser">
                                <input class="btn btn-hover-red-wh-fon" value="изменить">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
} else {
    echo '<script>document.location.href="?page=error403"</script>';
}
