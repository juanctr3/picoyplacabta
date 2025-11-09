<?php
// PICO Y PLACA - Bogotá (par/impar), Medellín, Cali + FESTIVOS COLOMBIA

$configuraciones = [
    'bogota' => [
        'nombre' => 'Bogotá',
        'horario' => '6:00 a.m. - 9:00 p.m.',
        'horarioInicio' => 6,
        'horarioFin' => 21
    ],
    'medellin' => [
        'nombre' => 'Medellín',
        'horario' => '5:00 a.m. - 8:00 p.m.',
        'horarioInicio' => 5,
        'horarioFin' => 20,
        'restricciones' => [
            'Monday' => [1, 8],
            'Tuesday' => [3, 4],
            'Wednesday' => [2, 9],
            'Thursday' => [5, 7],
            'Friday' => [0, 6],
            'Saturday' => [],
            'Sunday' => []
        ]
    ],
    'cali' => [
        'nombre' => 'Cali',
        'horario' => '6:00 a.m. - 7:00 p.m.',
        'horarioInicio' => 6,
        'horarioFin' => 19,
        'restricciones' => [
            'Monday' => [5, 6],
            'Tuesday' => [7, 8],
            'Wednesday' => [9, 0],
            'Thursday' => [1, 2],
            'Friday' => [3, 4],
            'Saturday' => [],
            'Sunday' => []
        ]
    ]
];

function isHolidayColombia($date) {
    $year = (int)$date->format('Y');
    $month = (int)$date->format('m');
    $day = (int)$date->format('d');
    
    // Festivos fijos
    $fixedHolidays = [
        ['month' => 1, 'day' => 1],   // Año Nuevo
        ['month' => 1, 'day' => 6],   // Reyes Magos
        ['month' => 3, 'day' => 24],  // San José
        ['month' => 5, 'day' => 1],   // Día del Trabajo
        ['month' => 7, 'day' => 20],  // Grito de Independencia
        ['month' => 8, 'day' => 7],   // Batalla de Boyacá
        ['month' => 8, 'day' => 18],  // Asunción de la Virgen
        ['month' => 10, 'day' => 13], // Día de la Raza
        ['month' => 11, 'day' => 3],  // Todos los Santos
        ['month' => 11, 'day' => 17], // Independencia de Cartagena
        ['month' => 12, 'day' => 8],  // Inmaculada Concepción
        ['month' => 12, 'day' => 25]  // Navidad
    ];
    
    // Verificar festivos fijos
    foreach ($fixedHolidays as $holiday) {
        if ($month === $holiday['month'] && $day === $holiday['day']) {
            return true;
        }
    }
    
    // Festivos móviles (Semana Santa y días relacionados)
    // Para 2025: Pascua es 20 de abril
    if ($year === 2025) {
        $easterDate = new DateTime('2025-04-20');
        $juevesPrePascua = clone $easterDate; $juevesPrePascua->modify('-3 days');
        $viernesSanto = clone $easterDate; $viernesSanto->modify('-2 days');
        $domingoRamosSemana = clone $easterDate; $domingoRamosSemana->modify('last Sunday');
        $ascension = clone $easterDate; $ascension->modify('+39 days');
        $corpusChristi = clone $easterDate; $corpusChristi->modify('+60 days');
        $sagradoCorazon = clone $easterDate; $sagradoCorazon->modify('+68 days');
        $sanPedroSanPablo = new DateTime('2025-06-30');
        
        $movableHolidays = [
            $juevesPrePascua->format('Y-m-d'),
            $viernesSanto->format('Y-m-d'),
            $domingoRamosSemana->format('Y-m-d'),
            $ascension->format('Y-m-d'),
            $corpusChristi->format('Y-m-d'),
            $sagradoCorazon->format('Y-m-d'),
            $sanPedroSanPablo->format('Y-m-d')
        ];
        
        if (in_array($date->format('Y-m-d'), $movableHolidays)) {
            return true;
        }
    }
    
    // Para 2026 y otros años
    if ($year >= 2026) {
        // Implementar cálculo de Pascua para otros años si es necesario
    }
    
    return false;
}

