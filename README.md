# HamLog WebApp

HamLog WebApp is a lightweight multi-user ham radio logging application built with PHP, MySQL and Bootstrap 5, designed to run even on simple shared hosting environments such as Aruba.

The project focuses on fast and practical radio operation, offering a clean responsive interface optimized both for desktop and smartphone use.

## Main Features

* Multi-user architecture with Superadmin management
* Multiple logs per user (contest, portable, SOTA, POTA, activations, etc.)
* Fast QSO entry workflow optimized for real radio operation
* Persistent Band and Mode selection
* Automatic default RST handling
* UTC ADIF export
* Responsive Bootstrap 5 dark interface
* Offline queue with automatic synchronization
* Local cache protection against temporary connection loss
* QSO synchronization status visualization
* Log editing and management
* Secure authentication with hashed passwords
* Shared-hosting friendly architecture (PHP + MySQL)

## Designed For

The application is intended for:

* daily ham radio logging
* contest operation
* portable activity
* SOTA / POTA activations
* radio clubs and shared environments

## Technical Stack

* PHP
* MySQL
* Bootstrap 5
* Vanilla JavaScript
* PDO prepared statements

No frameworks, Docker containers or Node.js dependencies are required.

## Current Release

Version 1.1 introduces:

* offline QSO queue
* automatic synchronization
* editable log details
* expanded QSO form fields
* live QSO counters
* improved UX and reliability

## Installation

The application includes:

* SQL installation script
* guided setup page
* example configuration file
* security `.htaccess`
* full installation documentation

Designed to be deployed easily via FTP on standard shared hosting environments.
