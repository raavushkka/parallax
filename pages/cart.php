<?
session_start();

if (isset($_SESSION['USER'])) {
    $user_id = $_SESSION['USER'];

    // Получаем данные пользователя
    $sql = "SELECT * FROM user WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$user_id]);
    $USER = $stmt->fetch();

    if ($USER && $USER['role'] == 1) {
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

        // Получаем товары из корзины
        $sql = "SELECT ci.*, c.name, c.price 
                FROM cart_item ci
                JOIN certificate c ON ci.product_id = c.id
                WHERE ci.cart_id = ?";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$cartId]);
        $cartItems = $stmt->fetchAll();

        $cartPrice = 0;

        // Сначала рассчитываем общую сумму корзины
        foreach ($cartItems as $cartItem) {
            $product_id = $cartItem['product_id'];
            $sql = "SELECT * FROM certificate WHERE id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            $itemPrice = $product['price'] * $cartItem['count'];
            $cartPrice += $itemPrice;
        }

        // Обработка оплаты через ЮKassa
        if (isset($_POST['buy']) && !empty($cartItems)) {
            // Создаем заказ в базе данных
            $sql = "INSERT INTO orders (user_id, price, status) VALUES (?, ?, 'оплачен')";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$user_id, $cartPrice]);
            $order_id = $connect->lastInsertId();

            // Добавляем товары в заказ
            foreach ($cartItems as $cartItem) {
                $productId = $cartItem['product_id'];
                $count = $cartItem['count'];
                $sql = "INSERT INTO order_item (certificate_id, order_id, count) VALUES (?, ?, ?)";
                $stmt = $connect->prepare($sql);
                $stmt->execute([$productId, $order_id, $count]);
            }

            // Идемпотентность
            $idempotentKey = uniqid('', true);

            // Формируем массив с данными о платеже
            $paymentData = [
                'amount' => [
                    'value' => number_format($cartPrice, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'capture' => true,
                // В блоке обработки ЮKassa замените return_url:
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                ],
                'description' => 'Заказ №' . $order_id,
                'metadata' => [
                    'order_id' => $order_id,
                    'user_id' => $user_id
                ]
            ];

            // URL API ЮKassa для создания платежей
            $url = 'https://api.yookassa.ru/v3/payments';

            // Настройки для HTTP-запроса
            $option = [
                'http' => [
                    'header' => "Content-Type: application/json\r\n" .
                        "Idempotence-Key: " . $idempotentKey . "\r\n" .
                        "Authorization: Basic " . base64_encode("1178497:test_KRP-CbJQKnnfEq2Eueb58MhSf6KAsyu8GDH3mD1V2Vk"),
                    'method' => 'POST',
                    'content' => json_encode($paymentData, JSON_UNESCAPED_UNICODE)
                ]
            ];

            $context = stream_context_create($option);
            $result = file_get_contents($url, false, $context);

            if ($result === false) {
                echo "<script>alert('Ошибка при создании платежа');</script>";
            } else {
                $response = json_decode($result, true);

                // Очищаем корзину только после успешного создания платежа
                foreach ($cartItems as $cartItem) {
                    $productId = $cartItem['product_id'];
                    $sql = "DELETE FROM cart_item WHERE product_id = ? AND cart_id = ?";
                    $stmt = $connect->prepare($sql);
                    $stmt->execute([$productId, $cartId]);
                }

                // Перенаправляем на страницу оплаты
                header('location:' . $response['confirmation']['confirmation_url']);
                exit();
            }
        }
?>

        <div class="container mt120">
            <div class="cart">
                <h2 class="title">КОРЗИНА СЕРТИФИКАТОВ</h2>

                <div class="cart-items-container">
                    <?
                    if (empty($cartItems)) {
                        echo '<div class="empty-cart-message">У вас еще нет заказов.<br>Сертификаты в ожидании...</div>';
                    } else {
                        // Переменная для отображения суммы в HTML
                        $displayCartPrice = 0;

                        foreach ($cartItems as $cartItem) {
                            $product_id = $cartItem['product_id'];
                            $sql = "SELECT * FROM certificate WHERE id = ?";
                            $stmt = $connect->prepare($sql);
                            $stmt->execute([$product_id]);
                            $product = $stmt->fetch();

                            $itemPrice = $product['price'] * $cartItem['count'];
                            $displayCartPrice += $itemPrice;
                    ?>
                            <div class="cart-item">
                                <div class="cart-item-name"><?= htmlspecialchars($product['name']) ?></div>
                                <div class="cart-item-controls">
                                    <div class="quantity-controls">
                                        <a href="?page=cart&minus=<?= $cartItem['product_id'] ?>" class="quantity-btn plus-btn">
                                            <img src="assets/img/modal/-.svg" alt="Увеличить">
                                        </a>
                                        <span class="quantity-display"><?= $cartItem['count'] ?></span>
                                        <a href="?page=cart&plus=<?= $cartItem['product_id'] ?>" class="quantity-btn minus-btn">
                                            <img src="assets/img/modal/+.svg " alt="Уменьшить">
                                        </a>
                                    </div>
                                    <div class="cart-item-info">
                                        <div class="cart-item-price"><?= number_format($itemPrice, 0, '', ' ') ?> ₽</div>
                                        <a href="?page=cart&delete=<?= $cartItem['product_id'] ?>" class="remove-btn delete-item-btn"
                                            data-id="<?= $cartItem['product_id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>">
                                            <img src="assets/img/modal/Shape.svg" alt="Удалить">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="cart-divider"></div>
                        <? } ?>

                        <!-- Итоговая сумма -->
                        <div class="cart-total">
                            <p class="total-price">Общая сумма: <?= number_format($displayCartPrice, 0, '', ' ') ?> ₽</p>
                        </div>

                        <!-- Кнопка оформления заказа -->
                        <div class="cart-actions">
                            <form action="" method="post" style="display: inline;">
                                <button type="submit" name="buy" class="btn btn-hover-red-wh-fon checkout-btn">оформить заказ</button>
                            </form>
                            <a href="?page=certificate" class="btn continue-shopping-btn">продолжить покупки</a>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>

        <?
        // Обработка уменьшения количества
        if (isset($_GET['minus'])) {
            $productId = $_GET['minus'];

            // Получаем текущее количество
            $sql = "SELECT * FROM cart_item WHERE cart_id = ? AND product_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$cartId, $productId]);
            $item = $stmt->fetch();

            if ($item && $item['count'] <= 1) {
                // Удаляем товар если количество 1 или меньше
                $sql = "DELETE FROM cart_item WHERE product_id = ? AND cart_id = ?";
                $stmt = $connect->prepare($sql);
                $stmt->execute([$productId, $cartId]);
            } else {
                // Уменьшаем количество
                $sql = "UPDATE cart_item SET count = count - 1 WHERE product_id = ? AND cart_id = ?";
                $stmt = $connect->prepare($sql);
                $stmt->execute([$productId, $cartId]);
            }

            echo '<script>document.location.href="?page=cart"</script>';
        }

        // Обработка увеличения количества
        if (isset($_GET['plus'])) {
            $productId = $_GET['plus'];

            // Увеличиваем количество
            $sql = "UPDATE cart_item SET count = count + 1 WHERE cart_id = ? AND product_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$cartId, $productId]);

            echo '<script>document.location.href="?page=cart"</script>';
        }
        ?>

        <!-- Модальное окно подтверждения удаления -->
        <div id="confirmDeleteModal" class="form1" style="display: none;">
            <div class="form_content w35">
                <div class="close_right modal-close-btn">
                    <span><img src="assets/img/modal/Path.svg" alt="Закрыть"></span>
                </div>
                <div class="content_deletemk_modal">
                    <h2 class="title">Удаление из корзины</h2>
                    <p class="modal-body">
                        Вы действительно хотите удалить «<span id="modalItemName"></span>» из корзины?
                    </p>
                    <div class="modal-footer">
                        <button class="btn btn-black modal-close-btn">нет</button>
                        <a id="confirmDeleteBtn" href="#" class="btn" style="text-align: center;">да</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('confirmDeleteModal');
                const modalItemName = document.getElementById('modalItemName');
                const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

                // Открытие модального окна
                document.querySelectorAll('.delete-item-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();

                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');

                        modalItemName.textContent = name;
                        confirmDeleteBtn.href = `?page=cart&delete=${id}`;
                        modal.style.display = 'flex';
                        document.body.style.overflow = 'hidden';
                    });
                });

                // Закрытие модального окна
                document.querySelectorAll('.modal-close-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        modal.style.display = 'none';
                        document.body.style.overflow = '';
                    });
                });

                // Закрытие при клике вне модального окна
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                        document.body.style.overflow = '';
                    }
                });
            });
        </script>

<?
        // Обработка удаления товара
        if (isset($_GET['delete'])) {
            $productId = $_GET['delete'];

            // Удаляем товар из корзины
            $sql = "DELETE FROM cart_item WHERE product_id = ? AND cart_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$productId, $cartId]);

            // Перенаправляем обратно в корзину
            echo '<script>document.location.href="?page=cart"</script>';
        }
    } else {
        echo '<script>document.location.href="?page=error403"</script>';
    }
} else {
    echo '<script>document.location.href="?page=error403"</script>';
}
?>