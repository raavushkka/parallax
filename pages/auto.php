<div class="stars-catalog mt120 container">
    <img src="assets/img/home/black_star.svg" alt="#" class="star_catalog_left2">
    <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left3">
    <img src="assets/img/home/black_star.svg" alt="#" class="star_catalog_left4">
</div>

<div class="reg container">
    <h2 class="title">
        авторизация
    </h2>
    <div class="registration-container container">
        <div class="registration-form">
            <?php


            if (isset($_POST['auto'])) {
                $phone = $_POST['phone'];
                $password = $_POST['password'];

                $flag = true;

                $errors = [
                    '<p class="error">Введите данные</p>',
                    '<p class="error">Телефон должен содержать 11 цифр</p>',
                    '<p class="error">Неверный формат телефона</p>',
                    '<p class="error">Неверный пароль или номер</p>',
                    '<p class="error">Вы не зарегистрированы</p>',
                    '<p class="error">Вы заблокированы</p>',
                ];
            }
            ?>
            <form class="form" method="post" name="auto">
                <!-- Поле телефона -->
                <div class="form-group">
                    <label for="phone" class="form-label">телефон</label>
                    <input type="tel" id="phone" class="form-input" name="phone"
                        placeholder="+7 (___) ___-__-__"
                        value="<?= isset($_POST['phone']) ? ($_POST['phone']) : '' ?>" maxlength="18">
                    <?php
                    if (isset($_POST['auto'])) {
                        $phone = $_POST['phone'] ?? '';
                        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

                        if (empty($cleanPhone)) {
                            $flag = false;
                            echo $errors[0];
                        } elseif (strlen($cleanPhone) !== 11) {
                            $flag = false;
                            echo $errors[1];
                        } elseif (!preg_match('/^[78]\d{10}$/', $cleanPhone)) {
                            $flag = false;
                            echo $errors[2];
                        } else {
                            $sql = "SELECT * FROM `user` WHERE `phone`='$phone'";
                            $res = $connect->query($sql)->fetchColumn();
                            if ($res == 0) {
                                $flag = false;
                                echo $errors[4];
                            }
                        }
                    }
                    ?>
                </div>

                <script>
                    document.getElementById('phone').addEventListener('input', function(e) {
                        let numbers = this.value.replace(/\D/g, '').substr(0, 11);
                        let formatted = '+7';
                        if (numbers.length > 1) formatted += ' (' + numbers.substr(1, 3);
                        if (numbers.length > 4) formatted += ') ' + numbers.substr(4, 3);
                        if (numbers.length > 7) formatted += '-' + numbers.substr(7, 2);
                        if (numbers.length > 9) formatted += '-' + numbers.substr(9, 2);
                        this.value = formatted;
                    });
                </script>

                <!-- Поле пароля -->
                <div class="form-group">
                    <label for="password" class="form-label">пароль</label>
                    <input type="password" id="password" class="form-input" name="password" placeholder="Введите пароль">
                    <?php
                    if (isset($_POST['auto'])) {
                        $sql = "SELECT * FROM `user` WHERE `phone`='$phone'";
                        $result = $connect->query($sql)->fetch();

                        if (empty($password)) {
                            $flag = false;
                            echo $errors[0];
                        } elseif ($result['role'] == 4) { // Проверка на блокировку
                            $flag = false;
                            echo $errors[5]; // Выводим ошибку "Вы заблокированы"
                        } elseif (!password_verify($password, $result['password'])) {
                            $flag = false;
                            echo $errors[3];
                        }
                    }
                    ?>
                </div>

                <!-- Кнопка авторизации -->
                <input type="submit" class="btn btn-hover-red-wh-fon" value="войти" name="auto">

                <!-- Ссылка на регистрацию -->
                <div class="form-footer">
                    <p class="footer-text">
                        у вас еще нет аккаунта?
                        <a href="?page=reg" class="footer-link">зарегистрироваться</a>
                    </p>
                </div>
            </form>

            <?php
            if (isset($_POST['auto'])) {
                if ($flag) {
                    $_SESSION['USER'] = $result['id'];
                    if ($result['role'] == 2) {
                        echo '<script>document.location.href="?page=adminLk"</script>';
                    } elseif ($result['role'] == 3) {
                        echo '<script>document.location.href="?page=lkPh"</script>';
                    } else {
                        echo '<script>document.location.href="?page=lk"</script>';
                    }
                }
            }

            ?>
        </div>
    </div>

    <div class="stars-catalog mt120">
        <img src="assets/img/home/black_star.svg" alt="#" class="star_catalog_left7">
        <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left8">
        <img src="assets/img/home/black_star.svg" alt="#" class="star_catalog_left9">
    </div>
</div>