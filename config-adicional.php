<?php
/**
 * Configuración Adicional - Pico y PL
 * 
 * Este archivo contiene configuraciones opcionales avanzadas.
 * No es necesario para el funcionamiento básico.
 * 
 * Uso: Incluir en index.php si necesitas personalización adicional
 */

// ==========================================
// 1. CONFIGURACIÓN DE DOMINIO
// ==========================================

define('DOMAIN', 'https://picoyplacabogota.com.co');
define('ENVIRONMENT', 'production'); // 'development' o 'production'

// ==========================================
// 2. CONFIGURACIÓN DE FESTIVOS DINÁMICOS
// ==========================================

/**
 * Agregar festivos adicionales por año
 * Formato: 'YYYY-MM-DD'
 */
$additionalHolidays = [
    // 2026
    '2026-01-01', // Año Nuevo
    '2026-01-06', // Reyes Magos
    '2026-03-25', // San José
    '2026-04-02', // Jueves Santo
    '2026-04-03', // Viernes Santo
    '2026-04-05', // Domingo de Pascua
    '2026-05-01', // Día del Trabajo
    '2026-05-14', // Ascensión
    '2026-06-04', // Corpus Christi
    '2026-06-08', // Sagrado Corazón
    '2026-06-29', // San Pedro y San Pablo
    '2026-07-20', // Grito de Independencia
    '2026-08-07', // Batalla de Boyacá
    '2026-08-17', // Asunción de la Virgen
    '2026-10-12', // Día de la Raza
    '2026-11-02', // Todos los Santos
    '2026-11-16', // Independencia de Cartagena
    '2026-12-08', // Inmaculada Concepción
    '2026-12-25', // Navidad
];

// ==========================================
// 3. CONFIGURACIÓN DE CIUDADES
// ==========================================

/**
 * Personalizar ciudades
 * Agregar o modificar según necesidad
 */
$customCities = [
    'bogota' => [
        'nombre' => 'Bogotá',
        'horario' => '6:00 a.m. - 9:00 p.m.',
        'horarioInicio' => 6,
        'horarioFin' => 21,
        'zona' => 'Andina',
        'poblacion' => '8 millones',
        'latitud' => 4.7110,
        'longitud' => -74.0055
    ],
    'medellin' => [
        'nombre' => 'Medellín',
        'horario' => '5:00 a.m. - 8:00 p.m.',
        'horarioInicio' => 5,
        'horarioFin' => 20,
        'zona' => 'Andina',
        'poblacion' => '2.5 millones',
        'latitud' => 6.2518,
        'longitud' => -75.5636
    ],
    'cali' => [
        'nombre' => 'Cali',
        'horario' => '6:00 a.m. - 7:00 p.m.',
        'horarioInicio' => 6,
        'horarioFin' => 19,
        'zona' => 'Pacífica',
        'poblacion' => '2.2 millones',
        'latitud' => 3.4372,
        'longitud' => -76.5197
    ]
];

// ==========================================
// 4. CONFIGURACIÓN DE ANALYTICS Y TRACKING
// ==========================================

define('GOOGLE_ANALYTICS_ID', 'G-2L2EV10ZWW'); // Reemplazar con tu ID
define('GOOGLE_SEARCH_CONSOLE', 'PicoyplacaBogota'); // Google Search Console

// ==========================================
// 5. CONFIGURACIÓN DE EMAIL (OPCIONAL)
// ==========================================

define('ADMIN_EMAIL', 'admin@picoyplacabogota.com.co');
define('NOTIFICATION_EMAIL', 'notifications@picoyplacabogota.com.co');

/**
 * Enviar notificación cuando hay errores
 */
function sendErrorNotification($error) {
    if (ENVIRONMENT === 'production') {
        $to = ADMIN_EMAIL;
        $subject = "Error en Pico y PL";
        $message = "Error: " . $error . "\nIP: " . $_SERVER['REMOTE_ADDR'];
        mail($to, $subject, $message);
    }
}

// ==========================================
// 6. CONFIGURACIÓN DE CACHÉ
// ==========================================

define('CACHE_ENABLED', true);
define('CACHE_TIME', 3600); // 1 hora en segundos

