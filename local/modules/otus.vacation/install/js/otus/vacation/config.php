<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

return [
    "js" => [
        "./src/js/moment.min.js",
        "./src/js/daterangepicker.js",
        "./src/js/handleLinks.js",
    ],
    "css" => [
        "./src/css/daterangepicker.css",
        "./src/css/comment.css"
    ],
    "rel" => [],
];