function getRestrictionsBogota($date) {
    $dayOfMonth = (int)$date->format('d');
    $isOdd = $dayOfMonth % 2 === 1;
    
    if ($isOdd) {
        return [6, 7, 8, 9, 0];
    } else {
        return [1, 2, 3, 4, 5];
    }
}

function getRestrictionsOtherCities($city, $date, $configuraciones) {
    $config = $configuraciones[$city];
    $dayName = $date->format('l');
    $restrictions = isset($config['restricciones'][$dayName]) ? $config['restricciones'][$dayName] : [];
    return $restrictions;
}

$isDatePage = false;
$dateData = [];

if (preg_match('/pico-y-placa\/(\d{4})-(\d{2})-(\d{2})-(\w+)/', $_SERVER['REQUEST_URI'], $matches)) {
    $year = (int)$matches[1];
    $month = (int)$matches[2];
    $day = (int)$matches[3];
    $city = $matches[4];
    
    if (in_array($city, ['bogota', 'medellin', 'cali'])) {
        try {
            $date = new DateTime("$year-$month-$day");
            $dayNames = ['Sunday' => 'domingo', 'Monday' => 'lunes', 'Tuesday' => 'martes', 'Wednesday' => 'miércoles', 'Thursday' => 'jueves', 'Friday' => 'viernes', 'Saturday' => 'sábado'];
            $monthNames = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            
            $dayNameEn = $date->format('l');
            $dayNameEs = $dayNames[$dayNameEn];
            $dayNum = (int)$date->format('d');
            $monthName = $monthNames[$month - 1];
            $cityName = $configuraciones[$city]['nombre'];
            
            $isWeekend = in_array($dayNameEn, ['Saturday', 'Sunday']);
            $isHoliday = isHolidayColombia($date);
            
            $restrictions = [];
            if (!$isWeekend && !$isHoliday) {
                $restrictions = $city === 'bogota' ? getRestrictionsBogota($date) : getRestrictionsOtherCities($city, $date, $configuraciones);
            }
            
            $allowed = array_diff(range(0, 9), $restrictions);
            sort($allowed);
            sort($restrictions);
            
            $dateData = [
                'dayNameEs' => $dayNameEs,
                'dayNum' => $dayNum,
                'monthName' => $monthName,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'cityName' => $cityName,
                'city' => $city,
                'restrictions' => $restrictions,
                'allowed' => range(0, 9),
                'isWeekend' => $isWeekend,
                'isHoliday' => $isHoliday,
                'horario' => $configuraciones[$city]['horario']
            ];
            
            $isDatePage = true;
        } catch (Exception $e) {}
    }
}

