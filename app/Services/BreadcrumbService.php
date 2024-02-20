<?php

namespace PSA\Services;

class BreadcrumbService
{
    /**
     * Generate a breadcrumb string from an array of breadcrumb items.
     *
     * Each breadcrumb item is an associative array with the following keys:
     * - 'url': The URL of the breadcrumb item.
     * - 'icon': The icon class of the breadcrumb item.
     * - 'title': The title of the breadcrumb item.
     *
     * The last breadcrumb item in the array will not have a hyperlink.
     *
     * @param array $breadcrumbs An array of breadcrumb items.
     * @return string The generated breadcrumb string.
     */
    public function generate(array $breadcrumbs): string
    {
        $breadcrumbString = '';
        $lastIndex = count($breadcrumbs) - 1;

        foreach ($breadcrumbs as $index => $breadcrumb) {
            // If the current breadcrumb item is the last one in the array, it will not have a hyperlink.
            if ($index === $lastIndex) {
                $breadcrumbString .= "<li class='breadcrumb-item'><i class='{$breadcrumb['icon']}'></i> {$breadcrumb['title']}</li>";
            } else {
                $breadcrumbString .= "<li class='breadcrumb-item'><a href='{$breadcrumb['url']}'><i class='{$breadcrumb['icon']}'></i> {$breadcrumb['title']}</a></li>";
            }
        }

        return $breadcrumbString;
    }
}