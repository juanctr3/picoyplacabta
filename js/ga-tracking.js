/**
 * Google Analytics Tracking - Eventos personalizados
 * /js/ga-tracking.js
 */

// Función para registrar evento en Google Analytics
function trackEvent(eventName, eventParams = {}) {
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, eventParams);
    }
}

// Rastrear tiempo de lectura del artículo
function trackArticleReadTime(articleId, articleTitle) {
    let readTime = 0;
    const checkInterval = setInterval(() => {
        readTime += 5; // Incrementar cada 5 segundos
        
        // Rastrear hitos de lectura
        if (readTime === 15) {
            trackEvent('article_read_15_seconds', {
                article_id: articleId,
                article_title: articleTitle
            });
        } else if (readTime === 30) {
            trackEvent('article_read_30_seconds', {
                article_id: articleId,
                article_title: articleTitle
            });
        } else if (readTime === 60) {
            trackEvent('article_read_1_minute', {
                article_id: articleId,
                article_title: articleTitle
            });
        } else if (readTime === 180) {
            trackEvent('article_read_3_minutes', {
                article_id: articleId,
                article_title: articleTitle
            });
        }
    }, 5000);
    
    // Detener cuando la pestaña se cierra
    window.addEventListener('beforeunload', () => {
        clearInterval(checkInterval);
    });
}

// Rastrear scroll del artículo
function trackArticleScroll(articleId, articleTitle) {
    let maxScroll = 0;
    
    window.addEventListener('scroll', () => {
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollTop = window.scrollY;
        const scrollPercent = Math.round((scrollTop / scrollHeight) * 100);
        
        if (scrollPercent > maxScroll) {
            maxScroll = scrollPercent;
            
            // Rastrear puntos de scroll
            if (scrollPercent === 25) {
                trackEvent('article_scroll_25', {
                    article_id: articleId,
                    article_title: articleTitle
                });
            } else if (scrollPercent === 50) {
                trackEvent('article_scroll_50', {
                    article_id: articleId,
                    article_title: articleTitle
                });
            } else if (scrollPercent === 75) {
                trackEvent('article_scroll_75', {
                    article_id: articleId,
                    article_title: articleTitle
                });
            } else if (scrollPercent === 100) {
                trackEvent('article_scroll_100', {
                    article_id: articleId,
                    article_title: articleTitle
                });
            }
        }
    });
}

// Rastrear clicks en botones
function trackButtonClick(buttonName, buttonAction) {
    const buttons = document.querySelectorAll(`[data-track-button="${buttonName}"]`);
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            trackEvent('button_click', {
                button_name: buttonName,
                button_action: buttonAction,
                timestamp: new Date().toISOString()
            });
        });
    });
}

// Rastrear compartir en redes
function trackSocialShare(platform, articleTitle) {
    trackEvent('social_share', {
        platform: platform,
        content_title: articleTitle,
        timestamp: new Date().toISOString()
    });
}

// Rastrear categoría visitada
function trackCategoryView(categoryName, articleCount = 0) {
    trackEvent('category_view', {
        category_name: categoryName,
        article_count: articleCount
    });
}

// Rastrear búsqueda de artículos
function trackArticleSearch(searchQuery, resultsCount = 0) {
    trackEvent('article_search', {
        search_query: searchQuery,
        results_count: resultsCount
    });
}

// Rastrear error 404
function trackNotFound(attemptedPath) {
    trackEvent('page_not_found', {
        attempted_path: attemptedPath
    });
}

// Rastrear tiempo en página
function trackTimeOnPage(pageName) {
    let startTime = Date.now();
    
    window.addEventListener('beforeunload', () => {
        let timeOnPage = Math.round((Date.now() - startTime) / 1000); // En segundos
        trackEvent('page_time', {
            page_name: pageName,
            time_seconds: timeOnPage
        });
    });
}

// Inicializar tracking para artículos
function initArticleTracking(articleData = {}) {
    const {
        id = '',
        title = '',
        category = '',
        author = ''
    } = articleData;
    
    if (id && title) {
        // Rastrear tiempo de lectura
        trackArticleReadTime(id, title);
        
        // Rastrear scroll
        trackArticleScroll(id, title);
        
        // Rastrear tiempo total en página
        trackTimeOnPage(title);
        
        // Rastrear vista de artículo
        trackEvent('article_view', {
            article_id: id,
            article_title: title,
            article_category: category,
            article_author: author
        });
    }
}

// Rastrear eventos de botones cuando carga la página
document.addEventListener('DOMContentLoaded', () => {
    // Botones de navegación
    trackButtonClick('home', 'go_to_home');
    trackButtonClick('category', 'go_to_category');
    
    // Botones de compartir
    const shareButtons = document.querySelectorAll('[data-share-platform]');
    shareButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const platform = btn.getAttribute('data-share-platform');
            const title = document.querySelector('h1, h2')?.textContent || 'Article';
            trackSocialShare(platform, title);
        });
    });
});
