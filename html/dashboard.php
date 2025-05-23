
<?php
$servername = "localhost";
$username = "root";
$password = "120994knj";
$dbname = "safegardendb_local";

$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die('Error de conexión (' . $mysqli->connect_errno . '): ' . $mysqli->connect_error);
}

// Consulta para obtener sensores con su última medición
$sql = "
SELECT s.id_sensor, s.nombre, s.tipo, m.valor, m.fecha_hora
FROM sensor s
LEFT JOIN (
    SELECT id_sensor, valor, fecha_hora
    FROM medicion
    WHERE (id_sensor, fecha_hora) IN (
        SELECT id_sensor, MAX(fecha_hora)
        FROM medicion
        GROUP BY id_sensor
    )
) m ON s.id_sensor = m.id_sensor
ORDER BY s.tipo, s.nombre
";

$result = $mysqli->query($sql);

if (!$result) {
    die("Error en la consulta: " . $mysqli->error);
}

// Preparar array para clasificar sensores por tipo
$sensores = [
    'Temperatura' => [],
    'Humedad' => [],
    'HumedadSuelo' => [],
    'Movimiento' => []
];

while ($row = $result->fetch_assoc()) {
    $tipo = $row['tipo'];
    if (isset($sensores[$tipo])) {
        $sensores[$tipo][] = $row;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
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

    <title> Dashboard -  | safegarden </title>

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

    .icon-temperature {
      background-color: #ff6f61;
    }

    .icon-humidity {
      background-color: #00bcd4;
    }

    .icon-animal {
      background-color: #4caf50;
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
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block">John Doe</span>
                            <small class="text-muted">Admin</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="perfil.php">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">Mi perfil</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="#">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                          <span class="flex-grow-1 align-middle">Billing</span>
                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                        </span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="auth-login-basic.html">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
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
                          <h5 class="card-title text-primary"> ¡Bienvenido a SafeGarden!🌱</h5>
                          <p class="mb-4">
                            Revisa los  datos de tus cultivos en timpo real.
                          </p>
                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                          <img
                            src="../assets/img/illustrations/image-removebg-preview.png"
                            height="150"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                 <div class="container mt-4">

  
                 <div class="row g-4">

  
                 <!-- Tarjetas de Temperatura -->
  
                 <?php if (!empty($sensores['Temperatura'])): ?>
      <?php foreach ($sensores['Temperatura'] as $sensor): ?>
        <div class="col-md-4">
          <div class="card card-custom p-3">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-container icon-temperature me-3">
                <i class="bx bx-temperature"></i>
              </div>
              <h5 class="mb-0"><?= htmlspecialchars($sensor['nombre']) ?></h5>
            </div>
            <h2 class="fw-bold">
              <?= htmlspecialchars($sensor['valor'] !== null ? $sensor['valor'] . ' °C' : 'Sin datos') ?>
            </h2>
            <small class="text-muted"><?= $sensor['fecha_hora'] ?? '' ?></small>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No hay sensores de temperatura disponibles.</p>
    <?php endif; ?>

    <!-- Tarjetas de Humedad -->
    <?php if (!empty($sensores['Humedad'])): ?>
      <?php foreach ($sensores['Humedad'] as $sensor): ?>
        <div class="col-md-4">
          <div class="card card-custom p-3">
            <div class="d-flex align-items-center mb-3">
              <div class="icon-container icon-humidity me-3">
                <i class="bx bx-droplet"></i>
              </div>
              <h5 class="mb-0"><?= htmlspecialchars($sensor['nombre']) ?></h5>
            </div>
            <h2 class="fw-bold">
              <?= htmlspecialchars($sensor['valor'] !== null ? $sensor['valor'] . ' %' : 'Sin datos') ?>
            </h2>
            <small class="text-muted"><?= $sensor['fecha_hora'] ?? '' ?></small>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No hay sensores de humedad disponibles.</p>
    <?php endif; ?>


    <!-- Tarjeta: Detección de Animales -->
      <div class="col-md-4">
        <div class="card card-custom text-center">
          <div class="card-body">
            <div class="icon-container icon-animal mb-3">
              <i class='bx bxs-paw'></i>
            </div>
            <h5 class="card-title">Detección de Animales</h5>
            <p class="card-text fs-4">3 eventos</p>
          </div>
        </div>
      </div>

      <!-- Gráfico de barras -->
    <div class="card card-custom mt-5">
      <div class="card-body">
        <h5 class="card-title">Historial de Datos</h5>
        <canvas id="graficoDatos" height="100"></canvas>
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



    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

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
