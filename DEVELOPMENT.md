# 📧 CleAuto Mailer - Guide de Développement

## 📋 Vue d'ensemble

**CleAuto Mailer** est un microservice Symfony 8.0 conçu pour traiter et envoyer des e-mails de demandes d'intervention automobile. L'application reçoit des données de formulaire avec des pièces jointes, valide les informations (données personnelles, état du véhicule, demandes de service), et envoie un e-mail HTML formaté avec les pièces jointes au service backend.

### Objectif principal
- Recevoir des demandes de service automobile via API REST
- Valider et enrichir les données (IP, date, compteur de fichiers)
- Générer un e-mail HTML formaté avec les informations structurées
- Envoyer l'e-mail avec toutes les pièces jointes (photos clé + photos voiture)
- Fournir un aperçu de l'e-mail avant envoi

## 🛠️ Stack Technique

| Composant | Version | Usage |
|-----------|---------|-------|
| **PHP** | 8.4+ | Runtime |
| **Symfony** | 8.0.* | Framework web |
| **Twig** | 3.23+ | Moteur de templates |
| **Symfony Mailer** | 8.0.* | Service d'envoi e-mail |
| **Docker** | Latest | Containerization |
| **Docker Compose** | Latest | Orchestration locale |

### Dépendances principales (dans composer.json)
- `symfony/framework-bundle` - Core framework
- `symfony/mailer` - Gestion des e-mails
- `symfony/console` - Commandes CLI
- `symfony/twig-bundle` - Templating
- `twig/twig` - Template engine

## 🏗️ Architecture et Structure du Projet

### Structure des répertoires
```
cleauto-mailer/
├── src/                          # Code applicatif
│   ├── Controller/
│   │   └── MailerController.php  # Routes API (3 endpoints)
│   ├── Kernel.php               # Configuration du kernel Symfony
│   └── Twig/
│       └── Extension/
│           └── EmojiExtension.php # Filtre Twig personnalisé
├── config/                        # Configuration Symfony
│   ├── bundles.php              # Bundles chargés
│   ├── services.yaml            # Injection de dépendances
│   ├── routes.yaml              # Configuration du routing
│   ├── packages/                # Configuration par package
│   └── routes/
├── templates/                     # Templates Twig
│   ├── base.html.twig           # Template de base
│   └── mailer/
│       └── car-request.html.twig # Email de demande de service
├── public/                        # Racine web
│   ├── index.php               # Entrypoint de l'appli
│   └── {assets du formulaire frontend}
├── bin/
│   └── console                  # CLI Symfony
├── Dockerfile                     # Configuration Docker
├── docker-compose.yml            # Configuration Docker Compose
├── Makefile                       # Tâches de build/déploiement
├── Makefile.docker              # Tâches Docker spécifiques
└── composer.json                 # Dépendances PHP

```

### Points architecturaux clés
- **Micro-kernel Symfony** : Utilise `MicroKernelTrait` pour une configuration minimale
- **Auto-wiring** : Services autowired automatiquement (voir services.yaml)
- **Injection de dépendances** : Paramètres liés depuis variables d'environnement
- **Attribute Routing** : Utilise les attributs PHP pour définir les routes

## 🔌 API - Endpoints

### 1. GET `/` (Page d'accueil)
**Route** : `app_mailer_get`

Affiche la page d'accueil avec un formulaire frontend.

**Réponse** : Template `app/index.html.twig`

**Détails** :
- Charge le template principal du formulaire
- Les assets du formulaire frontend sont dupliqués depuis l'image Docker du formulaire (https://github.com/Deuky/cleauto-form)

---

### 2. POST `/mailer` (Envoi d'e-mail)
**Route** : `app_mailer_post`

Reçoit les données du formulaire, enrichit les données, génère et envoie l'e-mail.

**Paramètres injectés** :
- `$mailer: MailerInterface` - Service d'envoi e-mail
- `$requestFrom: string` - Email expéditeur (du .env)
- `$requestTo: string` - Email destinataire (du .env)

**Format des données envoyées** (JSON + files multipart):
```json
{
  "personal": {
    "name": "Jean Dupont",
    "phone": "0612345678",
    "email": "jean@example.com"
  },
  "car": {
    "brand": "Peugeot",
    "model": "3008",
    "fuel": "diesel",
    "VIN": "VF3UZZZZ123456789",
    "first-registration": "2020-01-15",
    "address": "123 Rue de Paris, 75001 Paris",
    "attachments": [files]
  },
  "key": {
    "is-hand-free": true,
    "attachments": [files]
  },
  "request": {
    "repair-key": true,
    "copy-key": false,
    "command-works": true,
    "all-key-lost": false,
    "car-opened": false
  },
  "extra": {
    "informations": "Urgence, merci de m'appeler au 0612345678"
  },
  "agreement": {
    "rgpd": {
      "status": true,
      "content": "J'accepte le traitement de mes données...",
      "request-date": "2025-02-18T10:30:00Z",
      "url": "https://example.com"
    }
  }
}
```

