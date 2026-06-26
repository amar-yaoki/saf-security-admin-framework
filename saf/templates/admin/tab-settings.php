<?php
/* ===== Tab: Impostazioni ===== */
defined( 'ABSPATH' ) || exit;
$options = get_option( 'saf_adv_settings', [] );
$saved   = isset( $_GET['updated'] ) && $_GET['updated'] === '1';
if ( $saved ) echo '<div class="notice notice-success is-dismissible"><p>Impostazioni salvate.</p></div>';
?>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
    <?php wp_nonce_field( 'saf_save_settings', 'saf_nonce' ); ?>
    <input type="hidden" name="action" value="saf_save_settings">
    <table class="form-table">
        <tr>
            <th scope="row">Modulo SEO</th>
            <td>
                <label>
                    <input type="checkbox" name="saf_adv_settings[enable_seo]" value="1" <?php checked( ! empty( $options['enable_seo'] ) ); ?>>
                    Abilita meta description automatica e supporto excerpt
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">Caricamento SVG</th>
            <td>
                <label>
                    <input type="checkbox" name="saf_adv_settings[enable_svg]" value="1" <?php checked( ! empty( $options['enable_svg'] ) ); ?>>
                    Abilita caricamento file SVG nella Libreria Media
                </label>
                <p class="description">I file SVG vengono sanitizzati automaticamente all'upload.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">Performance — CSS differito</th>
            <td>
                <label>
                    <input type="checkbox" name="saf_adv_settings[perf_defer_css]" value="1" <?php checked( ! empty( $options['perf_defer_css'] ) ); ?>>
                    Carica i CSS in modo differito (preload → stylesheet)
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">Performance — JS differito</th>
            <td>
                <label>
                    <input type="checkbox" name="saf_adv_settings[perf_defer_js]" value="1" <?php checked( ! empty( $options['perf_defer_js'] ) ); ?>>
                    Aggiungi attributo defer agli script non critici
                </label>
            </td>
        </tr>
    </table>
    <?php submit_button( 'Salva Impostazioni' ); ?>
</form>
