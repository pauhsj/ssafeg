<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$servername = "localhost";
$username = "u557447082_9x8vh";
$password ="safegarden_bm9F8>y";
$dbname = "u557447082_safegardendb";


header('Content-Type: text/html; charset=utf-8');

// Conexión DB

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

// Verificar sesión
$id_cliente = $_SESSION['id_cliente'] ?? 0;
if ($id_cliente == 0) {
    header("Location: ../login.php");
    exit;
}

// Manejo AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Para respuestas JSON
    header('Content-Type: application/json');

    // Generar código único numérico para dispositivo
    if ($_POST['action'] === 'generar_codigo') {
        // Código de 8 dígitos numéricos
        do {
            $codigo = strval(rand(10000000, 99999999));
            if ($_POST['microcontrolador'] === 'LoRa') {
                $res = $conn->query("SELECT 1 FROM dispositivos_Lora WHERE codigo_lora='$codigo'");
            } else {
                $res = $conn->query("SELECT 1 FROM dispositivos_ESP32 WHERE codigo_esp32='$codigo'");
            }
        } while ($res->num_rows > 0);

        echo json_encode(['success' => true, 'codigo' => $codigo]);
        exit;
    }

    // Registrar dispositivo
    if ($_POST['action'] === 'registrar_dispositivo') {
        $micro = $_POST['microcontrolador'];
        $codigo = $conn->real_escape_string($_POST['codigo']);
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $ubicacion = $conn->real_escape_string($_POST['ubicacion']);

        if ($micro === 'LoRa') {
            $stmt = $conn->prepare("INSERT INTO dispositivos_Lora (codigo_lora, nombre_dispositivo, ubicacion, id_cliente, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssi", $codigo, $nombre, $ubicacion, $id_cliente);
        } elseif ($micro === 'ESP32') {
            $stmt = $conn->prepare("INSERT INTO dispositivos_ESP32 (codigo_esp32, nombre_dispositivo, ubicacion, id_cliente, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssi", $codigo, $nombre, $ubicacion, $id_cliente);
        } else {
            echo json_encode(['success' => false, 'message' => 'Microcontrolador inválido']);
            exit;
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Dispositivo registrado']);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    // Registrar sensor
    if ($_POST['action'] === 'registrar_sensor') {
        $micro = $_POST['microcontrolador'];
        $id_dispositivo = intval($_POST['id_dispositivo']);
        $tipo_sensor = $conn->real_escape_string($_POST['tipo_sensor']);
        $descripcion = $conn->real_escape_string($_POST['descripcion']);

        // Validar que el dispositivo exista
        if ($micro === 'LoRa') {
            $res = $conn->query("SELECT id_lora FROM dispositivos_Lora WHERE id_lora = $id_dispositivo AND id_cliente = $id_cliente");
        } else {
            $res = $conn->query("SELECT id_esp32 FROM dispositivos_ESP32 WHERE id_esp32 = $id_dispositivo AND id_cliente = $id_cliente");
        }

        if (!$res || $res->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Dispositivo no encontrado o no autorizado']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO sensores (id_dispositivo, tipo_microcontrolador, tipo_sensor, descripcion, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isss", $id_dispositivo, $micro, $tipo_sensor, $descripcion);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Sensor registrado']);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        exit;
    }
}

// Obtener dispositivos LoRa y ESP32 para el cliente (para mostrar en el select)
$result_lora = $conn->query("SELECT id_lora, nombre_dispositivo, codigo_lora FROM dispositivos_lora WHERE id_cliente = $id_cliente");
$result_esp32 = $conn->query("SELECT id_esp32, nombre_dispositivo, codigo_esp32 FROM dispositivos_ESP32 WHERE id_cliente = $id_cliente");
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

    <title> Registrar sensor | safegarden </title>

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
            <a href="dashboard.php" class="app-brand-link d-flex align-items-center">
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

          <!-- / Navbar -->
           <div class="content-wrapper">
            <!-- Content -->
