<?php
defined( 'ABSPATH' ) || exit;
?>
<h2>Strumenti</h2>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
<div class="postbox" style="padding:16px;">
<h3>📋 Esporta impostazioni</h3>
<p>Copia le opzioni SAF in un file JSON per trasferirle su un altro sito.</p>
<button class="button" onclick="alert('Funzionalità in arrivo.')">Esporta</button>
</div>
<div class="postbox" style="padding:16px;">
<h3>📥 Importa impostazioni</h3>
<p>Carica un file JSON con le opzioni SAF da un altro sito.</p>
<button class="button" onclick="alert('Funzionalità in arrivo.')">Importa</button>
</div>
<div class="postbox" style="padding:16px;">
<h3>🔍 Diagnostica</h3>
<p>Verifica lo stato di tutti i moduli SAF e delle dipendenze.</p>
<a href="<?php echo esc_url( admin_url( 'admin.php?page=saf&tab=modules' ) ); ?>" class="button">Vai a Moduli</a>
</div>
<div class="postbox" style="padding:16px;">
<h3>🗑 Pulisci Cache</h3>
<p>Pulisce le cache di WordPress e dei plugin.</p>
<button class="button" onclick="alert('Funzionalità in arrivo.')">Pulisci Cache</button>
</div>
</div>
