<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {
    ?>
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
                            <a href="?page=adminLk" class="nav-item">профиль</a>
                            <a href="?page=adminPh" class="nav-item">фотографы</a>
                            <a href="?page=adminCertificate" class="nav-item">сертификаты</a>
                            <a href="?page=adminPhotos" class="nav-item">фотосессии</a>
                            <a href="?page=adminPackets" class="nav-item">пакеты фотосессий</a>
                            <a href="?page=adminCategory" class="nav-item">категории фотосессий</a>
                            <a href="?page=adminZakaz" class="nav-item">заказы</a>
                            <a href="?page=adminUsers" class="nav-item">пользователи</a>
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
                            <a href="?page=editLkAdmin">
                                <input class="btn btn-hover-red-wh-fon" value="изменить">
                            </a>
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
    ?>
</div>