<div class="container">

    <h2>Registrar Dispositivo</h2>
    <form id="formDispositivo" class="card p-4 mb-4">
        <div class="mb-3">
            <label for="microcontrolador" class="form-label">Microcontrolador</label>
            <select name="microcontrolador" id="microcontrolador" class="form-select" required>
                <option value="" disabled selected>Selecciona un microcontrolador</option>
                <option value="LoRa">LoRa</option>
                <option value="ESP32">ESP32</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="codigo" class="form-label">Código único </label>
            <input type="text" name="codigo" id="codigo" class="form-control" readonly required placeholder="auto generado"/>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre o ubicación del dispositivo</label>
            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ejemplo: Huerto Norte" required />
        </div>
        <div class="mb-3">
            <label for="ubicacion" class="form-label">Descripción de ubicación</label>
            <input type="text" name="ubicacion" id="ubicacion" class="form-control" placeholder="Ejemplo: Cerca de la entrada" />
        </div>
        <button type="submit" class="btn btn-success">Registrar dispositivo</button>
    </form>
    <hr />

    <h2>Registrar Sensor</h2>
    <form id="formSensor" class="card p-4">
        <div class="mb-3">
            <label for="microcontrolador_sensor" class="form-label">Microcontrolador</label>
            <select name="microcontrolador_sensor" id="microcontrolador_sensor" class="form-select" required>
                <option value="" disabled selected>Selecciona un microcontrolador</option>
                <option value="LoRa">LoRa</option>
                <option value="ESP32">ESP32</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="dispositivo" class="form-label">Dispositivo</label>
            <select name="id_dispositivo" id="dispositivo" class="form-select" required>
                <option value="" disabled selected>Selecciona un dispositivo</option>
                <!-- Opciones cargadas por JS -->
            </select>
        </div>
        <div class="mb-3">
            <label for="tipo_sensor" class="form-label">Tipo de sensor</label>
            <input type="text" name="tipo_sensor" id="tipo_sensor" class="form-control" placeholder="Ejemplo: DHT11, Ultrasónico" required />
        </div>
        <div class="mb-3">
            <label for="descripcion_sensor" class="form-label">Descripción</label>
            <input type="text" name="descripcion" id="descripcion_sensor" class="form-control" placeholder="Ejemplo: Esquina noreste del huerto" />
        </div>
        <button type="submit" class="btn btn-primary">Registrar sensor</button>
    </form>
</div>

<script>
    const dispositivosLoRa = <?php 
        $arr = [];
        while($row = $result_lora->fetch_assoc()) {
            $arr[] = $row;
        }
        echo json_encode($arr);
    ?>;
    const dispositivosESP32 = <?php
        $arr2 = [];
        while($row2 = $result_esp32->fetch_assoc()) {
            $arr2[] = $row2;
        }
        echo json_encode($arr2);
    ?>;

    // Generar código cuando seleccionen microcontrolador en dispositivo
    document.getElementById('microcontrolador').addEventListener('change', function(){
        const micro = this.value;
        if(!micro) return;
        fetch('', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({action:'generar_codigo', microcontrolador: micro})
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('codigo').value = data.codigo;
            } else {
                alert('Error generando código: '+data.message);
            }
        })
        .catch(() => alert('Error en la comunicación'));
    });

    // Manejo registro dispositivo
    document.getElementById('formDispositivo').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        formData.append('action','registrar_dispositivo');

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.success){
                form.reset();
                document.getElementById('codigo').value = '';
                // Actualizar listas dispositivos
                actualizarListas();
            }
        })
        .catch(() => alert('Error en la comunicación'));
    });

    // Actualizar lista de dispositivos según microcontrolador seleccionado para sensor
    function actualizarListas(){
        // Se reconstruyen las listas del select de dispositivos
        const microSelect = document.getElementById('microcontrolador_sensor');
        const dispSelect = document.getElementById('dispositivo');
        const micro = microSelect.value;

        // Vaciar opciones
        dispSelect.innerHTML = '<option value="" disabled selected>Selecciona un dispositivo</option>';

        let lista = [];
        if(micro === 'LoRa'){
            lista = dispositivosLoRa;
        } else if(micro === 'ESP32'){
            lista = dispositivosESP32;
        }

        lista.forEach(d => {
            let code = d.codigo_lora || d.codigo_esp32 || '';
            let nombre = d.nombre_dispositivo || 'Sin nombre';
            let option = document.createElement('option');
            option.value = d.id_lora || d.id_esp32;
            option.textContent = nombre + " (" + code + ")";
            dispSelect.appendChild(option);
        });
    }

    // Cuando cambia el microcontrolador para sensores
    document.getElementById('microcontrolador_sensor').addEventListener('change', actualizarListas);

    // Registrar sensor
    document.getElementById('formSensor').addEventListener('submit', function(e){
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        formData.append('action','registrar_sensor');

        // Cambiar nombre campo microcontrolador para que coincida con backend
        formData.set('microcontrolador', formData.get('microcontrolador_sensor'));

        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if(data.success){
                form.reset();
                document.getElementById('dispositivo').innerHTML = '<option value="" disabled selected>Selecciona un dispositivo</option>';
            }
        })
        .catch(() => alert('Error en la comunicación'));
    });

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