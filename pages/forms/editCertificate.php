<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {

            // Получаем данные сертификата для редактирования
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "SELECT * FROM `certificate` WHERE `id`='$id'";
                $cert = $connect->query($sql)->fetch();
            }

            if (isset($_POST['update'])) {
                $name = $_POST['name'];
                $price = $_POST['price'];

                $flag = true;

                $errors = [
                    '<p class="error">Введите данные</p>',
                    '<p class="error">Превышено максимальное количество символов</p>',
                    '<p class="error">Цена должна быть числом</p>'
                ];
            }
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
                            <a href="#" class="nav-item">выйти</a>
                        </div>
                    </nav>

                    <div class="cabinet-content">
                        <div class="profile-form">
                            <h2 class="title">редактирование сертификата</h2>
                            <form method="post" name="update" class="form">
                                <div class="form-group">
                                    <label for="name" class="form-label">введите название</label>
                                    <input type="text" id="name" class="form-input" placeholder="премиум" name="name" value="<?= $name ?? $cert['name'] ?>" maxlength="16" required>
                                    <?
                                    if (isset($_POST['update'])) {
                                        if (empty($name)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($name) > 15) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label for="price" class="form-label">введите цену</label>
                                    <input type="text" id="price" class="form-input" placeholder="10000" name="price" value="<?= $price ?? $cert['price'] ?>" maxlength="11" required>
                                    <?
                                    if (isset($_POST['update'])) {
                                        if (empty($price)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (!is_numeric($price)) {
                                            $flag = false;
                                            echo $errors[2];
                                        } elseif (mb_strlen($price) > 10) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>
                                <input type="submit" class="btn btn-hover-red-wh-fon" value="сохранить" name="update">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    <?
            if (isset($_POST['update'])) {
                if ($flag) {
                    // Очищаем цену от пробелов и преобразуем в число
                    $clean_price = intval(str_replace(' ', '', $price));

                    $sql = "UPDATE `certificate` SET 
                            `name` = '$name', 
                            `price` = '$clean_price' 
                            WHERE `id` = '$id'";

                    $result = $connect->query($sql);

                    if ($result) {
                        echo '<script>document.location.href="?page=adminCertificate"</script>';
                    } else {
                        echo '<p class="error">Ошибка при обновлении сертификата</p>';
                    }
                }
            }
        } else {
            echo '<script>document.location.href="?page=error403"</script>';
        }
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
    ?>
</div>