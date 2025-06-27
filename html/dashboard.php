<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$servername = "localhost";
$username = "u557447082_9x8vh";
$password ="safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Validar sesión
if (!isset($_SESSION['id_cliente'])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Obtener sensores junto con dispositivo y últimos datos asociados
$sqlsensores = "
    SELECT s.*, 
           d.nombre_dispositivo AS micro_nombre,
           d.ubicacion AS ubicacion_dispositivo,
           (SELECT temperatura FROM registros r WHERE r.id_sensor = s.id_sensor ORDER BY r.fecha DESC LIMIT 1) AS ultima_temp,
           (SELECT humedad FROM registros r WHERE r.id_sensor = s.id_sensor ORDER BY r.fecha DESC LIMIT 1) AS ultima_hum,
           (SELECT COUNT(*) FROM sensor_movimiento m WHERE m.id_sensor = s.id_sensor AND DATE(m.fecha) = CURDATE()) AS eventos_hoy
    FROM sensores s
    INNER JOIN dispositivos_lora d ON s.id_dispositivo = d.id_lora
    WHERE d.id_cliente = ?
";

$stmt = $conn->prepare($sqlsensores);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resultSensores = $stmt->get_result();

$sensores = [];
if ($resultSensores && $resultSensores->num_rows > 0) {
    while ($row = $resultSensores->fetch_assoc()) {
        $sensores[] = $row;
    }
}

// Consulta rápida de estadísticas generales
$estadisticas = [
  'total_sensores' => 0,
  'total_lora' => 0,
  'total_esp32' => 0,
  'eventos_hoy' => 0,
  'temp_promedio' => 0,
  'hum_promedio' => 0
];

// Total sensores
$res = $conn->query("SELECT COUNT(*) AS total FROM sensores WHERE id_dispositivo IN 
  (SELECT id_lora FROM dispositivos_LoRa WHERE id_cliente = $id_cliente) 
  OR id_dispositivo IN 
  (SELECT id_esp32 FROM dispositivos_ESP32 WHERE id_cliente = $id_cliente)");
$estadisticas['total_sensores'] = $res->fetch_assoc()['total'] ?? 0;

// Dispositivos LoRa
$res = $conn->query("SELECT COUNT(*) AS total FROM dispositivos_LoRa WHERE id_cliente = $id_cliente");
$estadisticas['total_lora'] = $res->fetch_assoc()['total'] ?? 0;

// Dispositivos ESP32
$res = $conn->query("SELECT COUNT(*) AS total FROM dispositivos_ESP32 WHERE id_cliente = $id_cliente");
$estadisticas['total_esp32'] = $res->fetch_assoc()['total'] ?? 0;

// Eventos de movimiento hoy
$res = $conn->query("SELECT COUNT(*) AS total FROM sensor_movimiento WHERE DATE(fecha) = CURDATE()");
$estadisticas['eventos_hoy'] = $res->fetch_assoc()['total'] ?? 0;

// Promedio temperatura y humedad
$res = $conn->query("
  SELECT AVG(temperatura) AS temp, AVG(humedad) AS hum 
  FROM registros 
  WHERE DATE(fecha) = CURDATE() AND id_sensor IN (
    SELECT id_sensor FROM sensores 
    WHERE tipo_sensor = 'DHT11'
  )
");
$row = $res->fetch_assoc();
$estadisticas['temp_promedio'] = round($row['temp'], 1) ?? 0;
$estadisticas['hum_promedio'] = round($row['hum'], 1) ?? 0;

// Consulta últimos registros DHT11 para el usuario
$datos_grafica = [];

$sql_graf = "
    SELECT r.fecha, r.temperatura, r.humedad 
    FROM registros r 
    INNER JOIN sensores s ON r.id_sensor = s.id_sensor 
    INNER JOIN dispositivos_LoRa d ON s.id_dispositivo = d.id_lora 
    WHERE s.tipo_sensor = 'DHT11' AND d.id_cliente = $id_cliente 
    ORDER BY r.fecha DESC 
    LIMIT 12
";

$result = $conn->query($sql_graf);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $datos_grafica[] = [
            'fecha' => date('H:i', strtotime($row['fecha'])),
            'temp' => floatval($row['temperatura']),
            'hum' => floatval($row['humedad'])
        ];
    }
    // Invertir para mostrar cronológicamente
    $datos_grafica = array_reverse($datos_grafica);
}

?>


<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title> Dashboard | safegarden </title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/logoSG.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />
<link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>

    
    <style>
    body {
      background-color: #f8f9fa;
    }

    .card-custom {
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease-in-out;
    }

    .card-custom:hover {
      transform: scale(1.02);
    }

    .icon-container {
      font-size: 2.5rem;
      padding: 15px;
      border-radius: 50%;
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .sensor-card {
  border-radius: 15px;
  transition: all 0.3s ease;
  background: #ffffff;
}

.sensor-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.sensor-card .card-title {
  font-size: 1.25rem;
  font-weight: 600;
}

.sensor-card .badge {
  font-size: 0.9rem;
  padding: 0.5em 0.75em;
}

  </style>
</head>
  </head>

  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="index.html" class="app-brand-link d-flex align-items-center">
              <span class="app-brand-logo demo">
                  <img src="../assets/img/favicon/logoSG.png" alt="Logo" style="height: 50px; vertical-align: middle;" />
                  <defs>
                    <path
                      d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                      id="path-1"
                    ></path>
                    <path
                      d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                      id="path-3"
                    ></path>
                    <path
                      d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                      id="path-4"
                    ></path>
                    <path
                      d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                      id="path-5"
                    ></path>
                  </defs>
                  <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                      <g id="Icon" transform="translate(27.000000, 15.000000)">
                        <g id="Mask" transform="translate(0.000000, 8.000000)">
                          <mask id="mask-2" fill="white">
                            <use xlink:href="#path-1"></use>
                          </mask>
                          <use fill="#696cff" xlink:href="#path-1"></use>
                          <g id="Path-3" mask="url(#mask-2)">
                            <use fill="#696cff" xlink:href="#path-3"></use>
                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                          </g>
                          <g id="Path-4" mask="url(#mask-2)">
                            <use fill="#696cff" xlink:href="#path-4"></use>
                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                          </g>
                        </g>
                        <g
                          id="Triangle"
                          transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) "
                        >
                          <use fill="#696cff" xlink:href="#path-5"></use>
                          <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                        </g>
                      </g>
                    </g>
                  </g>
              </span>
              <span class="app-brand-text demo menu-text fw-bolder ms-1"> SafeGarden </span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item active">
              <a href="dashboard.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>

           <!-- Registros -->
  
           <li class="menu-item">
           <a href="registros.php" class="menu-link">
           <i class="menu-icon tf-icons bx bx-file"></i>
           <div data-i18n="Registros">Registros</div>
          </a> 
          </li>

            <!-- Perfil -->
  
            <li class="menu-item">
            <a href="perfil.php" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Perfil">Perfil</div>
          </a>
        </li>

        <!-- registro sensosr -->
        <li class="menu-item">
        <a href="addsensor.php" class="menu-link">
        <i class="menu-icon tf-icons bx bx-arrow-out-left-square-half"></i> 
        <div data-i18n="registrar sensor">Registrar sensor</div>
      </a>
    </li>

 
        <!-- Logout -->
        <li class="menu-item">
        <a href="logout.php" class="menu-link">
        <i class="menu-icon tf-icons bx bx-log-out"></i> 
        <div data-i18n="Logout">Cerrar sesión</div>
      </a>
    </li>
          


        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              
              <!-- /Search -->

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="nav-item lh-1 me-3">
                </li>

                <!-- User -->
              
                <!--/ User -->
              </ul>
            </div>
          </nav>

          <!-- / Navbar -->
           <div class="content-wrapper">
            <!-- Content -->

            

            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">

              <div class="card">
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-7">
                        <div class="card-body">
                          <h5 class="card-title text-primary"> ¡Bienvenido a SafeGarden!</h5>
                          <p class="mb-4">
                            Revisa los  datos de tus cultivos en timpo real.
                          </p>
                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="container mt-4">

                  <div class="row mt-4">
  <!-- Total sensores -->
  <div class="col-md-4">
    <div class="card text-center card-custom">
      <div class="card-body">
        <h5 class="card-title">Sensores Registrados</h5>
        <p class="display-6 text-primary"><?= $estadisticas['total_sensores'] ?></p>
      </div>
    </div>
  </div>

  <!-- Dispositivos LoRa -->
  <div class="col-md-4">
    <div class="card text-center card-custom">
      <div class="card-body">
        <h5 class="card-title">Dispositivos LoRa</h5>
        <p class="display-6 text-success"><?= $estadisticas['total_lora'] ?></p>
      </div>
    </div>
  </div>

  <!-- Dispositivos ESP32 -->
  <div class="col-md-4">
    <div class="card text-center card-custom">
      <div class="card-body">
        <h5 class="card-title">Dispositivos ESP32</h5>
        <p class="display-6 text-info"><?= $estadisticas['total_esp32'] ?></p>
      </div>
    </div>
  </div>

  <!-- Eventos movimiento hoy -->
  <div class="col-md-4 mt-3">
    <div class="card text-center card-custom">
      <div class="card-body">
        <h5 class="card-title">Eventos de Movimiento Hoy</h5>
        <p class="display-6 text-danger"><?= $estadisticas['eventos_hoy'] ?></p>
      </div>
    </div>
  </div>

  <!-- Promedio Temperatura -->
  <div class="col-md-4 mt-3">
    <div class="card text-center card-custom">
      <div class="card-body">
        <h5 class="card-title">Temperatura Promedio</h5>
        <p class="display-6 text-warning"><?= $estadisticas['temp_promedio'] ?> °C</p>
      </div>
    </div>
  </div>

  <!-- Promedio Humedad -->
  <div class="col-md-4 mt-3">
    <div class="card text-center card-custom">
      <div class="card-body">
        <h5 class="card-title">Humedad Promedio</h5>
        <p class="display-6 text-primary"><?= $estadisticas['hum_promedio'] ?> %</p>
      </div>
    </div>
  </div>
</div>

                  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
  <?php foreach ($sensores as $row): ?>
    <div class="col">
      <div class="card sensor-card h-100 shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title text-primary mb-2">
            <?= htmlspecialchars($row['tipo_sensor'] ?? 'Sensor') ?>
          </h5>

          <p class="mb-1"><i class="bx bx-chip me-2 text-info"></i><strong>Microcontrolador:</strong> <?= htmlspecialchars($row['micro_nombre'] ?? '') ?> (<?= htmlspecialchars($row['tipo_microcontrolador'] ?? '') ?>)</p>
          <p class="mb-1"><i class="bx bx-map me-2 text-success"></i><strong>Ubicación dispositivo:</strong> <?= htmlspecialchars($row['ubicacion'] ?? '') ?></p>
          <p class="mb-1"><i class="bx bx-current-location me-2 text-warning"></i><strong>Ubicación sensor:</strong> <?= htmlspecialchars($row['descripcion'] ?? '') ?></p>

          <?php if (($row['tipo_sensor'] ?? '') === 'DHT11'): ?>
            <div class="mt-3">
              <span class="badge bg-danger"><i class="bx bx-thermometer me-1"></i> <?= htmlspecialchars($row['ultima_temp'] ?? 'N/A') ?> °C</span>
              <span class="badge bg-primary ms-2"><i class="bx bx-droplet me-1"></i> <?= htmlspecialchars($row['ultima_hum'] ?? 'N/A') ?> %</span>
            </div>
          <?php elseif (($row['tipo_sensor'] ?? '') === 'Movimiento'): ?>
            <div class="mt-3">
              <span class="badge bg-success"><i class="bx bx-run me-1"></i> Eventos hoy: <?= htmlspecialchars($row['eventos_hoy'] ?? 0) ?></span>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<div class="card mt-4">
  <div class="card-body">
    <h5 class="card-title">Temperatura y Humedad - Últimas Lecturas</h5>
    <canvas id="graficaTH" height="100"></canvas>
  </div>
</div>


    
    </div>
</div>              
    </div>
  </div>
    </div>
</div>
                 
</div>

    <script>
function cargarDatosSensor() {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'obtenerDatos.php', true);

  xhr.onload = function() {
    if (xhr.status === 200) {
      try {
        var data = JSON.parse(xhr.responseText);
        if (!data.error) {
          document.getElementById('tempValor').textContent = data.temperatura + ' °C';
          document.getElementById('humValor').textContent = data.humedad + ' %';
        } else {
          document.getElementById('tempValor').textContent = 'No disponible';
          document.getElementById('humValor').textContent = 'No disponible';
        }
      } catch (e) {
        console.error('Error al parsear JSON:', e);
      }
    } else {
      console.error('Error en la petición AJAX: ' + xhr.status);
    }
  };

  xhr.onerror = function() {
    console.error('Error en la petición AJAX');
  };

  xhr.send();
}

