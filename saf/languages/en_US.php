<?php
/**
 * SAF English strings.
 * @package SAF
 */

return array(
    /* Tabs */
    'tab_org'      => '🏢 Organization',
    'tab_seo'      => '🔍 SEO & Images',
    'tab_security' => '🔒 Security',
    'tab_robots'   => '🤖 Robots.txt',
    'tab_nap'       => '📍 NAP Footer',
    'tab_shortcode' => '📋 Shortcodes',
    'tab_advanced'  => '⚙️ Advanced',
    'tab_child'    => '🎨 Child Theme',
    'tab_credits'  => '📝 Credits',
    'tab_sistema'  => '🖥 System',
    'tab_plugins'  => '🛠 Tools',
    'sistema_title' => '🖥 System Information',
    'sistema_desc'  => 'Full overview of server environment, PHP, WordPress and SAF. Visible to administrators only.',

    'page_title'   => 'Site Data',
    'site_data'    => 'Site Data',
    'menu_page_title' => 'Site Data',
    'menu_title'    => '⚙️ Site Data',

    /* Org tab */
    'org_title'    => '🏢 Organization Data',
    'org_desc'     => 'Feeds the JSON-LD <code>Organization</code> on homepage and the <code>[saf_footer_info]</code> shortcode.',
    'org_name'     => 'Organization name',
    'org_url'      => 'Website URL',
    'org_logo'     => 'Logo URL',
    'org_logo_btn' => 'Choose Image',
    'org_address'  => 'Address',
    'org_zip'      => 'ZIP / Postal Code',
    'org_city'     => 'City',
    'org_vat'      => 'VAT / Tax ID',
    'org_email'    => 'Email',
    'org_phone'    => 'Phone',
    'org_url_desc'  => 'Default: current WordPress URL.',
    'org_logo_desc' => 'Ideal: 512×512px, transparent PNG. Used in JSON-LD and login page.',
    'org_vat_desc' => 'Not included in public JSON-LD &mdash; internal reference and <code>[saf_footer_info]</code>.',
    'org_social'   => 'Social Profiles',
    'org_social_desc' => 'Enter full URLs (e.g. https://facebook.com/page). Used as sameAs in JSON-LD Organization.',

    /* SEO tab */
    'seo_title'    => '🔍 SEO & Default Images',
    'seo_desc'     => 'Default images for Open Graph and social sharing.',
    'seo_img1'     => 'Default OG Image',
    'seo_img2'     => 'Secondary OG Image',

    /* Security tab */
    'sec_title'    => '🔒 Security Settings',
    'sec_access'   => 'Backend access via standard <code>/wp-login.php</code>.',
    'sec_login_brand' => 'The login page is branded with the site logo and colors.',
    'sec_max_attempts' => 'Max login attempts',
    'sec_max_desc' => 'Between 3 and 20. After exceeding this limit, the IP is blocked for 15 minutes.',
    'sec_active'   => 'Active measures',
    'sec_list_rate' => '✅ Rate limiting login (max attempts per IP)',
    'sec_list_xmlrpc' => '✅ XML-RPC disabled',
    'sec_list_enum' => '✅ User enumeration blocked (?author=N &rarr; 301)',
    'sec_list_rest' => '✅ REST /wp/v2/users blocked for guests',
    'sec_list_headers' => '✅ HTTP security headers (X-Frame, X-Content-Type…)',
    'sec_list_wp_version' => '✅ WordPress version hidden',
    'sec_list_login_errors' => '✅ Generic login error messages',
    'sec_list_file_edit' => '✅ Backend file editor disabled',
    'sec_list_spam' => '✅ REST comment spam blocked',
    'sec_list_login_brand' => '✅ Branded login page',

    /* Robots tab */
    'robots_title' => '🤖 Robots.txt Editor',
    'robots_txt_label' => 'Robots.txt content',
    'robots_warn_seo' => '⚠️ <strong>Rank Math</strong> or <strong>Yoast SEO</strong> is active.<br>SAF does not manage robots.txt &mdash; let the SEO plugin handle it. The content is saved but not visible on <code>%s</code> while Rank Math/Yoast is active.<br>Use the <strong>Export to root</strong> button below to create a physical file.',
    'robots_warn_info' => 'ℹ️ WordPress serves this content on <code>%s</code>.<br><strong>{{SITE_URL}}</strong> is automatically replaced with your site URL.<br>⚠️ If a physical robots.txt file exists, this editor has no effect &mdash; remove the physical file first.',
    'robots_view_live' => '↗ View live robots.txt',
    'robots_export_title' => '📁 Export physical file',
    'robots_export_desc' => 'If you use CloudFlare, CDN or aggressive caching, WordPress may not serve the virtual robots.txt. Create a real robots.txt file in the site root.',
    'robots_export_btn' => '📁 Export robots.txt to root',
    'robots_export_empty' => 'No robots.txt content to export. Save some content in the Robots.txt tab first.',
    'robots_export_ok' => '✔ robots.txt file created at <code>%s</code>',
    'robots_export_err' => '❌ Could not write the file. Check write permissions on the site root.',

    /* NAP tab */
    'nap_title'    => '📍 NAP Footer',
    'nap_desc'     => 'Custom HTML for the NAP footer (Name, Address, Phone). Shortcode: <code>[saf_nap_html]</code>.',
    'nap_content_label' => 'NAP Footer HTML',
    'nap_preview'  => 'Live preview:',
    'nap_show_preview' => 'show preview',
    'nap_no_content' => 'no content yet',
    'nap_shortcode' => 'Use in theme: <code>[saf_nap_html]</code>.',

    /* Avanzate tab */
    'adv_title'    => '⚙️ Advanced Settings',
    'adv_email'    => '📧 Email &mdash; Sender',
    'adv_email_desc' => 'Set sender name and address for all WP emails. For SMTP host/port/auth use <strong>WP Mail SMTP</strong>.',
    'adv_from_name' => 'Sender name',
    'adv_from_name_ph' => 'Site Name',
    'adv_from_name_desc' => 'Default: organization name from Site Data, then WordPress name.',
    'adv_from_email' => 'Sender email',
    'adv_from_email_ph' => 'noreply@domain.com',
    'adv_from_email_desc' => 'Default: WordPress uses wordpress@domain.com &mdash; often ends up in spam.',
    'adv_comments' => '💬 Comments',
    'adv_comments_label' => 'Disable comments',
    'adv_comments_label_check' => 'Disable comments site-wide',
    'adv_comments_desc' => 'Removes comments, trackbacks, comment menu, columns and admin bar. Recommended for sites not using comments.',
    'adv_hsts'     => '🔒 HTTP Strict Transport Security (HSTS)',
    'adv_hsts_label' => 'Enable HSTS',
    'adv_hsts_label_check' => 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload',
    'adv_hsts_warn' => '⚠️ Enable ONLY with stable SSL + Cloudflare Full (Strict). Browser will force HTTPS for one year.',
    'adv_guide_hide' => 'Guide Section',
    'adv_guide_hide_check' => 'Hide the Project tab from the Site Guide',
    'adv_guide_hide_desc' => 'Hides the Project tab &mdash; useful after delivery to keep internal documents from clients.',
    'adv_menu'     => '🗂 Admin Menu Cleanup',
    'adv_menu_desc' => 'Hide selected admin menu items for non-admin roles. Useful to simplify the backend for clients.',
    'adv_menu_hide_for' => 'Hide "%s" for Editor, Author, Contributor',
    'adv_custom_hide' => 'Additional slugs',
    'adv_custom_ph' => 'edit.php?post_type=projects',
    'adv_custom_desc' => 'Menu slugs to hide (one per line). Useful for CPT from plugins.<br>Examples: edit.php?post_type=projects &middot; edit.php?post_type=portfolio &middot; tools.php',

    /* SVG Upload */
    'adv_svg'      => 'SVG Upload',
    'adv_svg_label' => 'Enable SVG Upload',
    'adv_svg_check' => 'Enable SVG file upload in Media Library',
    'adv_svg_warn'  => '⚠️ SVG files can contain scripts. SAF automatically strips scripts, event handlers (onclick, onload...) and javascript: href. For maximum security install <code>enshrined/svg-sanitize</code>.',

    /* Menu item labels for admin cleanup */
    'menu_tools'    => 'Tools',
    'menu_comments' => 'Comments',
    'menu_themes'   => 'Appearance / Themes',
    'menu_plugins'  => 'Plugins',
    'menu_users'    => 'Users',
    'menu_settings' => 'Settings',
    'menu_projects' => 'Projects (Divi)',

    /* Child tab */
    'child_title'  => '🎨 Child Theme <code>amar-design</code>',
    'child_create_btn' => 'Create child theme amar-design',
    'child_exists'  => 'Child theme found in <code>%s</code>. Your <code>screenshot.png</code> is kept as-is.',
    'child_css_warn' => '<strong>Important:</strong> the <code>Template:</code> line must match your parent theme folder name.',
    'child_save_css' => 'Save style.css',
    'child_css_header' => 'style.css Header',
    'child_css_header_desc' => 'Each field has a specific function for WordPress. The <strong>Template</strong> field is required.',
    'child_css_shortcuts' => 'Use Customize &rarr; Additional CSS for quick rules. Here only structural overrides of the parent theme.',
    'child_save_divi'  => 'Save Divi settings',

    /* Credits tab */
    'credits_title' => '📝 Credits',
    'credits_desc'  => 'This data appears in the Dashboard widget, admin footer and other areas. Useful to leave your mark on sites you develop.',
    'credits_author' => '👤 Author / Developer',
    'credits_author_name' => 'Author name',
    'credits_author_url' => 'Author website',
    'credits_author_url_ph' => 'yoursite.com',
    'credits_client' => '🤝 Client',
    'credits_client_name' => 'Client name',
    'credits_client_url' => 'Client website',
    'credits_client_url_ph' => 'theirsite.com',
    'credits_notes_title' => '📓 Development notes',
    'credits_notes' => 'Notes',
    'credits_notes_desc' => 'Visible only in the backend Site Data &rarr; Credits.',
    'credits_created' => 'Creation date',
    'credits_created_ph' => 'e.g. June 2026',
    'credits_save'   => 'Save Credits',

    /* Buttons */
    'btn_save_org'  => 'Save Organization',
    'btn_save_seo'  => 'Save SEO & Images',
    'btn_save_sec'  => 'Save Security',
    'btn_save_robots' => 'Save Robots.txt',
    'btn_save_nap'  => 'Save NAP Footer',
    'btn_save_adv'  => 'Save Advanced Settings',
    'btn_cleanup'      => 'Delete all SAF options',
    'btn_cleanup_desc' => 'Deletes all <code>saf_*</code> options from the database. Useful for clean uninstall. Data is not recoverable.',
    'btn_save_tools' => 'Save tools checklist',

    /* Dashboard */
    'dash_title'     => '🌐 Site Overview',
    'dash_checklist' => '📋 Checklist progress',
    'checklist_progress' => '%1$d/%2$d done (%3$d%%)',
    'dash_btn_dati'  => 'Site Data',
    'dash_btn_guida' => 'Guide',
    'dash_credits'   => 'Developed by',
    'dash_for'       => 'for',
    'dash_organization' => 'Organization',
    'dash_site'    => 'Site',

    /* Admin bar */
    'adminbar_frontend' => 'View Front-End',
    'adminbar_frontend_title' => 'Open site in a new tab',

    'footer_dev_by'  => 'Developed by',
    'url_placeholder_domain' => 'domain.com',

    /* Login */
    'login_welcome_title' => 'Sign In',
    'login_welcome_desc' => 'Enter your credentials to sign in.',

    /* Errors & feedback */
    'err_permission' => 'Permission denied.',
    'err_saved'                => '✔ Saved.',
    'err_too_many_attempts'    => 'Too many login attempts. Please wait 15 minutes and try again.',
    'err_invalid_credentials'  => 'Invalid credentials. Please try again.',
    'err_cleaned'     => '✔ All SAF options removed from database.',
    'err_css_updated' => '✔ style.css updated.',
    'err_css_write_fail' => '❌ Could not write %s. Check permissions.',
    'err_child_created' => '✔ Child theme <strong>amar-design</strong> created.',
    'err_ajax_unauthorized' => 'Unauthorized request.',
    'child_activate_warn' => 'Remember to activate the child theme from <strong>Appearance → Themes</strong> after creating it and configuring the parameters (Template, Name, Author) in the section below.',
    'child_detected'     => 'SAF detected an active child theme and will manage it automatically. You can edit style.css and view functions.php below.',
    'child_functions_title' => 'functions.php',
    'child_functions_desc'  => 'Read-only — use an FTP editor or the plugin editor to modify it.',

    /* Plugin & Tools tab */
    'plugins_title' => 'Plugins & Tools',
    'plugins_desc'  => 'Detection and checklist of site plugins and tools.',

    /* Shortcode tab */
    'sc_social_share_title' => '📤 Social Sharing',
    'sc_social_share_desc'  => 'Choose which buttons to display in the <code>[condividi_social]</code> shortcode. Instagram and TikTok copy the link to clipboard (no native share URL).',
    'sc_shortcode_usage'    => 'Use the shortcode:',
    'sc_active_buttons'     => 'Active buttons',
    'sc_copy_note'          => '(copies link)',
    'sc_copy_label'         => 'Copy link',
    'sc_default_note'       => 'If no boxes are saved, all buttons are visible (default behaviour).',


    /* Shortcode — Developer section */
    'sc_social_section' => 'Social',
    'sc_dev_section'    => 'Developer',
    'sc_dev_desc'       => 'Developer profiles. Enable with <code>[condividi_social type="dev"]</code>.',
    'sc_dev_profiles'   => 'Dev Profiles',
    'sc_dev_url_note'   => 'Enter the full profile URL (e.g. https://github.com/your-username).',
);
