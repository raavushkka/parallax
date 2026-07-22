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
                        <div class="profile-form admin-users">
                            <div class="btns-portfolio">
                                <h2 class="title">пользователи</h2>
                            </div>
                            <div class="cards-zapisi-user">
                                <?
                                $sql = "SELECT * FROM `user` WHERE `role` = 1 OR `role` = 4";
                                $users = $connect->query($sql);

                                foreach ($users as $u) {
                                ?>
                                    <div class="card-zapisi-user">
                                        <div class="info-zapis-user">
                                            <div class="info-double">
                                                <p class="photogr">
                                                    имя: <span><?= htmlspecialchars($u['name']) ?></span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="info-double">
                                            <p class="photogr">
                                                телефон: <span><?= htmlspecialchars($u['phone']) ?></span>
                                            </p>
                                        </div>

                                        <?
                                        if ($u['role'] == 1) {
                                        ?>
                                            <a href="?page=adminUsers&block=<?= $u['id'] ?>" class="btn edit">заблокировать</a>
                                        <?
                                        } elseif ($u['role'] == 4) {
                                        ?>
                                            <a href="?page=adminUsers&unblock=<?= $u['id'] ?>" class="btn btn-no-back edit">разблокировать</a>
                                        <?
                                        }
                                        ?>
                                    </div>
                                <?
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?
            // Обработка блокировки
            if (isset($_GET['block'])) {
                $id = $_GET['block'];
                $sql = "UPDATE `user` SET `role` = '4' WHERE `id`='$id'";
                $connect->query($sql);
                echo '<script>document.location.href="?page=adminUsers"</script>';
            }

            // Обработка разблокировки
            if (isset($_GET['unblock'])) {
                $id = $_GET['unblock'];
                $sql = "UPDATE `user` SET `role` = '1' WHERE `id`='$id'";
                $connect->query($sql);
                echo '<script>document.location.href="?page=adminUsers"</script>';
            }
        } else {
            echo '<script>document.location.href="?page=error403"</script>';
        }
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
    ?>
</div>