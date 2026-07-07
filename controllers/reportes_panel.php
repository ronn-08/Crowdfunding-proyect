<?php
//Incluir la conexión segura a la base de datos
require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();


//CONSULTA 1: AVANCE DE CAMPAÑAS (Meta vs Recaudado en Tiempo Real)

//Une las tablas CAUSA_SOCIAL, META_PLAZO y DONACION para calcular cuánto dinero lleva cada una.
$query_avance = "
    SELECT 
        c.Id_Causa,
        c.Titulo,
        m.Monto_Objetivo,
        IFNULL(SUM(d.Total_Donado), 0) AS Total_Recaudado,
        ROUND((IFNULL(SUM(d.Total_Donado), 0) / m.Monto_Objetivo) * 100, 2) AS Porcentaje_Progreso,
        DATEDIFF(m.Fecha_Finalizacion, NOW()) AS Dias_Restantes
    FROM CAUSA_SOCIAL c
    JOIN META_PLAZO m ON c.Id_Causa = m.CAUSA_SOCIAL_Id_Causa
    LEFT JOIN DONACION d ON c.Id_Causa = d.CAUSA_SOCIAL_Id_Causa
    GROUP BY c.Id_Causa, c.Titulo, m.Monto_Objetivo, m.Fecha_Finalizacion
    ORDER BY Porcentaje_Progreso DESC;
";
$stmt1 = $db->prepare($query_avance);
$stmt1->execute();
$reporte_avance = $stmt1->fetchAll(PDO::FETCH_ASSOC);


//CONSULTA 2: CONTABILIDAD GLOBAL (Recaudación neta y comisiones acumuladas) 
$query_contable = "
    SELECT 
        IFNULL(SUM(Total_Donado), 0) AS Recaudacion_Global,
        IFNULL(SUM(Comision), 0) AS Comisiones_Plataforma,
        COUNT(Id_Donacion) AS Total_Transacciones
    FROM DONACION;
";
$stmt2 = $db->prepare($query_contable);
$stmt2->execute();
$reporte_global = $stmt2->fetch(PDO::FETCH_ASSOC);


//CONSULTA 3: TOP DONANTES (Ranking de Filantropía)

$query_top_donantes = "
    SELECT 
        u.Nombre,
        u.Apellido,
        u.Correo,
        SUM(d.Total_Donado) AS Inversion_Total,
        COUNT(d.Id_Donacion) AS Cantidad_Aportes
    FROM USUARIO u
    JOIN DONACION d ON u.Id_Usuario = d.USUARIO_Id_Usuario
    GROUP BY u.Id_Usuario, u.Nombre, u.Apellido, u.Correo
    ORDER BY Inversion_Total DESC
    LIMIT 5;
";
$stmt3 = $db->prepare($query_top_donantes);
$stmt3->execute();
$top_donantes = $stmt3->fetchAll(PDO::FETCH_ASSOC);


//CONSULTA 4: MURO SOCIAL SEGURO (Manejo dinámico de anonimato) 

$query_muro = "
    SELECT 
        CASE 
            WHEN d.Es_Anonimo = 'S' THEN 'Donante Anónimo'
            ELSE CONCAT(u.Nombre, ' ', u.Apellido)
        END AS Nombre_Visible,
        m.Mensaje,
        m.Fecha_Publicacion,
        c.Titulo AS Proyecto_Apoyado
    FROM MURO_AGRADECIMIENTO m
    JOIN DONACION d ON m.DONACION_Id_Donacion = d.Id_Donacion
    JOIN USUARIO u ON d.USUARIO_Id_Usuario = u.Id_Usuario
    JOIN CAUSA_SOCIAL c ON d.CAUSA_SOCIAL_Id_Causa = c.Id_Causa
    WHERE m.Visibilidad = 'S'
    ORDER BY m.Id_Agradecimiento DESC
    LIMIT 5;
";
$stmt4 = $db->prepare($query_muro);
$stmt4->execute();
$comentarios_muro = $stmt4->fetchAll(PDO::FETCH_ASSOC);

//CONSULTA 5: ALERTAS DE URGENCIA CRÍTICA (Campañas en riesgo de vencer)

$query_alertas = "
    SELECT 
        c.Titulo,
        m.Monto_Minimo,
        m.Fecha_Finalizacion,
        IFNULL(SUM(d.Total_Donado), 0) AS Recaudado_Actual,
        DATEDIFF(m.Fecha_Finalizacion, NOW()) AS Dias_Para_Cerrar
    FROM CAUSA_SOCIAL c
    JOIN META_PLAZO m ON c.Id_Causa = m.CAUSA_SOCIAL_Id_Causa
    LEFT JOIN DONACION d ON c.Id_Causa = d.CAUSA_SOCIAL_Id_Causa
    GROUP BY c.Id_Causa, c.Titulo, m.Monto_Minimo, m.Fecha_Finalizacion
    HAVING Dias_Para_Cerrar <= 7 AND Recaudado_Actual < m.Monto_Minimo;
";
$stmt5 = $db->prepare($query_alertas);
$stmt5->execute();
$alertas_criticas = $stmt5->fetchAll(PDO::FETCH_ASSOC);
?>