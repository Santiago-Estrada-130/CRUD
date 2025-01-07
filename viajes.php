<?php
    session_start();
    ob_start();
    include 'conexion.php';

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = $_POST['action'];

        if ($action == 'agregar') {
            $destino = $_POST['destino'];
            $cantidad = $_POST['cantidad'];
            $precio = $_POST['precio'];

            $stmt = $conn->prepare("INSERT INTO viajes (destino, cantidad, precio) VALUES (?, ?, ?)");
            $stmt->bind_param("sid", $destino, $cantidad, $precio);
            if ($stmt->execute()) {
                header("Location: viajes.php");
                exit();
            } else {
                echo "Error al agregar el boleto: " . $conn->error;
            }
            $stmt->close();
        } elseif ($action == 'modificar') {
            $id = $_POST['id'];
            $destino = $_POST['destino'];
            $cantidad = $_POST['cantidad'];
            $precio = $_POST['precio'];

            $stmt = $conn->prepare("UPDATE viajes SET destino=?, cantidad=?, precio=? WHERE id=?");
            $stmt->bind_param("sidi", $destino, $cantidad, $precio, $id);

            if ($stmt->execute()) {
                header("Location: viajes.php");
                exit();
            } else {
                echo "Error al modificar la venta: " . $conn->error;
            }
            $stmt->close();
        } elseif ($action == 'eliminar') {
            $id = $_POST['id'];

            $stmt = $conn->prepare("DELETE FROM viajes WHERE id=?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                header("Location: viajes.php");
                exit();
            } else {
                echo "Error al eliminar la venta: " . $conn->error;
            }
            $stmt->close();
        }
    }
    $result = $conn->query("SELECT * FROM viajes");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link href="diseño.css" rel="stylesheet" type="text/css">
    <meta charset="utf-8">
    <title>Sistema de Reservas - Agencia de Viajes</title>
    <script>
        function calcularPrecioTotal() {
            var cantidad = document.getElementById("cantidad").value;
            var precio = document.getElementById("precio").value;
            var total = cantidad * precio;
            document.getElementById("precioTotal").value = total.toFixed(2); 
        }
    </script>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1 class="logo">Agencia de Viajes</h1>
            <div class="user-info">
                <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <a href="logout.php">
                    <button class="logout">Cerrar Sesión</button>
                </a>
            </div>
        </header>

        <section class="form-section">
            <h2>Registrar Nuevo Boleto</h2>
            <form method="POST" action="viajes.php" class="form">
                <input type="hidden" name="action" value="agregar">
                <div class="form-group">
                    <label for="destino">Destino</label>
                    <input list="ciudades" id="destino" name="destino" required class="campo">
                    <datalist id="ciudades">
                        <option value="Buenos Aires">
                        <option value="Madrid">
                        <option value="Paris">
                        <option value="New York">
                        <option value="Londres">
                        <option value="Tokio">
                        <option value="Los Angeles">
                        <option value="Sídney">
                        <option value="Ciudad de México">
                        <option value="Berlin">
                        <option value="Milan">
                        <option value="Dubai">
                        <option value="Barcelona">
                        <option value="Lima">
                        <option value="Chicago">
                        <option value="Toronto">
                        <option value="Moscú">
                        <option value="Viena">
                        <option value="Roma">
                        <option value="Lisboa">
                        <option value="Estocolmo">
                        <option value="Singapur">
                        <option value="Hong Kong">
                        <option value="Dubai">
                        <option value="Bangkok">
                        <option value="San Francisco">
                        <option value="Los Angeles">
                        <option value="Sydney">
                     </datalist>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" required class="campo" oninput="calcularPrecioTotal()">
                </div>
                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input type="number" id="precio" step="0.01" name="precio" required class="campo" oninput="calcularPrecioTotal()">
                </div>
                <div class="form-group">
                    <label for="precioTotal">Precio Total</label>
                    <input type="text" id="precioTotal" name="precioTotal" readonly class="campo">
                </div>
                <button type="submit" class="btn btn-primary">Agendar vuelo</button>
            </form>
        </section>

     
        <section class="table-section">
            <h2>Historial de Boletos</h2>
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Destino</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Precio Total</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST" action="viajes.php">
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><input type="text" name="destino" value="<?php echo htmlspecialchars($row['destino']); ?>" required></td>
                                <td><input type="number" name="cantidad" value="<?php echo htmlspecialchars($row['cantidad']); ?>" required oninput="calcularPrecioTotal()"></td>
                                <td><input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($row['precio']); ?>" required oninput="calcularPrecioTotal()"></td>
                                <td><input type="text" name="precioTotal" value="<?php echo htmlspecialchars($row['precio'] * $row['cantidad']); ?>" readonly></td>
                                <td><?php echo htmlspecialchars($row['Fecha']); ?></td>
                                <td>
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" name="action" value="modificar" class="btn btn-secondary">Modificar</button>
                                    <button type="submit" name="action" value="eliminar" class="btn btn-danger">Eliminar</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
