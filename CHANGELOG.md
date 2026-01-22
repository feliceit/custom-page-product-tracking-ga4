# Changelog - Custom Page Product Tracking for GA4

## [1.1.0] - 2026-01-22

### 🎨 Rebranding & Improvements

**New Name**: Custom Page Product Tracking for GA4 (previously GTM4WP Elementor Extension)

#### ✨ What Changed
- **Plugin renamed** to better reflect its universal compatibility
- **No longer limited to Elementor** - works with ANY page builder
- **Updated all text strings** to English for wider adoption
- **Simplified class names** and internal structure
- **New text domain**: `custom-page-product-tracking`

#### 🎯 Universal Page Builder Support
Now officially supports:
- ✅ Elementor
- ✅ Divi
- ✅ Beaver Builder
- ✅ Oxygen
- ✅ Bricks
- ✅ WPBakery
- ✅ Gutenberg
- ✅ Custom HTML/CSS
- ✅ **Any page builder** with button/link elements

#### 📋 Requirements
- Button or link (`<button>` or `<a>`) with configured CSS class
- GTM4WP installed and active
- WooCommerce installed and active

#### 🔄 Migration from 1.0.x
No data loss - the plugin will:
- ✅ Keep all configured products
- ✅ Keep all button class settings
- ⚠️ You may need to re-save settings once after updating

#### 🆕 What's New
- Metabox title: "GA4 Product Tracking Configuration"
- Settings page: Settings → Product Tracking GA4
- Improved admin UI with better descriptions
- Added page builder compatibility info in metabox
- English interface for international users

---

## [1.0.2] - 2026-01-22

### 🎯 Changed
- **Plugin ora gestisce SOLO il tracking dataLayer**
  - Rimossa completamente la logica AJAX add to cart
  - Rimossi gli handler `wp_ajax_gtm4wp_elem_add_to_cart`
  - Il plugin NON interferisce più con il comportamento originale dei bottoni
  - I bottoni mantengono il loro comportamento nativo (AJAX, redirect, form submit, ecc.)

### ✅ Behavior
Il plugin ora si limita a:
1. ✅ Tracciare evento `view_item` al caricamento pagina
2. ✅ Tracciare evento `add_to_cart` al click del bottone
3. ❌ NON aggiunge prodotti al carrello (gestito dal bottone originale)
4. ❌ NON modifica il comportamento dei bottoni

### 📝 Author Info Updated
- Author: Taglyzer
- URI: https://www.taglyzer.com

---

## [1.0.1] - 2026-01-22

### 🔧 Fixed
- **Formato GA4 corretto per evento add_to_cart**
  - Cambiato da `gtm4wp.addProductToCartEEC` a `add_to_cart` (standard GA4)
  - Modificata struttura da `ecommerce.add.products` a `ecommerce.items` (formato GA4)
  - Aggiunto parametro `value` con calcolo prezzo * quantità
  - Aggiunto parametro `currency` a livello ecommerce
  - Implementato `ecommerce: null` clear prima del push (best practice GA4)

- **Formato GA4 corretto per evento view_item**
  - Cambiato da struttura `ecommerce.detail.products` a `ecommerce.items`
  - Aggiunto evento `view_item` esplicito nel dataLayer
  - Aggiunto parametro `value` con prezzo prodotto
  - Modificato `pagePostType` da `productdetail` a `product` (più generico)

- **Comportamento bottone mantenuto**
  - Rimosso preventDefault automatico che bloccava il redirect
  - Aggiunto controllo per verificare se il bottone ha un href valido
  - Se il bottone è un link con href, segue il link naturalmente
  - AJAX add to cart eseguito solo per bottoni senza href valido

### 📊 Struttura Dati GA4

#### View Item Event (Pageload)
```javascript
{
  event: "view_item",
  ecommerce: {
    currency: "EUR",
    value: 29.99,
    items: [{
      item_id: "SKU_123",
      item_name: "Nome Prodotto",
      price: 29.99,
      item_category: "Categoria",
      item_brand: "Brand",
      item_variant: "Variante"
    }]
  },
  pagePostType: "product"
}
```

