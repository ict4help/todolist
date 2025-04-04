<?php
// config.php

// Define the JSON file path
define('TODO_FILE', 'todos.json');

// Initialize the JSON file if it doesn't exist
if (!file_exists(TODO_FILE)) {
    file_put_contents(TODO_FILE, json_encode([]));
}
?>
