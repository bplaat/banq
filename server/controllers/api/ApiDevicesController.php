<?php

class ApiDevicesController {
    // The API devices index route
    public static function index () {
        // The pagination vars
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
        $count = Devices::count();

        // Select all the devices by page
        $devices = Devices::selectPage($page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'devices' => $devices
        ];
    }

    // The API devices search route
    public static function search () {
        $q = request('q', '');

        // The pagination vars
        $page = request('page', 1);
        $limit = (int)request('limit', 20);
        if ($limit < 0) $limit = 1;
        if ($limit > 50) $limit = 50;
        $count = Devices::searchCount($q);

        // Select all the devices by page
        $devices = Devices::searchPage($q, $page, $limit)->fetchAll();

        // Return the data as JSON
        return [
            'page' => $page,
            'limit' => $limit,
            'count' => $count,
            'devices' => $devices
        ];
    }

    // The API devices create route
    public static function create () {
        // Validate the user input
        api_validate([
            'name' => Devices::NAME_VALIDATION
        ]);

        // Insert the device to the database
        Devices::insert([
            'name' => request('name'),
            'key' => Devices::generateKey()
        ]);

        // Return a confirmation message
        return [
            'message' => 'The device has been created successfully',
            'device_id' => Database::lastInsertId()
        ];
    }

    // The API devices show route
    public static function show ($device) {
        return $device;
    }

    // The API devices edit route
    public static function edit ($device) {
        // Validate the user input
        api_validate([
            'name' => Devices::NAME_VALIDATION
        ]);

        // Update the device in the database
        Devices::update($device->id, [
            'name' => request('name')
        ]);

        // Return a confirmation message
        return [
            'message' => 'The device has been edited successfully'
        ];
    }

    // The API devices delete route
    public static function delete ($device) {
        // Delete the device
        Devices::delete($device->id);

        // Return a confirmation message
        return [
            'message' => 'The device has been deleted successfully'
        ];
    }
}