#### Add to Cart Event (Click)
```javascript
// Clear previous ecommerce
dataLayer.push({ ecommerce: null });

// Push new event
dataLayer.push({
  event: "add_to_cart",
  ecommerce: {
    currency: "EUR",
    value: 29.99,
    items: [{
      item_id: "SKU_123",
      item_name: "Nome Prodotto",
      price: 29.99,
      quantity: 1,
      item_category: "Categoria",
      item_brand: "Brand",
      item_variant: "Variante"
    }]
  }
});
```

### ✅ Compatibilità
- ✅ GA4 standard format
- ✅ Google Analytics 4 ecommerce reports
- ✅ Google Tag Manager native GA4 tags
- ✅ Google Ads enhanced conversions
- ✅ Facebook Conversion API (con items array)

### 🎯 Breaking Changes
**Nessuno** - Il plugin è retrocompatibile, ma i trigger GTM potrebbero dover essere aggiornati:

#### Vecchi Trigger da Aggiornare:
- ❌ `pagePostType = 'productdetail'` → ✅ `pagePostType = 'product'` o usa direttamente `event = 'view_item'`
- ❌ Evento personalizzato `gtm4wp.addProductToCartEEC` → ✅ Evento `add_to_cart`

#### Nuove Variabili GTM Consigliate:
- `DLV - Ecommerce Items` → `ecommerce.items`
- `DLV - Ecommerce Value` → `ecommerce.value`
- `DLV - Ecommerce Currency` → `ecommerce.currency`

---

## [1.0.0] - 2026-01-22

### 🎉 Initial Release

#### Features
- Metabox per configurazione prodotto WooCommerce su pagine Elementor
- Tracking automatico evento view_item al caricamento pagina
- Tracking evento add_to_cart al click su bottoni personalizzati
- AJAX add to cart per aggiunta reale al carrello
- Pagina impostazioni per configurazione globale
- Supporto multiple post types
- Compatibilità con filtri GTM4WP standard
- Debug console log per troubleshooting

#### Documentazione
- README completo con guide utilizzo
- GTM-CONFIGURATION con setup dettagliato Tag Manager
- CUSTOMIZATIONS con esempi codice avanzati
- QUICK-START per setup rapido in 15 minuti

---

## Migrazione da 1.0.0 a 1.0.1

### Passi da Seguire:

1. **Aggiorna il plugin**
   - Disattiva versione 1.0.0
   - Installa versione 1.0.1
   - Riattiva il plugin

2. **Aggiorna Trigger GTM**
   ```
   Vecchio trigger view_item:
   - Tipo: Pageview
   - Condizione: pagePostType = 'productdetail'
   
   Nuovo trigger view_item:
   - Tipo: Evento Personalizzato
   - Nome evento: view_item
   O mantieni pageview con: pagePostType = 'product'
   ```

3. **Aggiorna Trigger Add to Cart**
   ```
   Vecchio trigger:
   - Evento personalizzato: gtm4wp.addProductToCartEEC
   
   Nuovo trigger:
   - Evento personalizzato: add_to_cart
   ```

4. **Aggiorna Tag GA4**
   - Non serve cambiare nulla nei tag GA4 Event
   - I parametri `items`, `value`, `currency` rimangono gli stessi
   - GA4 riconoscerà automaticamente gli eventi `view_item` e `add_to_cart`

5. **Test**
   - Attiva GTM Preview Mode
   - Verifica eventi in console browser
   - Controlla GA4 DebugView
   - Verifica che i dati appaiano correttamente

### Rollback (se necessario)

Se riscontri problemi, puoi tornare alla versione 1.0.0:
1. Disinstalla versione 1.0.1
2. Reinstalla versione 1.0.0
3. I tuoi trigger GTM precedenti continueranno a funzionare

---

## Supporto

Per problemi o domande sulla migrazione:
1. Controlla la documentazione aggiornata in GTM-CONFIGURATION.md
2. Verifica gli esempi in CUSTOMIZATIONS.md
3. Usa la console browser per debug (F12)
4. Contatta il supporto tecnico Loop Agency

---

**Note**: La versione 1.0.1 è allineata agli standard GA4 ufficiali di Google e segue le best practices di ecommerce tracking 2025.
