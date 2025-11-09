# 🚗 Pico y PL - Sistema de Consulta de Pico y Placa

Aplicación web responsiva para consultar restricciones vehiculares en tiempo real en Bogotá, Medellín y Cali, Colombia.

## 📋 Características

✅ **Lógica de Pico y Placa Correcta**
- Bogotá: Días impares (6,7,8,9,0) - Días pares (1,2,3,4,5)
- Medellín: Por día de la semana
- Cali: Por día de la semana

✅ **Funcionalidades**
- Búsqueda por placa (0-9)
- Búsqueda por fecha específica
- Reloj de cuenta regresiva (HH:MM:SS)
- Información de hoy en tiempo real
- Festivos colombianos incluidos
- Sitemap automático (60 días)
- PWA descargable

✅ **Diseño**
- 100% Responsivo (móvil, tablet, desktop)
- Gradientes modernas
- Touch-friendly
- Instalable como app

## 📂 Estructura de Archivos

```
/
├── index.php                 # Archivo principal
├── sitemap.xml.php          # Sitemap dinámico (60 días)
├── .htaccess                # Rewrite rules + caché
├── manifest.json            # Configuración PWA
├── service-worker.js        # Service Worker
├── robots.txt               # Configuración SEO
└── config.php              # Configuración (opcional)
```

## 🚀 Instalación Rápida

### Paso 1: Subir Archivos

Sube estos archivos a `/var/www/html/` en tu servidor:

```bash
- index.php
- sitemap.xml.php
- .htaccess
- manifest.json
- service-worker.js
- robots.txt
```

### Paso 2: Permisos

```bash
chmod 755 /var/www/html
chmod 644 /var/www/html/*.php
chmod 644 /var/www/html/.htaccess
chmod 644 /var/www/html/*.json
chmod 644 /var/www/html/*.js
chmod 644 /var/www/html/*.txt
```

### Paso 3: Verificar Requisitos

- PHP 7.2+
- Apache con mod_rewrite habilitado
- HTTPS (recomendado para PWA)

### Paso 4: Probar

Accede a:
- https://picoypl.com.co/
- https://picoypl.com.co/pico-y-placa/2025-11-05-bogota
- https://picoypl.com.co/sitemap.xml

## 📱 Funciones Principales

### 1. Consultar por Placa
- Ingresa último dígito (0-9)
- Muestra si hay restricción hoy
- Diferencia fin de semana y festivos

### 2. Consultar por Fecha
- Selecciona fecha y ciudad
- URL permanente: `/pico-y-placa/YYYY-MM-DD-ciudad`
- SEO optimizado

### 3. Reloj en Vivo
- Cuenta regresiva HH:MM:SS
- Muestra tiempo hasta término/inicio
- Se actualiza cada segundo

### 4. Información Hoy
- Restricción actual
- Placas afectadas
- Horario de aplicación

## 🔧 Configuración Personalizada

### Editar Horarios
En `index.php`, sección `$configuraciones`:

```php
'bogota' => [
    'horario' => '6:00 a.m. - 9:00 p.m.',
    'horarioInicio' => 6,
    'horarioFin' => 21
]
```

### Agregar Festivos Nuevos
En `index.php`, variable `$colombiaHolidays2025`:

```php
'2025-12-25', // Navidad
// Agregar más fechas aquí
```

### Cambiar Colores
En CSS (sección `<style>`):

```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
/* Cambiar gradiente de fondo */
```

## 🌐 URLs Importantes

```
POST/GET /                                     # Página principal
GET /pico-y-placa/YYYY-MM-DD-bogota          # Consulta por fecha
GET /sitemap.xml                               # Sitemap
GET /robots.txt                                # Para motores de búsqueda
GET /manifest.json                             # Configuración PWA
```

## 📊 Lógica de Pico y Placa

### Bogotá (Por día del mes)
```
Día impar:  Restricción 6, 7, 8, 9, 0
Día par:    Restricción 1, 2, 3, 4, 5
```

### Medellín (Por día de semana)
```
Lunes:      1, 8
Martes:     3, 4
Miércoles:  2, 9
Jueves:     5, 7
Viernes:    0, 6
```

### Cali (Por día de semana)
```
Lunes:      5, 6
Martes:     7, 8
Miércoles:  9, 0
Jueves:     1, 2
Viernes:    3, 4
```

### Sin Restricción
- Sábados
- Domingos
- Días festivos de Colombia

## 🎨 PWA - Instalación como App

El sitio es una PWA (Progressive Web App):

**En móvil:**
- Botón "Instalar App" aparece automáticamente
- Se instala como app nativa
- Funciona sin internet (con caché)

**En desktop (Chrome):**
- Click en ícono de instalación (arriba derecha)
- O desde menú > "Instalar Pico y PL"

## 🔍 SEO

- Sitemap automático: 180 URLs (60 días × 3 ciudades)
- Structured Data (Schema.org)
- Open Graph meta tags
- URLs limpias y descriptivas
- Caché HTTP automático

## 📈 Optimizaciones

✅ **Rendimiento**
- Compresión GZIP automática
- Caché HTTP de 1 hora (HTML), 1 mes (CSS/JS)
- Minificación CSS/JS

✅ **SEO**
- Canonical URLs
- Meta descriptions dinámicas
- Schema.org FAQPage
- Sitemap XML

✅ **Accesibilidad**
- Colores de alto contraste
- Texto descriptivo
- Navegación por teclado
- Botones 44px mínimo (mobile)

## 🐛 Troubleshooting

### URLs no funcionan
- Verificar mod_rewrite en Apache
- Verificar .htaccess en la raíz

### PWA no se instala
- Servir por HTTPS
- Verificar manifest.json válido
- Verificar service-worker.js registrado

### Reloj no se actualiza
- Verificar JS habilitado en navegador
- Abrir consola del navegador (F12) para errores

## 📞 Soporte

Para problemas:
1. Revisar consola del navegador (F12)
2. Verificar permisos de archivos
3. Verificar configuración de Apache/PHP

## 📝 Notas Importantes

- La aplicación calcula automáticamente festivos de Colombia 2025
- Para años posteriores, agregar fechas en `$colombiaHolidays2025`
- El sitemap se genera dinámicamente cada vez que se accede
- El caché se actualiza automáticamente

## 📄 Licencia

Libre para uso personal y comercial.

---

**Versión:** 1.0  
**Última actualización:** 2025-11-05  
**Soporte para:** Colombia
