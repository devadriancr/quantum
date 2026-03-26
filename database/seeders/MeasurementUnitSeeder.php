<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // --- CONTEO Y PIEZAS (LO MÁS USADO EN PISO) ---
            ['code' => 'EA',  'name' => 'Cada uno (Pieza)', 'status' => true], // Estándar Infor LX / SAP (Each)
            ['code' => 'PZA', 'name' => 'Pieza', 'status' => true],            // Común en sistemas locales
            ['code' => 'SET', 'name' => 'Juego / Set', 'status' => true],      // Conjunto de piezas
            ['code' => 'KIT', 'name' => 'Kit de Ensamble', 'status' => true],  // Kit para línea de producción
            ['code' => 'UN',  'name' => 'Unidad', 'status' => true],           // Genérico SAP/Oracle

            // --- EMBALAJE Y LOGÍSTICA (WMS / ALMACÉN) ---
            ['code' => 'PLT', 'name' => 'Pallet / Tarima', 'status' => true],  // Tarima estándar
            ['code' => 'BOX', 'name' => 'Caja', 'status' => true],             // Empaque secundario
            ['code' => 'CTN', 'name' => 'Cartón', 'status' => true],           // Usado mucho en SAP
            ['code' => 'PAI', 'name' => 'Paila / Cubeta', 'status' => true],   // Infor LX (Pail)
            ['code' => 'ROL', 'name' => 'Rollo / Bobina', 'status' => true],   // Para acero o etiquetas
            ['code' => 'HU',  'name' => 'Unidad de Manejo', 'status' => true], // Handling Unit (SAP/Oracle)

            // --- PESO (MATERIA PRIMA Y SCRAP) ---
            ['code' => 'KG',  'name' => 'Kilogramo', 'status' => true],        // Estándar universal
            ['code' => 'TN',  'name' => 'Tonelada Métrica', 'status' => true], // Muy usado en AS/400 para acero
            ['code' => 'LB',  'name' => 'Libra', 'status' => true],            // Proveedores USA
            ['code' => 'GR',  'name' => 'Gramo', 'status' => true],            // Químicos o piezas micro

            // --- LONGITUD Y ÁREA ---
            ['code' => 'M',   'name' => 'Metro', 'status' => true],            // Cables y mangueras
            ['code' => 'MM',  'name' => 'Milímetro', 'status' => true],        // Ingeniería técnica
            ['code' => 'M2',  'name' => 'Metro Cuadrado', 'status' => true],   // Lámina de acero / Alfombras
            ['code' => 'FT',  'name' => 'Pie', 'status' => true],              // Foot (común en AS/400)

            // --- VOLUMEN Y LÍQUIDOS (MANTENIMIENTO / PINTURA) ---
            ['code' => 'L',   'name' => 'Litro', 'status' => true],            // Aceites y refrigerantes
            ['code' => 'GAL', 'name' => 'Galón', 'status' => true],            // Químicos importados
            ['code' => 'DR',  'name' => 'Tambo / Barril', 'status' => true],   // Drum (200L aprox)
            ['code' => 'ML',  'name' => 'Mililitro', 'status' => true],        // Aditivos
        ];

        foreach ($units as $unit) {
            MeasurementUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }
    }
}