**Fichiers attendus** :
- `key[attachments][]` - D'une ou plusieurs photos de clé
- `car[attachments][]` - D'une ou plusieurs photos de voiture

**Processus** :
1. Récupère le JSON du payload
2. Enrichit les données :
   - `agreement.rgpd.ip` = IP du client
   - `agreement.rgpd.count-uploaded-files` = Compteur (0 au départ, incrémenté sur le nombre de fichier uploadé dans la requête)
   - `agreement.rgpd.request-trait-date` = Date/heure du traitement (W3C format)
3. Récupère les fichiers des clefs `key` et `car`
4. Crée un objet `Email` avec sujet "CleAuto - Demande d'intervention"
5. Ajoute les fichiers comme pièces jointes (avec noms : `item.0.jpg`, `item.1.png`, etc.)
6. Ajoute les données JSON enrichies comme pièce jointe `raw-data.json`
7. Génère l'HTML depuis le template avec les données
8. Envoie l'e-mail
9. Retourne `["Accept"]`

**Réponse** : `{"0":"Accept"}` (JSON)

---

### 3. POST `/mailer/preview` (Aperçu de l'e-mail)
**Route** : `app_mailer_preview`

Identique à `/mailer` mais retourne l'HTML de l'e-mail au lieu de l'envoyer.

**Utilisation** : Permet au client de valider le contenu de l'e-mail avant mise en production

**Réponse** : HTML de l'e-mail (Content-Type: text/html)

---

## 📧 Template d'Email

**Fichier** : [templates/mailer/car-request.html.twig](templates/mailer/car-request.html.twig)

Structure : Tableau HTML simple avec sections :
1. **Information personnelle** : Nom, téléphone, e-mail
2. **Voiture** : Marque, modèle, carburant, VIN, immatriculation, adresse, nombre de photos
3. **Clé** : Main-libre (oui/non), nombre de photos
4. **Demande** : Type de service demandé (5 options boléennes)
5. **Info supplémentaire** : Notes libres
6. **RGPD** : Consentement, contenu, dates, URL, IP, nombre de documents

### Filtres Twig utilisés
- `|default()` - Valeur par défaut si vide
- `|default()|emoji` - Convertit true/false en emojis ✅/❌ (voir EmojiExtension)
- `|length` - Nombre de fichiers

---

## 🎨 Extensions Twig Personnalisées

### EmojiExtension
**Fichier** : [src/Twig/Extension/EmojiExtension.php](src/Twig/Extension/EmojiExtension.php)

**Filtre** : `emoji`

```twig
{{ value|emoji }}
```

**Mapping** :
- `'true'` → `✅`
- `'false'` → `❌`
- autre → valeur inchangée

**Usage** : Afficher visuellement les booléens dans l'e-mail HTML

---

## ⚙️ Configuration Symfony

### services.yaml
```yaml
parameters:
  request.from: '%env(string:REQUEST_FROM)%'    # Email expéditeur
  request.to: '%env(string:REQUEST_TO)%'         # Email destinataire

services:
  _defaults:
    autowire: true      # Auto-injection des dépendances
    autoconfigure: true # Enregistrement automatique
    bind:
      $requestFrom: '%request.from%'    # Injecte partout
      $requestTo: '%request.to%'        # dans les constructeurs
```

**Variables d'environnement requises** :
- `REQUEST_FROM` - Email expéditeur (ex: contact@cleauto.be)
- `REQUEST_TO` - Email destinataire (ex: support@cleauto.be)

---

## 🐳 Docker et Docker Compose

### Dockerfile (Multi-stage)
- **Stage `base`** : Image PHP 8.5-FPM
- **Stage `build`** : Installation de dépendances, création du projet Skeleton Symfony 8.0
- **Stage `artifact`** : Image vide (scratch) pour export des artifacts
- **Stage `unit`** : Image complète de l'application

**Commande de démarrage** : `php -S 0.0.0.0:3000 -t ./public`

**Port** : 3000 exposé, mappé sur 3002 en local

### docker-compose.yml
```yaml
services:
  cleauto-mailer:
    image: deuky/cleauto-mailer
    build:
      target: ${BUILD_TARGET:-base}  # Cible de build (base par défaut)
    ports:
      - 3002:3000                    # Port local : port conteneur
```

**Variable d'environnement** :
- `BUILD_TARGET` - Stage Docker pour la cible de build (défaut: `base`)

---

## 🚀 Développement Local

### Prérequis
- Docker & Docker Compose
- Makefile (pour les tâches)
- Variables d'environnement `.env`

### Fichier .env requis
```bash
REQUEST_FROM=contact@cleauto.be
REQUEST_TO=support@cleauto.be
```

### Commandes Makefile

#### `make configure`
Télécharge les artifacts du formulaire frontend depuis Docker Hub, copie les assets public et templates.

```bash
make configure
```

