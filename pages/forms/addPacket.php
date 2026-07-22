<div class="personal-cabinet-wrapper mt80">
    <?
    if (isset($_SESSION['USER'])) {
        if ($USER['role'] == 2) {
            if (isset($_POST['add'])) {
                $name = $_POST['name'];
                $duration = $_POST['duration'];
                $sources = $_POST['sources'];
                $processing = $_POST['processing'];
                $rent = $_POST['rent'];
                $stylist = $_POST['stylist'];
                $visagiste = $_POST['visagiste'];
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
                            <h2 class="title">добавление пакета</h2>
                            <form method="post" name="add" class="form">
                                <div class="form-group">
                                    <label for="name" class="form-label">введите название</label>
                                    <input type="text" id="name" class="form-input" placeholder="название" name="name" value="<?= $name ?? '' ?>" maxlength="20">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($name)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($name) > 19) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="duration" class="form-label">введите длительность</label>
                                    <input type="text" id="duration" class="form-input" placeholder="от 2 часов" name="duration" value="<?= $duration ?? '' ?>" maxlength="20">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($duration)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($duration) > 19) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="sources" class="form-label">введите количество исходников</label>
                                    <input type="text" id="sources" class="form-input" placeholder="до 200 шт" name="sources" value="<?= $sources ?? '' ?>" maxlength="20">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($sources)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($sources) > 19) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="processing" class="form-label">введите количество обработанных кадров</label>
                                    <input type="text" id="processing" class="form-input" placeholder="15-20 кадров" name="processing" value="<?= $processing ?? '' ?>" maxlength="16">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($processing)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($processing) > 15) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="rent" class="form-label">введите статус аренды студии</label>
                                    <input type="text" id="rent" class="form-input" placeholder="бесплатно" name="rent" value="<?= $rent ?? '' ?>" maxlength="15">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($rent)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($rent) > 14) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="stylist" class="form-label">введите работу стилиста</label>
                                    <input type="text" id="stylist" class="form-input" placeholder="6 образов" name="stylist" value="<?= $stylist ?? '' ?>" maxlength="11">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($stylist)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($stylist) > 10) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="visagiste" class="form-label">введите работу визажиста</label>
                                    <input type="text" id="visagiste" class="form-input" placeholder="макияж" name="visagiste" value="<?= $visagiste ?? '' ?>" maxlength="11">
                                    <?
                                    if (isset($_POST['add'])) {
                                        if (empty($visagiste)) {
                                            $flag = false;
                                            echo $errors[0];
                                        } elseif (mb_strlen($visagiste) > 10) {
                                            $flag = false;
                                            echo $errors[1];
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label for="price" class="form-label">введите цену</label>
                                    <input type="text" id="price" class="form-input" placeholder="8000" name="price" value="<?= $price ?? '' ?>" maxlength="11">
                                    <?
                                    if (isset($_POST['add'])) {
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

                                <input type="submit" class="btn btn-hover-red-wh-fon" value="добавить" name="add">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    <?
            if (isset($_POST['add'])) {
                if ($flag) {
                    // Очищаем цену от пробелов и преобразуем в число
                    $clean_price = intval(str_replace(' ', '', $price));

                    $sql = "INSERT INTO `packets` (`name`, `duration`, `sources`, `processing`, `rent`, `stylist`, `visagiste`, `price`) 
                            VALUES ('$name', '$duration', '$sources', '$processing', '$rent', '$stylist', '$visagiste', '$clean_price')";

                    var_dump($sql);

                    $result = $connect->query($sql);

                    if ($result) {
                        echo '<script>document.location.href="?page=adminPackets"</script>';
                    } else {
                        echo '<p class="error">Ошибка при добавлении пакета</p>';
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