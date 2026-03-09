<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Brands
        $brands = [
            ['name' => 'Dell', 'description' => 'Dell Technologies'],
            ['name' => 'HP', 'description' => 'Hewlett-Packard'],
            ['name' => 'Lenovo', 'description' => 'Lenovo Group Limited'],
            ['name' => 'Acer', 'description' => 'Acer Inc.'],
            ['name' => 'ASUS', 'description' => 'ASUSTeK Computer Inc.'],
            ['name' => 'Apple', 'description' => 'Apple Inc.'],
            ['name' => 'Samsung', 'description' => 'Samsung Electronics'],
            ['name' => 'Epson', 'description' => 'Seiko Epson Corporation'],
            ['name' => 'Canon', 'description' => 'Canon Inc.'],
            ['name' => 'Brother', 'description' => 'Brother Industries'],
            ['name' => 'LG', 'description' => 'LG Electronics'],
            ['name' => 'Sony', 'description' => 'Sony Corporation'],
            ['name' => 'Microsoft', 'description' => 'Microsoft Corporation'],
            ['name' => 'Cisco', 'description' => 'Cisco Systems'],
            ['name' => 'Other', 'description' => 'Other brands'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }

        // Seed Categories
        $categories = [
            ['name' => 'Computer', 'description' => 'Desktop and laptop computers'],
            ['name' => 'Monitor', 'description' => 'Display monitors'],
            ['name' => 'Printer', 'description' => 'Printers and multifunction devices'],
            ['name' => 'Scanner', 'description' => 'Document scanners'],
            ['name' => 'Projector', 'description' => 'Presentation projectors'],
            ['name' => 'Network Equipment', 'description' => 'Routers, switches, access points'],
            ['name' => 'UPS', 'description' => 'Uninterruptible power supplies'],
            ['name' => 'Keyboard & Mouse', 'description' => 'Input devices'],
            ['name' => 'Audio Equipment', 'description' => 'Speakers, microphones, headsets'],
            ['name' => 'Camera', 'description' => 'Cameras and webcams'],
            ['name' => 'Storage Device', 'description' => 'External drives, USB drives'],
            ['name' => 'Tablet', 'description' => 'Tablet devices'],
            ['name' => 'Phone', 'description' => 'Desk phones and mobile devices'],
            ['name' => 'Furniture', 'description' => 'Office furniture and fixtures'],
            ['name' => 'Other', 'description' => 'Other equipment types'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Seed Departments
        $departments = [
            ['name' => 'Information Technology', 'code' => 'IT'],
            ['name' => 'Human Resources', 'code' => 'HR'],
            ['name' => 'Finance', 'code' => 'FIN'],
            ['name' => 'Operations', 'code' => 'OPS'],
            ['name' => 'Marketing', 'code' => 'MKT'],
            ['name' => 'Sales', 'code' => 'SLS'],
            ['name' => 'Administration', 'code' => 'ADM'],
            ['name' => 'Research & Development', 'code' => 'RND'],
            ['name' => 'Quality Assurance', 'code' => 'QA'],
            ['name' => 'Maintenance', 'code' => 'MNT'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        // Seed Locations
        $locations = [
            ['name' => 'Main Office', 'building' => 'Building A', 'floor' => '1st Floor', 'room' => 'Room 101'],
            ['name' => 'IT Department', 'building' => 'Building A', 'floor' => '2nd Floor', 'room' => 'Room 201'],
            ['name' => 'Conference Room A', 'building' => 'Building A', 'floor' => '1st Floor', 'room' => 'Room 105'],
            ['name' => 'Conference Room B', 'building' => 'Building A', 'floor' => '2nd Floor', 'room' => 'Room 205'],
            ['name' => 'Server Room', 'building' => 'Building A', 'floor' => 'Basement', 'room' => 'Room B01'],
            ['name' => 'Storage Room', 'building' => 'Building B', 'floor' => '1st Floor', 'room' => 'Room 101'],
            ['name' => 'Training Room', 'building' => 'Building B', 'floor' => '1st Floor', 'room' => 'Room 102'],
            ['name' => 'Reception', 'building' => 'Building A', 'floor' => '1st Floor', 'room' => 'Lobby'],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
