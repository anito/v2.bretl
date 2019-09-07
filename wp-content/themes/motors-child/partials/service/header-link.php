<?php 
    $service_header_label = get_theme_mod('service_header_label', esc_html__('Make an Appointment', 'motors'));
    $service_header_link  = get_theme_mod('service_header_link', '#appointment-form');
?>	

<div class="service-mobile-menu-trigger visible-sm visible-xs">
    <span></span>
    <span></span>
    <span></span>
</div>		

<?php if(!empty($service_header_label) and !empty($service_header_link)): ?>
    <a href="<?php echo esc_url($service_header_link); ?>" class="button_3d white service-header-appointment heading-font">
        <div class="default-state">
            <i class="stm-service-icon-appointment_calendar"></i><?php stm_dynamic_string_translation_e('Service Header Label', $service_header_label); ?>
            <span class="active-state">
                <i class="stm-service-icon-appointment_calendar"></i><?php stm_dynamic_string_translation_e('Service Header Label', $service_header_label); ?>
            </span>
        </div>
    </a>
<?php endif; ?>