**Processus** :
1. Crée un conteneur à partir de `ghcr.io/deuky/cleauto-form/artifact:latest`
2. Copie l'entire `/artifact` dans `.artifact/`
3. Copie le contenu dans `public/` et `templates/app/`
4. Supprime le conteneur temporaire

#### `make build` (si défini dans Makefile.docker)
Compile/prépare le projet (ex: autoload Composer)

#### `make install` (si défini dans Makefile.docker)
Installe les dépendances PHP via Composer

### Lancements locaux

**Démarrer le conteneur** :
```bash
docker-compose up
```

Accessible sur http://localhost:3002

**Voir les logs** :
```bash
docker-compose logs -f cleauto-mailer
```

**Arrêter** :
```bash
docker-compose down
```

---

## 📝 Points clés d'alignement pour le développement

### À connaître avant de développer

1. **Focus minimal** : C'est un microservice dédié à l'email, pas une application complète
2. **Pattern MicroKernel** : Pas de configuration complexe, utilise les attributs Routing
3. **Injection de dépendances** : Les emails FROM/TO viennent des paramètres services.yaml
4. **Static content** : Les assets du formulaire viennent d'un autre projet (cleauto-form)
5. **File uploads** : Gérés via multipart/form-data, compilés en pièces jointes
6. **Data enrichment** : IP client, dates, compteurs ajoutés côté serveur
7. **Fallback defaults** : Tous les templates utilisent `|default()` pour sécurité
8. **Email HTML** : Formatage simple avec tableau HTML (pas de CSS complexe)

### Checklist avant modification

- [ ] Vérifier que le changement n'affecte pas le contrat API (endpoints / JSON)
- [ ] Les modifications du template email doivent rester insérables dans `<html><body>...</body></html>`
- [ ] Tester les pièces jointes : vérifier que le compteur et les noms restent corrects
- [ ] Vérifier l'enrichissement des données RGPD (IP, dates)
- [ ] Les variables .env doivent rester cohérentes (REQUEST_FROM / REQUEST_TO)
- [ ] Valider avec `/mailer/preview` avant de vrai envoyer

### Conventions du code

- **Namespaces** : `App\{SubFolder}\{ClassName}` (PSR-4 routing)
- **Nommage** : PascalCase pour classes, snake_case pour les paramétrages
- **Routes** : Attribut `#[Route(...)]` sur les méthodes du contrôleur
- **Templates** : Twig avec syntaxe `{{ variable|filter }}`
- **Services** : Injectés par autowiring dans les constructeurs/méthodes

### Fichiers critiques

| Fichier | Description | Modifications attendues |
|---------|-------------|-------------------------|
| [src/Controller/MailerController.php](src/Controller/MailerController.php) | Endpoints API | Très rarement (routes essentielles) |
| [templates/mailer/car-request.html.twig](templates/mailer/car-request.html.twig) | Email HTML | Souvent (améliorations design/contenu) |
| [config/services.yaml](config/services.yaml) | Injection dépendances | Rarement (ajout paramètres) |
| [src/Twig/Extension/EmojiExtension.php](src/Twig/Extension/EmojiExtension.php) | Filtres Twig | Jamais |
| [Dockerfile](Dockerfile) | Containerization | Très rarement |

---

## 🔍 Debugging et Troubleshooting

### E-mails ne s'envoient pas

**Vérifier** :
1. Variables d'environnement `REQUEST_FROM` et `REQUEST_TO` definies
2. Logs du conteneur : `docker-compose logs cleauto-mailer`
3. Configuration du mailer dans `config/packages/mailer.yaml`

### Pièces jointes manquantes

**Vérifier** :
1. Fichiers uploadés reçus dans le formulaire
2. Le compteur `count-uploaded-files` s'incrémente correctement
3. Les noms de fichiers générés correctement (`item.0.jpg`, etc.)

### Template mal formaté

**Vérifier** :
1. Variables existent dans le JSON envoyé
2. Syntaxe Twig correcte (`{{ var|default() }}`)
3. HTML valide entre `<table>` tags

### Pages 404

**Vérifier** :
1. Routes enregistrées : `bin/console debug:router` (dans le conteneur)
2. Chemins API corrects : 
  - GET `/`,
  - POST `/mailer`,
  - POST `/mailer/preview`

---

## 📚 Ressources Utiles

- [Symfony 8.0 Documentation](https://symfony.com/doc/8.0/)
- [Symfony Mailer](https://symfony.com/doc/current/mailer.html)
- [Twig Documentation](https://twig.symfony.com/doc/3.x/)
- [Docker Documentation](https://docs.docker.com/)

---

## 🤝 Contribution

Lors du développement :
1. Maintenir la compatibilité avec les endpoints existants
2. Enrichir les données RGPD si nécessaire (ajouter IP, etc.)
3. Valider avec l'endpoint `/mailer/preview` avant envoi réel
4. Documenter les nouveaux filtres Twig ou services

---

**Version** : 1.1.0
**Dernière mise à jour** : 18 février 2025  
**Projet** : CleAuto Mailer (Symfony 8.0)
