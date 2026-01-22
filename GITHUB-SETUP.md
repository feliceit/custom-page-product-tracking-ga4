# 🚀 Guida Creazione Repository GitHub

## Passo 1: Crea Repository su GitHub

1. **Vai su GitHub**
   - https://github.com/new

2. **Compila il form:**
   ```
   Repository name: custom-page-product-tracking-ga4
   Description: Track WooCommerce products on custom landing pages with GA4 events. Works with any page builder.
   
   ☑️ Public
   ☐ Add a README file (già presente)
   ☐ Add .gitignore (già presente)
   ☐ Choose a license (già presente - GPL v2)
   ```

3. **Clicca "Create repository"**

## Passo 2: Push del Codice

### Opzione A: Da Terminale

```bash
# Vai nella cartella del progetto
cd /path/to/github-repo

# Inizializza git
git init

# Aggiungi remote
git remote add origin https://github.com/felicegattuso/custom-page-product-tracking-ga4.git

# Aggiungi tutti i file
git add .

# First commit
git commit -m "Initial commit - Custom Page Product Tracking for GA4 v1.1.0"

# Push to main
git branch -M main
git push -u origin main
```

### Opzione B: Da GitHub Desktop

1. Apri GitHub Desktop
2. File → Add Local Repository
3. Seleziona la cartella `github-repo`
4. Clicca "Publish repository"
5. Seleziona account e conferma

### Opzione C: Upload via Web (più semplice)

1. Vai alla repository appena creata su GitHub
2. Clicca "uploading an existing file"
3. Drag & drop tutti i file dalla cartella `github-repo`
4. Commit message: "Initial commit - v1.1.0"
5. Clicca "Commit changes"

## Passo 3: Crea il First Release

1. **Vai su Releases**
   - https://github.com/felicegattuso/custom-page-product-tracking-ga4/releases

2. **Clicca "Create a new release"**

3. **Compila:**
   ```
   Tag version: v1.1.0
   Release title: v1.1.0 - Initial Release
   
   Description:
   ```
   
   ### 🎉 First Release - Custom Page Product Tracking for GA4
   
   Track WooCommerce products on custom landing pages with GA4 events.
   
   #### ✨ Features
   - GA4 standard event format (view_item, add_to_cart)
   - Universal page builder compatibility
   - Non-invasive tracking (dataLayer only)
   - GTM4WP integration
   
   #### 📦 Installation
   1. Download the ZIP file below
   2. Upload to WordPress → Plugins → Add New
   3. Activate and configure
   
   #### 📖 Documentation
   See [README](https://github.com/felicegattuso/custom-page-product-tracking-ga4#readme)
   
   #### 🎯 Requirements
   - WordPress 5.8+
   - WooCommerce
   - GTM4WP
   ```

4. **Upload ZIP**
   - Attach il file `custom-page-product-tracking-ga4.zip`

5. **Clicca "Publish release"**

## Passo 4: Configura Repository

### Topics (Tags)

Aggiungi questi topics alla repository:

```
wordpress
wordpress-plugin
woocommerce
google-analytics
ga4
google-tag-manager
gtm
tracking
ecommerce
elementor
divi
page-builder
analytics
```

**Come aggiungere:**
1. Vai sulla homepage della repo
2. Clicca su ⚙️ accanto a "About"
3. Aggiungi topics
4. Salva

### Description

```
Track WooCommerce products on custom landing pages with GA4 events. Works with any page builder (Elementor, Divi, Beaver Builder, Oxygen, etc). Requires GTM4WP.
```

### Website

```
https://www.taglyzer.com
```

## Passo 5: Aggiungi Badge al README (Opzionale)

Nel README.md, aggiorna i badge con i link corretti:

```markdown
[![GitHub release](https://img.shields.io/github/v/release/felicegattuso/custom-page-product-tracking-ga4)](https://github.com/felicegattuso/custom-page-product-tracking-ga4/releases)
[![GitHub downloads](https://img.shields.io/github/downloads/felicegattuso/custom-page-product-tracking-ga4/total)](https://github.com/felicegattuso/custom-page-product-tracking-ga4/releases)
[![GitHub issues](https://img.shields.io/github/issues/felicegattuso/custom-page-product-tracking-ga4)](https://github.com/felicegattuso/custom-page-product-tracking-ga4/issues)
```

## Passo 6: GitHub Actions (Opzionale)

Per automatizzare i test, crea `.github/workflows/test.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'
        
    - name: Check PHP syntax
      run: find . -name "*.php" -exec php -l {} \;
```

## ✅ Checklist Finale

- [ ] Repository creata su GitHub
- [ ] Codice pushato su main
- [ ] Release v1.1.0 creata
- [ ] ZIP caricato nella release
- [ ] Topics aggiunti
- [ ] Description impostata
- [ ] README verificato
- [ ] LICENSE presente
- [ ] .gitignore configurato

## 🔗 Link Utili

**Dopo la creazione:**

- Repository: https://github.com/felicegattuso/custom-page-product-tracking-ga4
- Releases: https://github.com/felicegattuso/custom-page-product-tracking-ga4/releases
- Issues: https://github.com/felicegattuso/custom-page-product-tracking-ga4/issues
- Clone URL: `git clone https://github.com/felicegattuso/custom-page-product-tracking-ga4.git`

## 📢 Dopo la Pubblicazione

1. **Annuncia su social**
   - Twitter
   - LinkedIn
   - Dev.to

2. **Aggiungi al profilo GitHub**
   - Pin repository nel profilo

3. **Aggiorna WordPress.org**
   - Nel form di submission, aggiungi link GitHub

4. **Crea issue templates**
   - Bug report
   - Feature request

---

Fatto! 🎉 La tua repository è pronta e professionale!
