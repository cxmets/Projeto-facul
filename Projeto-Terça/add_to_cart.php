<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];

    require_once 'connection.php';

    $sql = "SELECT * FROM products WHERE id = :productId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] += 1;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cartItem = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1
            ];
            $_SESSION['cart'][] = $cartItem;
        }

        // array pra calcular qnt de itens no carrinho
        $totalQuantity = array_sum(array_column($_SESSION['cart'], 'quantity'));

        // retorna a qnt total em json
        echo json_encode(['totalQuantity' => $totalQuantity]);
    } else {
        echo json_encode(['error' => 'Produto não encontrado!']);
    }
} else {
    echo json_encode(['error' => 'Requisição inválida!']);
}
?>
