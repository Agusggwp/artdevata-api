<?php

namespace Database\Seeders;

use App\Models\Documentation;
use Illuminate\Database\Seeder;

class DocumentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'title'       => 'Penyelarasan Sudut Kamera CCTV',
                'category'    => 'Adjustment',
                'description' => 'Proses penyesuaian arah dan sudut elevasi tangkapan lensa kamera keamanan di area outdoor.',
                'image'       => 'https://images.unsplash.com/photo-1557597774-9d273605dfa9?w=600&auto=format&fit=crop&q=80',
                'status'      => 'active',
                'sort_order'  => 1,
            ],
            [
                'title'       => 'Kalibrasi Sensitivitas Sensor Optik',
                'category'    => 'Calibration',
                'description' => 'Pengecekan dan kalibrasi presisi tangkapan infra merah serta deteksi gerakan.',
                'image'       => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600&auto=format&fit=crop&q=80',
                'status'      => 'active',
                'sort_order'  => 2,
            ],
            [
                'title'       => 'Instalasi Fisik Unit Kamera Outdoor',
                'category'    => 'Installation',
                'description' => 'Pemasangan dudukan bracket dan penyambungan kabel komunikasi jaringan.',
                'image'       => 'https://images.unsplash.com/photo-1581092335397-9583fe92d232?w=600&auto=format&fit=crop&q=80',
                'status'      => 'active',
                'sort_order'  => 3,
            ],
            [
                'title'       => 'Hasil Akhir Pemasangan & pengujian HD',
                'category'    => 'Result',
                'description' => 'Tampilan akhir unit kamera PTZ terpasang siap pakai dengan sudut pantau optimal.',
                'image'       => 'https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=600&auto=format&fit=crop&q=80',
                'status'      => 'active',
                'sort_order'  => 4,
            ],
        ];

        foreach ($items as $item) {
            Documentation::create($item);
        }
    }
}
