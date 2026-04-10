# Xophz Questbook CRM

> **Category:** Trajectory · **Version:** 1.0.0

A CRM that manages client profiles, tasks, assets, and integrations without ever leaving WordPress.

## Description

**Questbook** is a comprehensive Customer Relationship Management (CRM) system for the COMPASS platform. It replaces expensive third-party tools by centralizing client data, project assets, and WPMU DEV Hub integration directly into your WordPress dashboard, all presented via a futuristic, glassmorphic UI.

### Core Capabilities

- **Contact Management** – Custom Post Type (`compass_contact`) to store comprehensive client profiles including names, emails, roles, companies, and associated assets.
- **Client Assets** – Upload, tag, and manage client-specific files (logos, brand assets, documents) directly tied to their profile.
- **WPMU DEV Hub Integration** – Syncs directly with WPMU DEV's Hub API to manage sites, services, client billing, and reports right from within Questbook.
- **REST API** – Fully featured REST API for seamless integration with the Vue frontend.

## Requirements

- **Xophz COMPASS** parent plugin (active)
- WordPress 5.8+, PHP 7.4+

## Installation

1. Ensure **Xophz COMPASS** is installed and active.
2. Upload `xophz-compass-quests` to `/wp-content/plugins/`.
3. Activate through the Plugins menu.
4. Access via the My Compass dashboard → **Questbook**.

## PHP Class Map

| Class | File | Purpose |
|---|---|---|
| `Xophz_Compass_Quests` | `class-xophz-compass-quests.php` | Core plugin hooks and loader |
| `Xophz_Compass_Quests_CPT` | `class-xophz-compass-quests-cpt.php` | Defines the `compass_contact` post type |
| `Xophz_Compass_Quests_Rest` | `class-xophz-compass-quests-rest.php` | Handles CRUD REST API for contacts |
| `Xophz_Compass_Quests_API` | `class-xophz-compass-quests-api.php` | Additional REST routes |
| `Xophz_Compass_Quests_WPMUDEV` | `class-xophz-compass-quests-wpmudev.php` | Integration with WPMU DEV Hub |

## Frontend Routes

| Route | View | Description |
|---|---|---|
| `/quests` | Questbook Dashboard | Contact list, stats, and search interface |
| `/quests/:id` | Contact Detail | Deep dive into client profile, assets, and Hub linkage |

## Changelog

### 1.0.0

- Initial release with `compass_contact` CPT, Contact REST API, Client Assets manager, and WPMU DEV Hub integration.
