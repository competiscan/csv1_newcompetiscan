<?php

/**
 * Central application URL base.
 * - Local XAMPP: 'csv1_newcompetiscan'
 * - Domain document root: ''
 * Change the folder name only in this file when the project folder changes.
 */
$APP_FOLDER = 'csv1_newcompetiscan';
$APP_FOLDER = trim($APP_FOLDER, " /\\");
$APP_BASE_PATH = $APP_FOLDER === '' ? '' : '/' . $APP_FOLDER;

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$APP_BASE_URL = $scheme . '://' . $host . $APP_BASE_PATH;

$GLOBALS['APP_FOLDER'] = $APP_FOLDER;
$GLOBALS['APP_BASE_PATH'] = $APP_BASE_PATH;
$GLOBALS['APP_BASE_URL'] = $APP_BASE_URL;
