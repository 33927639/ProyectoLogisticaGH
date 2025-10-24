<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\TblDepartment;
use App\Models\TblMunicipality;

class DepartmentsAndMunicipalitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentsData = [
            'Guatemala' => [
                'Guatemala', 'Santa Catarina Pinula', 'San José Pinula', 'San José del Golfo', 
                'Palencia', 'Chinautla', 'San Pedro Ayampuc', 'Mixco', 'San Pedro Sacatepéquez',
                'San Juan Sacatepéquez', 'San Raymundo', 'Chuarrancho', 'Fraijanes', 'Amatitlán',
                'Villa Nueva', 'Villa Canales', 'Petapa'
            ],
            'El Progreso' => [
                'Guastatoya', 'Morazán', 'San Agustín Acasaguastlán', 'San Cristóbal Acasaguastlán',
                'El Jícaro', 'Sansare', 'Sanarate', 'San Antonio La Paz'
            ],
            'Sacatepéquez' => [
                'Antigua Guatemala', 'Jocotenango', 'Pastores', 'Sumpango', 'Santo Domingo Xenacoj',
                'Santiago Sacatepéquez', 'San Bartolomé Milpas Altas', 'San Lucas Sacatepéquez',
                'Santa Lucía Milpas Altas', 'Magdalena Milpas Altas', 'Santa María de Jesús',
                'Ciudad Vieja', 'San Miguel Dueñas', 'San Juan Alotenango', 'San Antonio Aguas Calientes',
                'Santa Catarina Barahona'
            ],
            'Chimaltenango' => [
                'Chimaltenango', 'San José Poaquil', 'San Martín Jilotepeque', 'Comalapa',
                'Santa Apolonia', 'Tecpán', 'Patzún', 'San Miguel Pochuta', 'Patzicía',
                'Santa Cruz Balanyá', 'Acatenango', 'San Pedro Yepocapa', 'San Andrés Itzapa',
                'Parramos', 'Zaragoza', 'El Tejar'
            ],
            'Escuintla' => [
                'Escuintla', 'Santa Lucía Cotzumalguapa', 'La Democracia', 'Siquinalá',
                'Masagua', 'Tiquisate', 'La Gomera', 'Guanagazapa', 'San José',
                'Iztapa', 'Palín', 'San Vicente Pacaya', 'Nueva Concepción'
            ],
            'Santa Rosa' => [
                'Cuilapa', 'Barberena', 'Santa Rosa de Lima', 'Casillas', 'San Rafael Las Flores',
                'Oratorio', 'San Juan Tecuaco', 'Chiquimulilla', 'Taxisco', 'Santa María Ixhuatán',
                'Guazacapán', 'Santa Cruz Naranjo', 'Pueblo Nuevo Viñas', 'Nueva Santa Rosa'
            ],
            'Sololá' => [
                'Sololá', 'San José Chacayá', 'Santa María Visitación', 'Santa Lucía Utatlán',
                'Nahualá', 'Santa Catarina Ixtahuacán', 'Santa Clara La Laguna', 'Concepción',
                'San Andrés Semetabaj', 'Panajachel', 'Santa Catarina Palopó', 'San Antonio Palopó',
                'San Lucas Tolimán', 'Santa Cruz La Laguna', 'San Pablo La Laguna', 'San Marcos La Laguna',
                'San Juan La Laguna', 'San Pedro La Laguna', 'Santiago Atitlán'
            ],
            'Totonicapán' => [
                'Totonicapán', 'San Cristóbal Totonicapán', 'San Francisco El Alto', 'San Andrés Xecul',
                'Momostenango', 'Santa María Chiquimula', 'Santa Lucía La Reforma', 'San Bartolo'
            ],
            'Quetzaltenango' => [
                'Quetzaltenango', 'Salcajá', 'Olintepeque', 'San Carlos Sija', 'Sibilia',
                'Cabricán', 'Cajolá', 'San Miguel Sigüilá', 'San Juan Ostuncalco', 'San Mateo',
                'Concepción Chiquirichapa', 'San Martín Sacatepéquez', 'Almolonga', 'Cantel',
                'Huitán', 'Zunil', 'Colomba Costa Cuca', 'San Francisco La Unión', 'El Palmar',
                'Coatepeque', 'Génova Costa Cuca', 'Flores Costa Cuca', 'La Esperanza', 'Palestina de Los Altos'
            ],
            'Suchitepéquez' => [
                'Mazatenango', 'Cuyotenango', 'San Francisco Zapotitlán', 'San Bernardino',
                'San José El Ídolo', 'Santo Domingo Suchitepéquez', 'San Lorenzo', 'Samayac',
                'San Pablo Jocopilas', 'San Antonio Suchitepéquez', 'San Miguel Panán',
                'San Gabriel', 'Chicacao', 'Patulul', 'Santa Bárbara', 'San Juan Bautista',
                'Santo Tomás La Unión', 'Zunilito', 'Pueblo Nuevo', 'Río Bravo'
            ],
            'Retalhuleu' => [
                'Retalhuleu', 'San Sebastián', 'Santa Cruz Muluá', 'San Martín Zapotitlán',
                'San Felipe', 'San Andrés Villa Seca', 'Champerico', 'Nuevo San Carlos', 'El Asintal'
            ],
            'San Marcos' => [
                'San Marcos', 'San Pedro Sacatepéquez', 'San Antonio Sacatepéquez', 'Comitancillo',
                'San Miguel Ixtahuacán', 'Concepción Tutuapa', 'Tacaná', 'Sibinal', 'Tajumulco',
                'Tejutla', 'San Rafael Pie de La Cuesta', 'Nuevo Progreso', 'El Tumbador',
                'El Rodeo', 'Malacatán', 'Catarina', 'Ayutla', 'Ocós', 'San Pablo',
                'El Quetzal', 'La Reforma', 'Pajapita', 'Ixchiguán', 'San José Ojetenam',
                'San Cristóbal Cucho', 'Sipacapa', 'Esquipulas Palo Gordo', 'Río Blanco',
                'San Lorenzo', 'La Blanca'
            ],
            'Huehuetenango' => [
                'Huehuetenango', 'Chiantla', 'Malacatancito', 'Cuilco', 'Nentón', 'San Pedro Necta',
                'Jacaltenango', 'Soloma', 'Ixtahuacán', 'Santa Bárbara', 'La Libertad', 'La Democracia',
                'San Miguel Acatán', 'San Rafael La Independencia', 'Todos Santos Cuchumatán',
                'San Juan Atitán', 'Santa Eulalia', 'San Mateo Ixtatán', 'Colotenango', 'San Sebastián Huehuetenango',
                'San Rafael Petzal', 'San Gaspar Ixchil', 'Santiago Chimaltenango', 'Santa Ana Huista',
                'San Antonio Huista', 'San Sebastián Coatán', 'Barillas', 'Aguacatán', 'San Juan Ixcoy',
                'Santa Cruz Barillas', 'Tectitán', 'Concepción Huista'
            ],
            'Quiché' => [
                'Santa Cruz del Quiché', 'Chiché', 'Chinique', 'Zacualpa', 'Chajul', 'Santo Tomás Chiché',
                'Patzité', 'San Antonio Ilotenango', 'San Pedro Jocopilas', 'Cunén', 'San Juan Cotzal',
                'Joyabaj', 'Nebaj', 'San Andrés Sajcabajá', 'San Miguel Uspantán', 'Sacapulas',
                'San Bartolomé Jocotenango', 'Canillá', 'Chicamán', 'Ixcán', 'Pachalum'
            ],
            'Baja Verapaz' => [
                'Salamá', 'San Miguel Chicaj', 'Rabinal', 'Cubulco', 'Granados', 'Santa Cruz El Chol',
                'San Jerónimo', 'Purulhá'
            ],
            'Alta Verapaz' => [
                'Cobán', 'Santa Cruz Verapaz', 'San Cristóbal Verapaz', 'Tactic', 'Tamahú',
                'San Miguel Tucurú', 'Panzós', 'Senahú', 'San Pedro Carchá', 'San Juan Chamelco',
                'Lanquín', 'Santa María Cahabón', 'Chisec', 'Chahal', 'Fray Bartolomé de Las Casas',
                'Santa Catalina La Tinta', 'Raxruhá'
            ],
            'Petén' => [
                'Flores', 'San José', 'San Benito', 'San Andrés', 'La Libertad', 'San Francisco',
                'Santa Ana', 'Dolores', 'San Luis', 'Sayaxché', 'Melchor de Mencos', 'Poptún',
                'Las Cruces', 'El Chal'
            ],
            'Izabal' => [
                'Puerto Barrios', 'Livingston', 'El Estor', 'Morales', 'Los Amates'
            ],
            'Zacapa' => [
                'Zacapa', 'Estanzuela', 'Río Hondo', 'Gualán', 'Teculután', 'Usumatlán',
                'Cabañas', 'Huité', 'San Diego', 'La Unión'
            ],
            'Chiquimula' => [
                'Chiquimula', 'San José La Arada', 'San Juan Ermita', 'Jocotán', 'Camotán',
                'Olopa', 'Esquipulas', 'Concepción Las Minas', 'Quezaltepeque', 'San Jacinto', 'Ipala'
            ],
            'Jalapa' => [
                'Jalapa', 'San Pedro Pinula', 'San Luis Jilotepeque', 'San Miguel Chaparrón',
                'San Carlos Alzatate', 'Monjas', 'Mataquescuintla'
            ],
            'Jutiapa' => [
                'Jutiapa', 'El Progreso', 'Santa Catarina Mita', 'Agua Blanca', 'Asunción Mita',
                'Yupiltepeque', 'Atescatempa', 'Jerez', 'El Adelanto', 'Zapotitlán', 'Comapa',
                'Jalpatagua', 'Conguaco', 'Moyuta', 'Pasaco', 'Quesada'
            ]
        ];

        foreach ($departmentsData as $departmentName => $municipalities) {
            $department = TblDepartment::create([
                'name_department' => $departmentName,
                'status' => true
            ]);

            foreach ($municipalities as $municipalityName) {
                TblMunicipality::create([
                    'name_municipality' => $municipalityName,
                    'department_id' => $department->id_department,
                    'status' => true
                ]);
            }
        }

        $this->command->info('Departamentos y municipios de Guatemala creados exitosamente.');
    }
}