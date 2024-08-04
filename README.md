# WPMap

WPMap is a WordPress plugin that allows you to easily embed interactive maps into your WordPress site. The plugin supports various map providers and customization options.

## Features

- Embed interactive maps using shortcodes
- Supports multiple map providers (Google Maps, OpenStreetMap, etc.)
- Customizable map markers and styles
- Responsive and mobile-friendly

## Prerequisites

- WordPress 5.0 or higher
- PHP 7.0 or higher

## Installation

1. Download the plugin zip file from the [releases page](https://github.com/yourusername/wpmap/releases).

2. In your WordPress admin dashboard, go to `Plugins` > `Add New` > `Upload Plugin`.

3. Choose the downloaded zip file and click `Install Now`.

4. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

1. Go to the WPMap settings page in your WordPress admin dashboard to configure the plugin.

2. Use the following shortcode to embed a map in your posts or pages:
    ```shortcode
    [wpmap lat="40.7128" lng="-74.0060" zoom="12" provider="google"]
    ```

3. Customize the shortcode attributes to fit your needs:
    - `lat`: Latitude of the map center
    - `lng`: Longitude of the map center
    - `zoom`: Zoom level of the map
    - `provider`: Map provider (e.g., `google`, `osm`)

## Project Structure
