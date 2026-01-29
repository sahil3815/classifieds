# CL Classified – Free Classified Ads WordPress Theme

**Contributors:** [RadiusTheme](https://radiustheme.com/)  
**Tags:** classified listing, directory, listing, elementor, business directory, listing theme  
**Requires at least:** 5.5  
**Tested up to:** 6.8  
**Requires PHP:** 7.4  
**Stable tag:** 0.0.1  
**License:** GPLv3  
**License URI:** https://www.gnu.org/licenses/gpl-3.0.html

---

## Overview

**CL Classified** is a top-tier free Classified Ads WordPress Theme that makes it easy to own an exceptional classified ads website without worrying about coding. Its multipurpose features make it perfect for various niches, including Music, Restaurants, Pet Shops, Gyms, and Hotels.

This repository contains both PHP and JavaScript build tools using **Composer** and **NPM** for streamlined development and deployment.

---

## Installation

Follow these steps to install and run CL Classified locally:

---

### Clone the Repository

Clone the repository using one of the following methods:

#### Using HTTPS
```bash
git clone https://github.com/radiustheme/cl-classified.git
```

#### Using SSH
```bash
git clone git@github.com:radiustheme/cl-classified.git
```
#### Navigate to the Plugin Directory
```bash
cd cl-classified
```
### Install Dependencies
Run the following commands to install the required dependencies:
```bash
composer install // install dev dependencies also
composer install --no-dev // install only production dependencies
npm install --force
```
#### Start Development Server
```bash
gulp run watch
```

#### Production Build
```bash
gulp build
```