/**
 * Funciones de caché
 */
function getCache($key) {
    $file = '/tmp/pico-y-pl-' . md5($key) . '.cache';
    if (file_exists($file) && (time() - filemtime($file)) < CACHE_TIME) {
        return unserialize(file_get_contents($file));
    }
    return null;
}

function setCache($key, $value) {
    if (CACHE_ENABLED) {
        $file = '/tmp/pico-y-pl-' . md5($key) . '.cache';
        file_put_contents($file, serialize($value));
    }
}

function clearCache($key = null) {
    if ($key) {
        $file = '/tmp/pico-y-pl-' . md5($key) . '.cache';
        if (file_exists($file)) {
            unlink($file);
        }
    } else {
        array_map('unlink', glob('/tmp/pico-y-pl-*.cache'));
    }
}

// ==========================================
// 7. CONFIGURACIÓN DE LOGGING
// ==========================================

define('LOG_ENABLED', true);
define('LOG_FILE', '/var/log/pico-y-pl.log');

function logEvent($event, $level = 'INFO') {
    if (LOG_ENABLED && is_writable(dirname(LOG_FILE))) {
        $timestamp = date('Y-m-d H:i:s');
        $message = "[{$timestamp}] [{$level}] {$event}\n";
        file_put_contents(LOG_FILE, $message, FILE_APPEND);
    }
}

// ==========================================
// 8. FUNCIONES ÚTILES
// ==========================================

/**
 * Obtener IP del usuario
 */
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Obtener ubicación por IP (opcional)
 */
function getLocationByIP($ip) {
    // Usar API externa como ip-api.com
    // $url = "http://ip-api.com/json/{$ip}";
    // $data = json_decode(file_get_contents($url), true);
    // return $data;
}

/**
 * Generar sitemap dinámico
 */
function generateSitemap() {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    $cities = ['bogota', 'medellin', 'cali'];
    $today = new DateTime();
    
    for ($i = 0; $i < 60; $i++) {
        $date = clone $today;
        $date->modify("+$i days");
        
        $year = $date->format('Y');
        $month = str_pad($date->format('m'), 2, '0', STR_PAD_LEFT);
        $day = str_pad($date->format('d'), 2, '0', STR_PAD_LEFT);
        
        foreach ($cities as $city) {
            $url = DOMAIN . "/pico-y-placa/{$year}-{$month}-{$day}-{$city}";
            $lastmod = $date->format('Y-m-d');
            $priority = ($i <= 7) ? '0.9' : '0.5';
            
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url}</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <priority>{$priority}</priority>\n";
            $xml .= "  </url>\n";
        }
    }
    
    $xml .= '</urlset>';
    return $xml;
}

// ==========================================
// 9. ESTADÍSTICAS (OPCIONAL)
// ==========================================

function trackPageView() {
    $ip = getUserIP();
    $page = $_SERVER['REQUEST_URI'];
    $timestamp = date('Y-m-d H:i:s');
    
    logEvent("PageView - IP: {$ip}, Page: {$page}");
}

/**
 * Registrar búsqueda de placa
 */
function trackPlateSearch($plate, $city) {
    $ip = getUserIP();
    logEvent("PlateSearch - IP: {$ip}, Plate: {$plate}, City: {$city}");
}

/**
 * Registrar búsqueda por fecha
 */
function trackDateSearch($date, $city) {
    $ip = getUserIP();
    logEvent("DateSearch - IP: {$ip}, Date: {$date}, City: {$city}");
}

// ==========================================
// 10. FUNCIONES DE ADMINISTRADOR
// ==========================================

/**
 * Verificar si es admin
 */
function isAdmin() {
    // Implementar autenticación según necesidad
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Panel de administrador (URL privada)
 */
function adminPanel() {
    if (!isAdmin()) {
        return "No autorizado";
    }
    
    $stats = [
        'total_hits' => 0,
        'total_searches' => 0,
        'unique_ips' => 0
    ];
    
    return $stats;
}

// ==========================================
// INICIALIZAR
// ==========================================

// Registrar vista de página en producción
if (ENVIRONMENT === 'production') {
    // trackPageView();
}

// Fin del archivo
?>