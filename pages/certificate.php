<div class="certificate-banner ">
    <div class="sectors-banner-certif container">
        <div class="right-cert-banner">
            <h2 class="title">
                Дарите родным и близким впечатления
            </h2>
            <p class="desc-cert-banner">
                Подарочный сертификат на фотосессию – это эмоция, застывшая в кадре, и возможность создать зримую историю своей уникальности. Погрузитесь в мир света и тени, чтобы вместе с нами превратить мгновение в искусство, а образ – в высказывание.
            </p>
            <a href="#certificate"><input type="submit" class="btn btn-hover-red-bl-fon" value="выбрать сертификат"></a>
        </div>
        <div class="left-cert-banner">
            <div class="cert1">
                <img src="assets/img/certificate/certific.png" alt="">
            </div>
            <div class="certif-banner-star">
                <img src="assets/img/home/red_star.svg" alt="">
            </div>
            <div class="cert2">
                <img src="assets/img/certificate/cartific2.png" alt="">
            </div>
            <div class="certif-banner-star1">
                <img src="assets/img/home/red_star.svg" alt="">
            </div>
        </div>
    </div>
    <div class="marquee">
        <div class="marquee-content">
            <div class="star-text">
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
                <span>параллакс <img src="assets/img/home/white_full_star.svg" alt=""></span>
            </div>
        </div>
    </div>
</div>

<div class="certs mt120" id="certificate">
    <h2 class="title">
        подарочный сертификат <br> в фотостудию
    </h2>
    <p class="subtitle">
        Такой подарок станет настоящим сюрпризом и оставит <br> только самые яркие и теплые воспоминания
    </p>
    <div class="cards-container container">
        <!-- Первая большая карточка -->
        <div class="card large-card">
            <div class="card-image">
                <h3 class="card-title">формат на выбор: электронный / печатный</h3>
                <div class="img-card-cert">
                    <img src="assets/img/certificate/hands.png" alt="Название услуги">
                </div>
                <div class="img-card-cert-mini">
                    <img src="assets/img/certificate/mini-cert.png" alt="Название услуги">
                </div>
            </div>
            <div class="card-content">
                <!-- Можно добавить кнопку и для большой карточки если нужно -->
            </div>
        </div>

        <?php
        // Получаем сертификаты из базы данных
        $sql = "SELECT * FROM certificate ORDER BY price ASC";
        $stmt = $connect->prepare($sql);
        $stmt->execute();
        $certificates = $stmt->fetchAll();

        // Разделяем сертификаты на две колонки
        $first_column = array_slice($certificates, 0, 2); // Первые два сертификата
        $second_column = array_slice($certificates, 2);   // Остальные сертификаты
        ?>

        <!-- Блок с двумя маленькими карточками (первый столбец) -->
        <div class="small-cards-column">
            <?php foreach ($first_column as $certificate): ?>
                <div class="card small-card">
                    <div class="card-image">
                        <h3 class="card-title"><?= htmlspecialchars($certificate['name']) ?></h3>
                    </div>
                    <div class="card-content">
                        <div class="card-bottom">
                            <p class="card-price"><?= number_format($certificate['price'], 0, '', ' ') ?> ₽</p>
                            <!-- В файле certificate.php замени кнопки "выбрать" на: -->
                            <?
                            if (isset($_SESSION['USER'])) {
                                if ($USER['role'] == 1) {
                            ?><a href="?page=certificate&toCart=1&id=<?= $certificate['id'] ?>" class="btn btn-hover-red-wh-fon">выбрать</a><?
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                            ?><a href="?page=auto" class="btn btn-hover-red-wh-fon">выбрать</a><?
                                                                                                                                                                                                            }
                                                                                                                                                                                                                ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Блок с двумя маленькими карточками (второй столбец) -->
        <div class="small-cards-column">
            <?php foreach ($second_column as $certificate): ?>
                <div class="card small-card">
                    <div class="card-image">
                        <h3 class="card-title"><?= htmlspecialchars($certificate['name']) ?></h3>
                    </div>
                    <div class="card-content">
                        <div class="card-bottom">
                            <p class="card-price"><?= number_format($certificate['price'], 0, '', ' ') ?> ₽</p>
                            <!-- В файле certificate.php замени кнопки "выбрать" на: -->
                            <?
                            if (isset($_SESSION['USER'])) {
                                if ($USER['role'] == 1) {
                            ?><a href="?page=certificate&toCart=1&id=<?= $certificate['id'] ?>" class="btn btn-hover-red-wh-fon">выбрать</a><?
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                            ?><a href="?page=auto" class="btn btn-hover-red-wh-fon">выбрать</a><?
                                                                                                                                                                                                            }
                                                                                                                                                                                                                ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Статическая карточка "на ваш вкус" -->
            <div class="card small-card">
                <div class="card-image">
                    <h3 class="card-title gray-lk">*действителен 6 месяцев с даты покупки</h3>
                </div>
                <div class="card-content">

                </div>
            </div>
        </div>
    </div>
</div>

<?
// Обработка добавления в корзину (добавь в начало certificate.php)
if (isset($_GET['toCart']) && isset($_GET['id'])) {
    $user_id = $_SESSION['USER'];
    $product_id = $_GET['id'];

    // Получаем ID корзины пользователя
    $sql = "SELECT id FROM cart WHERE id_user = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$user_id]);
    $cart = $stmt->fetch();

    if (!$cart) {
        // Если корзины нет - создаем новую
        $sql = "INSERT INTO cart (id_user, price) VALUES (?, '0')";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$user_id]);
        $cartId = $connect->lastInsertId();
    } else {
        $cartId = $cart['id'];
    }

    // Проверяем, есть ли уже этот товар в корзине
    $sql = "SELECT * FROM cart_item WHERE cart_id = ? AND product_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$cartId, $product_id]);
    $existingItem = $stmt->fetch();

    if ($existingItem) {
        // Если товар уже есть в корзине - увеличиваем количество
        $sql = "UPDATE cart_item SET count = count + 1 WHERE cart_id = ? AND product_id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$cartId, $product_id]);
    } else {
        // Если товара нет в корзине - добавляем
        $sql = "INSERT INTO cart_item (cart_id, product_id, count) VALUES (?, ?, 1)";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$cartId, $product_id]);
    }

    // Показываем модальное окно
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("AddToCart").style.display = "flex";
            document.body.style.overflow = "hidden";
        });
    </script>';
}
?>

