<?php
function mesmerize_set_in_memory($key, $value = false)
{
    
    if ( ! isset($GLOBALS['MESMERIZE_MEMORY_CACHE'])) {
        $GLOBALS['MESMERIZE_MEMORY_CACHE'] = array();
    }
    
    $GLOBALS['MESMERIZE_MEMORY_CACHE'][$key] = $value;
}

function mesmerize_has_in_memory($key)
{
    
    if (isset($GLOBALS['MESMERIZE_MEMORY_CACHE']) && isset($GLOBALS['MESMERIZE_MEMORY_CACHE'][$key])) {
        return $key;
    } else {
        return false;
    }
}

function mesmerize_get_from_memory($key)
{
    if (mesmerize_has_in_memory($key)) {
        return $GLOBALS['MESMERIZE_MEMORY_CACHE'][$key];
    }
    
    return false;
}

function mesmerize_skip_customize_register()
{
    return isset($_REQUEST['mesmerize_skip_customize_register']);
}

function mesmerize_get_cache_option_key()
{
    return "__mesmerize_cached_values__";
}

function mesmerize_can_show_cached_value($slug)
{
    global $wp_customize;
    
    if ($wp_customize || wp_doing_ajax() || WP_DEBUG || mesmerize_is_wporg_preview()) {
        return false;
    }
    
    if ($value = mesmerize_get_from_memory("mesmerize_can_show_cached_value_{$slug}")) {
        return $value;
    }
    
    $result = (mesmerize_get_cached_value($slug) !== null);
    
    mesmerize_set_in_memory("mesmerize_can_show_cached_value_{$slug}", $result);
    
    return $result;
}

function mesmerize_cache_value($slug, $value, $cache_on_ajax = false)
{
    
    if (wp_doing_ajax()) {
        if ( ! $cache_on_ajax) {
            return;
        }
    }
    
    $cached_values = get_option(mesmerize_get_cache_option_key(), array());
    
    $cached_values[$slug] = $value;
    
    update_option(mesmerize_get_cache_option_key(), $cached_values, 'yes');
    
}

function mesmerize_remove_cached_value($slug)
{
    $cached_values = get_option(mesmerize_get_cache_option_key(), array());
    
    if (isset($cached_values[$slug])) {
        unset($cached_values[$slug]);
    }
    
    update_option(mesmerize_get_cache_option_key(), $cached_values, 'yes');
}

function mesmerize_get_cached_value($slug)
{
    $cached_values = get_option(mesmerize_get_cache_option_key(), array());
    
    if (isset($cached_values[$slug])) {
        return $cached_values[$slug];
    }
    
    return null;
}

function mesmerize_clear_cached_values()
{
    // cleanup old cached values
    $slugs = get_option('mesmerize_cached_values_slugs', array());
    
    if (count($slugs)) {
        foreach ($slugs as $slug) {
            mesmerize_remove_cached_value($slug);
        }
        
        delete_option('mesmerize_cached_values_slugs');
    }
    // cleanup old cached values
    
    delete_option(mesmerize_get_cache_option_key());
    
    if (class_exists('autoptimizeCache')) {
        autoptimizeCache::clearall();
    }
}