if ($isDatePage) {
    $title = "Pico y placa " . $dateData['dayNameEs'] . " " . $dateData['dayNum'] . " de " . $dateData['monthName'] . " " . $dateData['year'] . " en " . $dateData['cityName'];
    $description = "Consulta el pico y placa para el " . $dateData['dayNameEs'] . " " . $dateData['dayNum'] . " de " . $dateData['monthName'] . ". Placas: " . (count($dateData['restrictions']) > 0 ? implode(', ', $dateData['restrictions']) : 'Sin restricción');
    $keywords = "pico y placa, pico y placa " . $dateData['dayNum'] . " " . $dateData['monthName'] . ", " . $dateData['cityName'];
} else {
    $title = "Pico y placa Bogota hoy - Consultar restricción vehicular";
    $description = "Pico y placa Bogota hoy, Medellín y Cali en tiempo real.";
    $keywords = "pico y placa bogota, pico y placa hoy en bogota, pico y placa, restriccion vehicular bogota, hora pico y placa bogota ";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Pico y PL">
    
    <link rel="manifest" href="/manifest.json">
    <link rel="sitemap" type="application/xml" href="https://picoyplacabogota.com.co/sitemap.xml.php">
    
    <title><?php echo htmlspecialchars($title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
    
    <?php if ($isDatePage): ?>
    <link rel="canonical" href="https://picoyplacabogota.com.co/pico-y-placa/<?php echo $dateData['year']; ?>-<?php echo str_pad($dateData['month'], 2, '0', STR_PAD_LEFT); ?>-<?php echo str_pad($dateData['day'], 2, '0', STR_PAD_LEFT); ?>-<?php echo $dateData['city']; ?>">
    <?php else: ?>
    <link rel="canonical" href="https://picoyplacabogota.com.co/">
    <?php endif; ?>
    
    <link rel="icon" type="image/png" sizes="192x192" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 192 192'><rect fill='%23667eea' width='192' height='192'/><text x='50%' y='50%' font-size='120' font-weight='bold' text-anchor='middle' dy='.3em' fill='white' font-family='Arial'>🚗</text></svg>">
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta property="og:type" content="website">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-2L2EV10ZWW"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-2L2EV10ZWW');
</script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html, body { width: 100%; height: 100%; overflow-x: hidden; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 10px; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        header { text-align: center; color: white; margin-bottom: 20px; padding: 15px 10px; position: relative; }
        h1 { font-size: clamp(1.5rem, 8vw, 3rem); margin-bottom: 8px; font-weight: 800; }
        .subtitle { font-size: clamp(0.85rem, 3vw, 1.1rem); opacity: 0.95; }
        
        .install-btn { position: absolute; top: 10px; right: 10px; background: white; color: #667eea; border: none; padding: 8px 16px; border-radius: 20px; font-weight: 600; cursor: pointer; font-size: 0.85rem; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; }
        .install-btn.show { display: block; }
        .install-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.2); }
        .install-btn::before { content: "⬇️ "; margin-right: 5px; }
        
        @media (max-width: 600px) {
            .install-btn { top: 5px; right: 5px; padding: 6px 12px; font-size: 0.75rem; }
            h1 { margin-bottom: 5px; }
        }
        
        .countdown-section { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); display: none; }
        .countdown-section.show { display: block; }
        .countdown-section.ending-soon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .countdown-section.not-active { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
        .countdown-section h3 { font-size: 1.1rem; margin-bottom: 12px; font-weight: 700; text-align: center; }
        
        .countdown-display { display: grid; grid-template-columns: repeat(auto-fit, minmax(70px, 1fr)); gap: 10px; margin-bottom: 15px; }
        .countdown-item { text-align: center; background: rgba(255,255,255,0.3); padding: 12px; border-radius: 10px; }
        .countdown-value { font-size: clamp(1.2rem, 4vw, 2.5rem); font-weight: 800; }
        .countdown-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        
        .today-info { background: white; padding: 15px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; }
        .info-card { padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; text-align: center; }
        .info-card h3 { font-size: 0.75rem; text-transform: uppercase; margin-bottom: 8px; }
        .info-card p { font-size: clamp(1rem, 4vw, 1.5rem); font-weight: 800; }
        
        .main-content { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        @media (max-width: 900px) { .main-content { grid-template-columns: 1fr; gap: 15px; } }
        
        .search-box, .restrictions-today { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        @media (min-width: 600px) { .search-box, .restrictions-today { padding: 30px; } }
        
        .city-selector h2, .plate-input-section label, .restrictions-today h2 { font-size: clamp(1rem, 3vw, 1.2rem); margin-bottom: 12px; color: #333; font-weight: 700; }
        
        .cities-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 8px; }
        .city-btn { padding: 10px 15px; border: 2px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; font-size: 0.9rem; min-height: 44px; }
        .city-btn:active { transform: scale(0.98); }
        .city-btn.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-color: #667eea; }
        
        .input-group { display: flex; gap: 10px; flex-wrap: wrap; }
        input[type="text"], input[type="date"], select { flex: 1; min-width: 100px; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; text-align: center; font-family: 'Poppins', sans-serif; min-height: 44px; }
        
        .btn-search { padding: 12px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.9rem; white-space: nowrap; min-height: 44px; transition: all 0.3s; }
        .btn-search:active, .btn-search:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3); }
        
        .result-box { margin-top: 20px; padding: 20px; border-radius: 12px; display: none; }
        .result-box.show { display: block; }
        .result-success { background: #d4edda; border: 2px solid #28a745; color: #155724; }
        .result-restricted { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; }
        
        .plates-list { display: flex; flex-wrap: wrap; gap: 8px; }
        .plate-badge { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 8px 14px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; }
        .plate-badge.allowed { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
        
        .info-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; }
        @media (min-width: 600px) { .info-section { padding: 30px; } }
        .info-section h2 { color: white; margin-bottom: 15px; }
        .info-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
        
        .date-search-section { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        @media (min-width: 600px) { .date-search-section { padding: 30px; } }
        .date-input-group { display: flex; gap: 10px; flex-wrap: wrap; }
        
        .back-btn { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background: #667eea; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; min-height: 44px; transition: all 0.3s; }
        .breadcrumb { background: rgba(255,255,255,0.2); padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; color: white; display: none; }
        .breadcrumb.show { display: block; }
        .breadcrumb a { color: white; text-decoration: underline; cursor: pointer; }
        
        footer { text-align: center; color: white; padding: 15px; opacity: 0.9; font-size: 0.9rem; }
        .info-block { background: #f0f0f0; padding: 12px; border-radius: 8px; margin-top: 12px; font-size: 0.9rem; }
        .info-block p { margin-bottom: 8px; }
        .no-restriction { color: #28a745; font-weight: 700; }
        
        @media (max-width: 480px) {
            body { padding: 8px; }
            .search-box, .restrictions-today { padding: 15px; }
            .date-search-section { padding: 15px; }
            .info-section { padding: 15px; }
            .main-content { gap: 10px; }
            .cities-grid { gap: 6px; }
            input, select { font-size: 16px; }
        }
                         
                         .btn-noticias-premium {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-noticias-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-noticias-premium .icono {
    font-size: 20px;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb" id="breadcrumb">
            <a onclick="backToHome()">🏠 Inicio</a> / <span id="breadcrumb-text">Búsqueda</span>
        </div>
        
        <header>
            <button class="install-btn" id="installBtn" onclick="installApp()">Instalar App</button>
            <h1>🚗 Pico y Placa Bogota Hoy</h1>
            <p class="subtitle">Pico y Placa Bogota, Medellin y Cali en tiempo real</p>
<a href="/noticias" class="btn-noticias-premium">
    <span class="icono">📰</span>
    <span class="texto">Noticias Recientes</span>
</a>
        </header>
        
        <?php if (!$isDatePage): ?>
        
        <div class="date-search-section">
            <h2 style="font-size: clamp(1rem, 3vw, 1.1rem); margin-bottom: 12px;">📅 Buscar por Fecha</h2>
            <form class="date-input-group" onsubmit="searchByDate(event)">
                <input type="date" id="dateInput" required>
                <select id="citySelect">
                    <option value="bogota">Bogotá</option>
                    <option value="medellin">Medellín</option>
                    <option value="cali">Cali</option>
                </select>
                <button type="submit" class="btn-search">Buscar</button>
            </form>
        </div>
        
        <div class="countdown-section ending-soon" id="countdownEnding">
            <h3>⏰ Pico y Placa Activo</h3>
            <div class="countdown-display">
                <div class="countdown-item"><div class="countdown-value" id="countdownHours">00</div><div class="countdown-label">Horas</div></div>
                <div class="countdown-item"><div class="countdown-value" id="countdownMinutes">00</div><div class="countdown-label">Mins</div></div>
                <div class="countdown-item"><div class="countdown-value" id="countdownSeconds">00</div><div class="countdown-label">Segs</div></div>
            </div>
            <div style="text-align: center; font-weight: 600; font-size: 0.95rem;">Falta para terminar</div>
        </div>
        
        <div class="countdown-section not-active" id="countdownStarting">
            <h3>✅ Sin Pico y Placa</h3>
            <div class="countdown-display">
                <div class="countdown-item"><div class="countdown-value" id="countdownHours2">00</div><div class="countdown-label">Horas</div></div>
                <div class="countdown-item"><div class="countdown-value" id="countdownMinutes2">00</div><div class="countdown-label">Mins</div></div>
                <div class="countdown-item"><div class="countdown-value" id="countdownSeconds2">00</div><div class="countdown-label">Segs</div></div>
            </div>
            <div style="text-align: center; font-weight: 600; font-size: 0.95rem;">Falta para iniciar</div>
        </div>
        
        <div class="today-info">
            <div class="info-card"><h3>📅 Hoy</h3><p id="today-date">--</p></div>
            <div class="info-card"><h3>🚫 Restricción</h3><p id="today-plates-restricted">--</p></div>
            <div class="info-card"><h3>🕐 Horario</h3><p id="city-schedule">--</p></div>
        </div>
        
        <div class="main-content">
            <div class="search-box">
                <div class="city-selector">
                    <h2>Tu ciudad</h2>
                    <div class="cities-grid">
                        <button type="button" class="city-btn" id="btn-bogota" onclick="selectCity('bogota')">Bogotá</button>
                        <button type="button" class="city-btn" id="btn-medellin" onclick="selectCity('medellin')">Medellín</button>
                        <button type="button" class="city-btn" id="btn-cali" onclick="selectCity('cali')">Cali</button>
                    </div>
                </div>
                
                <div class="plate-input-section">
                    <label>Última placa (0-9)</label>
                    <div class="input-group">
                        <input type="text" id="plate-input" placeholder="5" maxlength="1" inputmode="numeric">
                        <button type="button" class="btn-search" onclick="searchPlate()">Consultar</button>
                    </div>
                </div>
                
                <div id="result-box" class="result-box">
                    <h3 id="result-title" style="margin-bottom: 10px;"></h3>
                    <p id="result-text" style="margin-bottom: 10px;"></p>
                    <div id="result-info"></div>
                </div>
            </div>
            
            <div class="restrictions-today">
                <h2>Restricciones HOY</h2>
                <h3 id="city-today" style="color: #667eea; font-size: 1rem; margin-bottom: 10px;">Bogotá</h3>
                <p style="margin-bottom: 10px; font-weight: 600; color: #dc3545; font-size: 0.9rem;">🚫 Con restricción:</p>
                <div class="plates-list" id="plates-restricted-today"></div>
                <p style="margin: 15px 0 10px 0; font-weight: 600; color: #28a745; font-size: 0.9rem;">✅ Habilitadas:</p>
                <div class="plates-list" id="plates-allowed-today"></div>
                <div class="info-block">
                    <p><strong>📅 Día:</strong> <span id="day-today">--</span></p>
                    <p><strong>🕐 Horario:</strong> <span id="schedule-today">--</span></p>
                    <p><strong>📍 Ciudad:</strong> <span id="city-name-today">Bogotá</span></p>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <h2>ℹ️ Información</h2>
            <div class="info-list">
                <div><strong>🚗 Exentos:</strong><p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 0.9rem;">Eléctricos, híbridos, gas natural</p></div>
                <div><strong>📅 Fin de Semana:</strong><p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 0.9rem;">Sin restricción</p></div>
                <div><strong>🎉 Festivos:</strong><p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 0.9rem;">Sin restricción</p></div>
                <div><strong>⚠️ Multas:</strong><p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 0.9rem;">$600K - $900K</p></div>
            </div>
        </div>
        
        <?php else: ?>
        
        <div class="breadcrumb show">
            <a onclick="backToHome()">🏠 Inicio</a> / 📅 <?php echo htmlspecialchars($dateData['dayNum'] . ' de ' . $dateData['monthName']); ?>
        </div>
        
        <button class="back-btn" onclick="backToHome()">← Volver</button>
        
        <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h2 style="font-size: clamp(1rem, 4vw, 1.5rem); margin-bottom: 15px;">📅 <?php echo htmlspecialchars($dateData['dayNum'] . ' de ' . $dateData['monthName'] . ' de ' . $dateData['year']); ?></h2>
            
            <div class="info-block">
                <p><strong>📅 Fecha:</strong> <?php echo ucfirst($dateData['dayNameEs']) . ', ' . htmlspecialchars($dateData['dayNum'] . ' de ' . $dateData['monthName'] . ' de ' . $dateData['year']); ?></p>
                <p><strong>📍 Ciudad:</strong> <?php echo htmlspecialchars($dateData['cityName']); ?></p>
                <p><strong>🕐 Horario:</strong> <?php echo htmlspecialchars($dateData['horario']); ?></p>
                <p><strong>📊 Estado:</strong> 
                    <?php 
                    if ($dateData['isWeekend']) {
                        echo '<span style="color: #28a745;">✅ Sin restricción (Fin de semana)</span>';
                    } elseif ($dateData['isHoliday']) {
                        echo '<span style="color: #28a745;">✅ Sin restricción (Día festivo)</span>';
                    } elseif (count($dateData['restrictions']) > 0) {
                        echo '<span style="color: #dc3545;">⚠️ Hay restricción</span>';
                    } else {
                        echo '<span style="color: #28a745;">✅ Sin restricción</span>';
                    }
                    ?>
                </p>
            </div>
            
            <p style="margin: 15px 0 10px 0; font-weight: 600; color: #dc3545; font-size: 0.9rem;">🚫 Con restricción:</p>
            <div class="plates-list">
                <?php
                if ($dateData['isWeekend']) {
                    echo '<p class="no-restriction">✅ Fin de semana</p>';
                } elseif ($dateData['isHoliday']) {
                    echo '<p class="no-restriction">✅ Día festivo</p>';
                } elseif (count($dateData['restrictions']) > 0) {
                    foreach ($dateData['restrictions'] as $plate) echo '<span class="plate-badge">' . htmlspecialchars($plate) . '</span>';
                } else {
                    echo '<p class="no-restriction">✅ Sin restricción</p>';
                }
                ?>
            </div>
            
            <p style="margin: 15px 0 10px 0; font-weight: 600; color: #28a745; font-size: 0.9rem;">✅ Habilitadas:</p>
            <div class="plates-list">
                <?php
                if ($dateData['isWeekend'] || $dateData['isHoliday']) {
                    echo '<p class="no-restriction">✅ Todas (0-9)</p>';
                } elseif (count($dateData['restrictions']) > 0) {
                    $allowed = array_diff(range(0, 9), $dateData['restrictions']);
                    foreach ($allowed as $plate) echo '<span class="plate-badge allowed">' . htmlspecialchars($plate) . '</span>';
                } else {
                    echo '<p class="no-restriction">✅ Todas (0-9)</p>';
                }
                ?>
            </div>
        </div>
        
        <?php endif; ?>
        
        <footer>
            <p><strong>Pico y PL</strong> - Colombia 2025</p>
        </footer>
    </div>
    
    <script>
        let deferredPrompt;
        let installBtn = document.getElementById('installBtn');
        let selectedCity = 'bogota';
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installBtn.classList.add('show');
        });
        
        async function installApp() {
            if (!deferredPrompt) return alert('La app ya está instalada');
            deferredPrompt.prompt();
            deferredPrompt = null;
            installBtn.classList.remove('show');
        }
        
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js').catch(e => console.log('SW error:', e));
        }
        
        const config = {
            bogota: {nombre: 'Bogotá', horario: '6:00 a.m. - 9:00 p.m.', inicio: 6, fin: 21},
            medellin: {nombre: 'Medellín', horario: '5:00 a.m. - 8:00 p.m.', inicio: 5, fin: 20},
            cali: {nombre: 'Cali', horario: '6:00 a.m. - 7:00 p.m.', inicio: 6, fin: 19}
        };
        
        const restricciones = {
            medellin: {Monday: [1,8], Tuesday: [3,4], Wednesday: [2,9], Thursday: [5,7], Friday: [0,6], Saturday: [], Sunday: []},
            cali: {Monday: [5,6], Tuesday: [7,8], Wednesday: [9,0], Thursday: [1,2], Friday: [3,4], Saturday: [], Sunday: []}
        };
        
        const colombiaHolidays2025 = [
            '2025-01-01', '2025-01-06', '2025-03-24', '2025-04-17', '2025-04-18', '2025-04-20',
            '2025-05-01', '2025-05-29', '2025-06-19', '2025-06-23', '2025-06-30', '2025-07-20',
            '2025-08-07', '2025-08-18', '2025-10-13', '2025-11-03', '2025-11-17', '2025-12-08', '2025-12-25'
        ];
        
        function getRestrictionsBogota(dayOfMonth) {
            const isOdd = dayOfMonth % 2 === 1;
            return isOdd ? [6, 7, 8, 9, 0] : [1, 2, 3, 4, 5];
        }
        
        function isHolidayToday(date) {
            const dateStr = date.toISOString().split('T')[0];
            return colombiaHolidays2025.includes(dateStr);
        }
        
        function getRestrictionsForDay(city, dayName, dayOfMonth, date) {
            if (city === 'bogota') {
                return getRestrictionsBogota(dayOfMonth);
            }
            return restricciones[city][dayName] || [];
        }
        
        function updateTodayInfo() {
            const today = new Date();
            const dayIndex = today.getDay();
            const dayOfMonth = today.getDate();
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayNamesEs = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            const dayName = dayNames[dayIndex];
            
            const options = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'};
            const dateStr = today.toLocaleDateString('es-CO', options);
            
            document.getElementById('today-date').textContent = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
            document.getElementById('day-today').textContent = dayNamesEs[dayIndex].charAt(0).toUpperCase() + dayNamesEs[dayIndex].slice(1);
            document.getElementById('city-today').textContent = config[selectedCity].nombre;
            document.getElementById('city-name-today').textContent = config[selectedCity].nombre;
            document.getElementById('schedule-today').textContent = config[selectedCity].horario;
            document.getElementById('city-schedule').textContent = config[selectedCity].horario;
            
            const isWeekend = dayIndex === 0 || dayIndex === 6;
            const isHoliday = isHolidayToday(today);
            const restrictions = (isWeekend || isHoliday) ? [] : getRestrictionsForDay(selectedCity, dayName, dayOfMonth, today);
            const allowed = [0,1,2,3,4,5,6,7,8,9].filter(p => !restrictions.includes(p));
            
            document.getElementById('today-plates-restricted').textContent = restrictions.length > 0 ? restrictions.join(', ') : 'Libre';
            document.getElementById('plates-restricted-today').innerHTML = restrictions.length > 0 ? restrictions.map(p => '<span class="plate-badge">' + p + '</span>').join('') : '<p class="no-restriction">✅ Sin restricción</p>';
            document.getElementById('plates-allowed-today').innerHTML = allowed.map(p => '<span class="plate-badge allowed">' + p + '</span>').join('');
            
            updateCountdown();
        }
        
        function updateCountdown() {
            const now = new Date();
            const dayIndex = now.getDay();
            const isWeekend = dayIndex === 0 || dayIndex === 6;
            const isHoliday = isHolidayToday(now);
            const hora = config[selectedCity];
            
            let timeUntil = null, isActive = false;
            
            if (!isWeekend && !isHoliday && now.getHours() >= hora.inicio && now.getHours() < hora.fin) {
                isActive = true;
                const endTime = new Date();
                endTime.setHours(hora.fin, 0, 0, 0);
                timeUntil = endTime - now;
            } else {
                let startTime = new Date();
                startTime.setHours(hora.inicio, 0, 0, 0);
                if (startTime <= now) startTime.setDate(startTime.getDate() + 1);
                while (startTime.getDay() === 0 || startTime.getDay() === 6 || isHolidayToday(startTime)) {
                    startTime.setDate(startTime.getDate() + 1);
                }
                timeUntil = startTime - now;
            }
            
            const total = Math.floor(timeUntil / 1000);
            const hours = Math.floor(total / 3600);
            const mins = Math.floor((total % 3600) / 60);
            const secs = total % 60;
            
            if (isActive) {
                document.getElementById('countdownEnding').classList.add('show');
                document.getElementById('countdownStarting').classList.remove('show');
                document.getElementById('countdownHours').textContent = String(hours).padStart(2, '0');
                document.getElementById('countdownMinutes').textContent = String(mins).padStart(2, '0');
                document.getElementById('countdownSeconds').textContent = String(secs).padStart(2, '0');
            } else {
                document.getElementById('countdownStarting').classList.add('show');
                document.getElementById('countdownEnding').classList.remove('show');
                document.getElementById('countdownHours2').textContent = String(hours).padStart(2, '0');
                document.getElementById('countdownMinutes2').textContent = String(mins).padStart(2, '0');
                document.getElementById('countdownSeconds2').textContent = String(secs).padStart(2, '0');
            }
        }
        
        function selectCity(city) {
            selectedCity = city;
            document.querySelectorAll('.city-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('btn-' + city).classList.add('active');
            updateTodayInfo();
            document.getElementById('result-box').classList.remove('show');
            document.getElementById('plate-input').value = '';
        }
        
        function searchPlate() {
            const plate = document.getElementById('plate-input').value;
            if (!plate || isNaN(plate)) return alert('Solo 0-9');
            
            const today = new Date();
            const dayIndex = today.getDay();
            const dayOfMonth = today.getDate();
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const restrictions = getRestrictionsForDay(selectedCity, dayNames[dayIndex], dayOfMonth, today);
            const isWeekend = dayIndex === 0 || dayIndex === 6;
            const isHoliday = isHolidayToday(today);
            const hasRestriction = restrictions.includes(parseInt(plate));
            
            const box = document.getElementById('result-box');
            box.classList.remove('result-success', 'result-restricted');
            
            if (isWeekend) {
                box.classList.add('result-success');
                document.getElementById('result-title').textContent = '✅ Puedes circular';
                document.getElementById('result-text').textContent = 'Placa ' + plate + ': Sin restricción (fin de semana)';
            } else if (isHoliday) {
                box.classList.add('result-success');
                document.getElementById('result-title').textContent = '✅ Puedes circular';
                document.getElementById('result-text').textContent = 'Placa ' + plate + ': Sin restricción (día festivo)';
            } else if (hasRestriction) {
                box.classList.add('result-restricted');
                document.getElementById('result-title').textContent = '⚠️ ¡RESTRICCIÓN!';
                document.getElementById('result-text').textContent = 'Tu placa ' + plate + ' NO puede circular hoy';
            } else {
                box.classList.add('result-success');
                document.getElementById('result-title').textContent = '✅ Puedes circular';
                document.getElementById('result-text').textContent = 'Tu placa ' + plate + ' puede circular hoy';
            }
            box.classList.add('show');
        }
        
        function searchByDate(e) {
            e.preventDefault();
            const date = document.getElementById('dateInput').value;
            const city = document.getElementById('citySelect').value;
            if (date) {
                const [year, month, day] = date.split('-');
                window.location.href = `/pico-y-placa/${year}-${month}-${day}-${city}`;
            }
        }
        
        function backToHome() {
            window.location.href = '/';
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-bogota').classList.add('active');
            updateTodayInfo();
            setInterval(updateCountdown, 1000);
            const plateInput = document.getElementById('plate-input');
            if (plateInput) {
                plateInput.addEventListener('input', function() { this.value = this.value.replace(/[^0-9]/g, ''); });
                plateInput.addEventListener('keypress', function(e) { if (e.key === 'Enter') searchPlate(); });
            }
        });
    </script>
</body>
</html>