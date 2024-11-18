<?php
include 'datos.php';  
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Gráfico Estudiante</title>
</head>
<header>
    <nav>
        <ul>
            <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <img src="sello_unaponline_blanco_1.webp" alt="Logo UNAP Online" width="25%" height="auto" style="margin-right: -260px;"> 
                <input type="button" value="Volver" title="Volver atrás" style="height: 30px; margin-left: -85px"> 
            </li>
        </ul>
    </nav>
</header>
<body>
    <br><br>
    <h3 style="text-align: center;">Porcentaje de Avance Semanal en Aula Virtual Online %
        <i class="fa fa-exclamation-circle" aria-hidden="true" style="color:#036cad" 
        title="Este reporte muestra el avance en la lectura de los contenidos del curso durante cada semana." style="font-size: 5px;"></i></h3>
    <div class="container">
        <h4 title="Nombre del curso." id="titulo"><?php echo $datos['titulo'] ?? ''; ?></h4>

        <canvas id="densityChart"></canvas>
    
        <button id="loadData" title="Permite cargar los datos">Cargar Datos</button>

    </div>

    <script>
       
        const datosPHP = <?php echo json_encode($datos['porcentajeIngreso']); ?>;

        async function obtenerDatos() {
            return new Promise(resolve => {
                setTimeout(() => resolve(datosPHP), 500); 
            });
        }
        
        function ajustarEspacioEntreValoresYBarras(datos, anchoMaximo) {
            const longitudMaxima = Math.max(...datos.map(item => item.nombre.length));
            const espacioMinimo = 30; 
            const espacioPorCaracter = 10; 
            return Math.min(espacioMinimo + (longitudMaxima * espacioPorCaracter), anchoMaximo * 0.3);
        }

        function dibujarGrafico(datos) {
            const lienzo = document.getElementById('densityChart');
            const contexto = lienzo.getContext('2d');
        
            const alturaBarras = 60;
            const espacioEntreBarras = 30;
            const alturaTotal = (alturaBarras + espacioEntreBarras) * datos.length + 100; 

            lienzo.width = 1000;
            lienzo.height = alturaTotal;

            const anchoMaximo = 650;
            const escala = anchoMaximo / 100;
            const espacioEntreLetraYBarra = ajustarEspacioEntreValoresYBarras(datos, anchoMaximo);
        
            contexto.clearRect(0, 0, lienzo.width, lienzo.height);
            
            let progresoX = 0;
            const animarEjeX = () => {
                if (progresoX <= anchoMaximo) {
                    contexto.beginPath();
                    contexto.moveTo(60 + espacioEntreLetraYBarra, lienzo.height - 60);
                    contexto.lineTo(60 + espacioEntreLetraYBarra + progresoX, lienzo.height - 60);
                    contexto.stroke();
                    progresoX += 14;
                    requestAnimationFrame(animarEjeX);
                }
            };
            
            let progresoY = 0;
            const animarEjeY = () => {
                if (progresoY <= lienzo.height - 60) {
                    contexto.beginPath();
                    contexto.moveTo(60 + espacioEntreLetraYBarra, lienzo.height - 60);
                    contexto.lineTo(60 + espacioEntreLetraYBarra, lienzo.height - 60 - progresoY);
                    contexto.stroke();
                    progresoY += 14;
                    requestAnimationFrame(animarEjeY);
                }
            };

            datos.forEach((item, indice) => {
                const anchoBarras = item.porcentaje * escala;
                const y = indice * (alturaBarras + espacioEntreBarras) + espacioEntreBarras;
                const x = 60 + espacioEntreLetraYBarra;

                const gradiente = contexto.createLinearGradient(x, y, x + anchoBarras, y);
                gradiente.addColorStop(0, '#67a5cf');
                gradiente.addColorStop(0.6, '#67a5cf');
                gradiente.addColorStop(1, '#036cad');

                let anchoBarra = 0;
                const animarBarra = () => {
                    if (anchoBarra < anchoBarras) {
                        anchoBarra += (anchoBarras - anchoBarra) * 0.1;
                        if (anchoBarras - anchoBarra < 0.1) anchoBarra = anchoBarras;
                        
                        contexto.fillStyle = gradiente;
                        contexto.fillRect(x, y, anchoBarra, alturaBarras);

                        contexto.fillStyle = '#FFFFFF'; 
                        contexto.font = 'bold 17px Segoe UI, sans-serif'; 
                        contexto.textAlign = 'right';
                        contexto.textBaseline = 'middle';
                        contexto.fillText(item.porcentaje.toFixed(2) + ' %', x + anchoBarra + -5, y + alturaBarras / 2);

                        requestAnimationFrame(animarBarra);
                    }
                };
                animarBarra();

                contexto.fillStyle = '#000000'; 
                contexto.font = ' 15.5px Segoe UI, sans-serif'; 
                contexto.textAlign = 'right';
                contexto.textBaseline = 'middle';
                contexto.fillText(item.nombre, 200, y + alturaBarras / 2); 
            });

            animarEjeX();
            animarEjeY();
        }

        document.getElementById('loadData').addEventListener('click', () => {
           
            const boton = document.getElementById('loadData');
            boton.style.transform = 'scale(0.95)';
            setTimeout(() => {
                boton.style.transform = 'scale(1)';
            }, 100);

            obtenerDatos()
                .then(datos => {

                    const lienzo = document.getElementById('densityChart');
                    const contexto = lienzo.getContext('2d');
                    contexto.clearRect(0, 0, lienzo.width, lienzo.height);

                    dibujarGrafico(datos);
                })
                .catch(error => {
                    console.error('Error al cargar los datos:', error);
                });
        });
    </script>
</body>
</html>
