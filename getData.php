<?php
include 'db.php';

$sql = "SELECT nombre, COUNT(*) as cantidad FROM usuarios GROUP BY id";
$result = $conn->query($sql);

$data = array();
$total = 0;

// Calcular el total
while ($row = $result->fetch_assoc()) {
    $total += $row['cantidad'];
}

// Obtener los porcentajes
$result->data_seek(0); // Reiniciar el puntero del resultado
while ($row = $result->fetch_assoc()) {
    $porcentaje = ($row['cantidad'] / $total) * 100;
    $data[] = array('nombre' => $row['nombre'], 'porcentaje' => $porcentaje);
}

echo json_encode($data);
$conn->close();
?>