<!-- Модальное окно добавления в корзину -->
<div id="AddToCart" class="form1" style="display: none;">
    <div class="form_content w35">
        <div class="close_right modal-close-btn">
            <span><img src="assets/img/modal/Path.svg" alt="Закрыть"></span>
        </div>
        <div class="content_deletemk_modal">
            <h2 class="title">успешно!</h2>
            <?
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "SELECT * FROM certificate WHERE id = ?";
                $stmt = $connect->prepare($sql);
                $stmt->execute([$id]);
                $cert = $stmt->fetch();
            ?>
                <p class="modal_mk_desc">«<?= $cert['name'] ?>» добавлен в корзину</p>
            <?
            }
            ?>
            <div class="edit_del cart_btns_modal">
                <a href="?page=cart" class="btn">в корзину</a>
                <button class="btn btn_line_red_hover continue-shopping-btn">к покупкам</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Закрытие модального окна
        document.querySelectorAll('.modal-close-btn, .continue-shopping-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('AddToCart').style.display = 'none';
                document.body.style.overflow = '';
            });
        });

        // Закрытие при клике вне окна
        document.getElementById('AddToCart').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                document.body.style.overflow = '';
            }
        });

        // Убираем параметры из URL после добавления в корзину
        <?php if (isset($_GET['toCart'])): ?>
            history.replaceState(null, "", "?page=certificate");
        <?php endif; ?>
    });
</script>

<!-- primeri rabot -->
<div class="raboti mt120">
    <h2 class="title">
        Примеры работ
    </h2>
    <div class="photo-gallery">
        <div class="left-column">
            <div class="img-container">
                <img src="assets/img/home/rab1.png" alt="" class="small">
            </div>
            <div class="img-container">
                <img src="assets/img/home/rab2.png" alt="" class="small">
            </div>
        </div>

        <div class="middle-column">
            <div class="img-container">
                <img src="assets/img/home/rab3.png" alt="" class="medium">
            </div>
            <div class="img-container">
                <img src="assets/img/home/rab4.png" alt="" class="medium">
            </div>
        </div>

        <div class="large-column">
            <div class="img-container">
                <img src="assets/img/home/rab5.png" alt="" class="large">
            </div>
        </div>

        <div class="right-column">
            <div class="img-container">
                <img src="assets/img/home/rab6.png" alt="" class="small">
            </div>
            <div class="img-container">
                <img src="assets/img/home/rab7.png" alt="" class="small">
            </div>
        </div>
    </div>
</div>