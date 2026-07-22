<div class="stars-catalog mt120 container">
    <img src="assets/img/home/black_star.svg" alt="#" class="star_catalog_left2">
    <img src="assets/img/home/red_star.svg" alt="#" class="star_catalog_left3">
    <img src="assets/img/home/black_star.svg" alt="#" class="star_catalog_left4">
</div>

<div class="reg container ">
    <h2 class="title">
        регистрация
    </h2>
    <div class="registration-container container">
        <div class="registration-form">
            <?php
            if (isset($_POST['reg'])) {
                $name = $_POST['name'];
                $phone = $_POST['phone'];
                $password = $_POST['password'];
                $passwordR = $_POST['passwordR'];
                $img = "assets/img/modal/zaglushka.png";

                $flag = true;

                $errors = [
                    '<p class="error">Введите данные</p>',
                    '<p class="error"Телефон должен содержать 11 цифр</p>',
                    '<p class="error">Неверный формат телефона</p>',
                    '<p class="error">Пароль не менее 6 символов</p>',
                    '<p class="error">Пароли не совпадают</p>',
                    '<p class="error">Вы уже зарегистрированы</p>',
                    '<p class="error">Необходимо согласие на обработку данных</p>',
                    '<p class="error">Превышено максимальное количество символов</p>'
                ];
            }
            ?>
            <form class="form" method="post" name="reg">
                <!-- Поле имени -->
                <div class="form-group">
                    <label for="name" class="form-label">имя</label>
                    <input type="text" id="name" class="form-input" name="name" placeholder="Введите ваше имя"
                        value="<?php if (isset($_POST['reg'])) {
                                    echo $name;
                                } ?>" maxlength="31">
                    <?php
                    if (isset($_POST['reg'])) {
                        if (empty($name)) {
                            $flag = false;
                            echo $errors[0];
                        } elseif (mb_strlen($name) > 30) {
                            $flag = false;
                            echo $errors[7];
                        }
                    }
                    ?>
                </div>

                <!-- Поле телефона -->
                <div class="form-group">
                    <label for="phone" class="form-label">телефон</label>
                    <input type="tel" id="phone" class="form-input" name="phone"
                        placeholder="+7 (___) ___-__-__"
                        value="<?php if (isset($_POST['reg'])) {
                                    echo $phone;
                                } ?>" maxlength="18">
                    <?php
                    if (isset($_POST['reg'])) {
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
                            if ($res != 0) {
                                $flag = false;
                                echo $errors[5];
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
                    if (isset($_POST['reg'])) {
                        if (empty($password)) {
                            $flag = false;
                            echo $errors[0];
                        } elseif (strlen($password) < 6) {
                            $flag = false;
                            echo $errors[3];
                        }
                    }
                    ?>
                </div>

                <!-- Подтверждение пароля -->
                <div class="form-group">
                    <label for="confirm-password" class="form-label">подтверждение пароля</label>
                    <input type="password" id="confirm-password" class="form-input" name="passwordR" placeholder="Повторите пароль">
                    <?php
                    if (isset($_POST['reg'])) {
                        if (empty($passwordR)) {
                            $flag = false;
                            echo $errors[0];
                        } elseif ($password != $passwordR) {
                            $flag = false;
                            echo $errors[4];
                        }
                    }
                    ?>
                </div>

                <!-- Чекбокс согласия -->
                <div class="form-checkbox">
                    <div class="input-label-checkbox">
                        <input type="checkbox" id="agreement" class="checkbox-input" name="checkbox" <?php if (isset($_POST['reg']) && isset($_POST['checkbox'])) echo 'checked'; ?>>
                        <label for="agreement" class="checkbox-label">
                            я согласен(а)<span><a href="assets/documents/personality.pdf" download="согласие на обработку персональных данных.pdf">на обработку персональных данных*</a></span>
                        </label>
                    </div>
                    <?php
                    if (isset($_POST['reg'])) {
                        if (!isset($_POST['checkbox'])) {
                            $flag = false;
                            echo $errors[6];
                        }
                    }
                    ?>
                </div>

                <!-- Кнопка регистрации -->
                <input type="submit" class="btn btn-hover-red-wh-fon" value="зарегистрироваться" name="reg">

                <!-- Ссылка на вход -->
                <div class="form-footer">
                    <p class="footer-text">
                        у вас уже есть аккаунт?
                        <a href="?page=auto" class="footer-link">войти</a>
                    </p>
                </div>
            </form>

            <?php
            if (isset($_POST['reg'])) {
                if ($flag) {
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $default_img = "assets/img/modal/zaglushka.png";
                    $sql = "INSERT INTO `user`(`img`, `name`, `surname`, `phone`, `password`, `role`) VALUES ('$default_img','$name','$surname','$phone','$password','1')";
                    $res = $connect->query($sql);
                    //для корзины
                    $sql = "SELECT `id` FROM `user` ORDER BY `id` DESC LIMIT 1";
                    $user_id = $connect->query($sql)->fetch()['id'];
                    $sql = "INSERT `cart` (id_user) VALUES ('$user_id')";
                    $connect->query($sql);
                    echo '<script>document.location.href="?page=auto"</script>';
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