// Ejecutar al cargar la página
cargarDatosSensor();

// Actualizar cada 10 segundos sin refrescar la página
setInterval(cargarDatosSensor, 10000);
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sensores = document.querySelectorAll('[id^="temp-"], [id^="hum-"], [id^="mov-"]');
  
  sensores.forEach(sensor => {
    const id = sensor.id.split('-')[1];
    const tipo = sensor.id.split('-')[0];

    fetch(`obtener_valor_sensor.php?id_sensor=${id}&tipo=${tipo}`)
      .then(res => res.json())
      .then(data => {
        if (tipo === 'temp') {
          document.getElementById(`temp-${id}`).textContent = data.temperatura ?? 'N/D';
        } else if (tipo === 'hum') {
          document.getElementById(`hum-${id}`).textContent = data.humedad ?? 'N/D';
        } else if (tipo === 'mov') {
          document.getElementById(`mov-${id}`).textContent = data.total_eventos ?? '0';
        }
      });
  });
});
</script>


<script>
const ctx = document.getElementById('graficoDatos').getContext('2d');
let grafico = null;

  fetch('obtenerDatos.php')
    .then(response => response.json())
    .then(data => {
      const fechas = data.fechas;
      const temperaturas = data.temperaturas;
      const humedades = data.humedades;

      // Aquí puedes alimentar tus gráficos ApexCharts, por ejemplo
      console.log(fechas, temperaturas, humedades);
    });


// Cargar inicialmente
cargarDatos();

// Actualizar cada 10 segundos
setInterval(cargarDatos, 10000);

</script>


    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>