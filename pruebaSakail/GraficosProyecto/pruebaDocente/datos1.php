<?php

require_once 'db.php';

$conexion = new conexion();

$conexion->verificarConexion();

$conexion->conexion->query("SET NAMES 'utf8mb4'"); 

$semana = 968;
$curso = 'PRO54-ON-QUIMICA_INDUSTRIAL-2024-1';
$permission = 1;

$conexion->conexion->query("SET @semana1 = $semana");
$conexion->conexion->query("SET @curso1 = '$curso'");
$conexion->conexion->query("SET @permission1 = $permission");

$sql = "SELECT 
            su.FIRST_NAME AS NOMBRES, 
            su.LAST_NAME AS APELLIDOS,
            ROUND(
                (SUM(CASE WHEN a.done = 1 THEN 1 ELSE 0 END) / 
                (SELECT COUNT(*) 
                 FROM sakail.lesson_builder_ch_status 
                 WHERE checklistId = @semana1 
                   AND owner = su.USER_ID)) * 100, 
            2) AS PORCENTAJE_REALIZADO
        FROM 
            sakail.SAKAI_USER su
        JOIN
            sakail.SAKAI_SITE_USER ssu ON su.USER_ID = ssu.USER_ID
        JOIN
            sakail.lesson_builder_ch_status a ON a.owner = su.USER_ID
        JOIN
            sakail.SAKAI_SITE site ON ssu.SITE_ID = site.SITE_ID
        WHERE
            ssu.SITE_ID = @curso1
            AND ssu.PERMISSION = @permission1
            AND a.checklistId = @semana1
        GROUP BY
            su.FIRST_NAME, su.LAST_NAME
        ORDER BY
            PORCENTAJE_REALIZADO DESC";

$stmt = $conexion->conexion->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $nombre = mb_convert_encoding($row['NOMBRES'], 'UTF-8', 'auto');
        $apellido = mb_convert_encoding($row['APELLIDOS'], 'UTF-8', 'auto');

        $usuarios[] = [
            'nombre' => $nombre . ' ' . $apellido,
            'porcentaje' => (float)$row['PORCENTAJE_REALIZADO']
        ];
    }
    $porcentajeRealizado = $usuarios;
} else {
    $porcentajeRealizado = [];
}

$stmt->close();

$stmt = $conexion->conexion->prepare("SELECT title FROM sakail.SAKAI_SITE WHERE SITE_ID = ?");
$stmt->bind_param("s", $curso);
$stmt->execute();
$stmt->bind_result($titulo);
$stmt->fetch();

$conexion->conexion->close();

$datos = [
    'titulo' => $titulo,
    'porcentajeIngreso' => $porcentajeRealizado
];

$jsonData = json_encode($datos['porcentajeIngreso'], JSON_UNESCAPED_UNICODE);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'Error en la codificación JSON: ' . json_last_error_msg();
    exit;
}

?>