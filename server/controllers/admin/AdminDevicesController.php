<?php

class AdminDevicesController {
    // The admin devices index page
    public static function index () {
        // The pagination variables
        $page = request('page', 1);
        $per_page = 9;
        $last_page = ceil(Devices::count() / $per_page);

        // Select all the devices and give them to the view
        $devices = Devices::selectPage($page, $per_page)->fetchAll();
        return view('admin.devices.index', [
            'devices' => $devices,
            'page' => $page,
            'last_page' => $last_page
        ]);
    }

    // The admin devices create page
    public static function create () {
        return view('admin.devices.create');
    }

    // The admin devices store page
    public static function store () {
        // Validate the user input
        validate([
            'name' => Devices::NAME_VALIDATION
        ]);

        // Insert the device to the database
        Devices::insert([
            'name' => request('name'),
            'key' => Devices::generateKey()
        ]);

        // Redirect to the new devices show page
        Router::redirect('/admin/devices/' . Database::lastInsertId());
    }

    // The admin devices show page
    public static function show ($device) {
        // Give all the data to the view
        return view('admin.devices.show', [ 'device' => $device ]);
    }

    // The admin devices edit page
    public static function edit ($device) {
        return view('admin.devices.edit', [ 'device' => $device ]);
    }

    // The admin devices update page
    public static function update ($device) {
        // Validate the user input
        validate([
            'name' => Devices::NAME_VALIDATION
        ]);

        // Update the device in the database
        Devices::update($device->id, [
            'name' => request('name')
        ]);

        // Redirect to the device page
        Router::redirect('/admin/devices/' . $device->id);
    }

    // The admin devices delete page
    public static function delete ($device) {
        Devices::delete($device->id);
        Router::redirect('/admin/devices');
    }
}
