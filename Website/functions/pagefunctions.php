<?php

/**
 * Show the page not found error
 */
function Page_ShowError404() {
    $page = new Error404Page();
    $page->Show();
    exit();
}