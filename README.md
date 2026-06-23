# Tadjiso

**Gestion multi‑stations pour entreprises pétrolières**

---

## 📌 Description

Tadjiso est une plateforme web développée avec **Symfony** pour la gestion multi‑stations de carburant.  
Elle permet aux entreprises pétrolières de gérer plusieurs stations, des stocks de carburant (cuves, jaugeages, indexations), des ventes et des factures PDF.  
L'application intègre un système de rôles (Super‑Admin, Admin entreprise, Gérant de station).

---

## 🛠️ Stack technique

| Composant | Technologie |
|-----------|-------------|
| **Framework** | Symfony 6 |
| **Langage** | PHP 8+ |
| **ORM** | Doctrine |
| **Base de données** | MySQL |
| **Frontend** | Twig, Bootstrap, JavaScript |
| **Génération PDF** | (à préciser) |
| **Sécurité** | Annotations `@Security`, rôles |
| **API** | REST |

---

## 📦 Fonctionnalités

- ✅ Gestion multi‑entreprises pétrolières
- ✅ Gestion multi‑stations par entreprise
- ✅ Suivi des stocks de carburant (cuves, jaugeages, indexations)
- ✅ Gestion des pistolets et pompes
- ✅ Gestion des ventes (par cuve ou pistolet)
- ✅ Génération de factures PDF
- ✅ Tableaux de bord par station
- ✅ Rôles : Super‑Admin, Admin entreprise, Gérant de station
- ✅ Historique des opérations
- ✅ Alertes de stock bas

---

## 🚀 Installation et lancement

### Prérequis

- PHP 8.1+
- Composer
- MySQL
- Node.js (pour les assets)

### Cloner le projet

```bash
git clone https://github.com/yarasekou/tadjiso.git
cd tadjiso
