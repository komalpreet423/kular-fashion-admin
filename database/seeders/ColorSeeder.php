<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'name' => 'AQUA BLUE',
                'short_name' => 'AQUA',
                'code' => '025',
                'ui_color_code' => '#00FFF0',
            ],
            [
                'name' => 'BEIGE',
                'short_name' => 'BEIGE',
                'code' => '033',
                'ui_color_code' => '#F5F5DC',
            ],
            [
                'name' => 'BLACK/WHITE',
                'short_name' => 'BLACK/WHITE',
                'code' => '108',
                'ui_color_code' => '#000000',
            ],
            [
                'name' => 'BLACK',
                'short_name' => 'BLK',
                'code' => '010',
                'ui_color_code' => '#000000',
            ],
            [
                'name' => 'BLUE',
                'short_name' => 'BLUE',
                'code' => '020',
                'ui_color_code' => '#0000FF',
            ],
            [
                'name' => 'BOTTLE GREEN',
                'short_name' => 'BOTTL',
                'code' => '066',
                'ui_color_code' => '#006A4E',
            ],
            [
                'name' => 'BRICK RED',
                'short_name' => 'BRICK',
                'code' => '045',
                'ui_color_code' => '#CB4154',
            ],
            [
                'name' => 'BROWN',
                'short_name' => 'BROWN',
                'code' => '030',
                'ui_color_code' => '#A52A2A',
            ],
            [
                'name' => 'BURGUNDY',
                'short_name' => 'BURG',
                'code' => '042',
                'ui_color_code' => '#800020',
            ],
            [
                'name' => 'CAMEL',
                'short_name' => 'CAMEL',
                'code' => '055',
                'ui_color_code' => '#C19A6B',
            ],
            [
                'name' => 'CARAMEL',
                'short_name' => 'CARAMEL',
                'code' => '006',
                'ui_color_code' => '#AF6E4D',
            ],
            [
                'name' => 'CERISE PINK',
                'short_name' => 'CERI',
                'code' => '046',
                'ui_color_code' => '#DE3163',
            ],
            [
                'name' => 'CHAMPAGNE',
                'short_name' => 'CHAM',
                'code' => '094',
                'ui_color_code' => '#F7E7CE',
            ],
            [
                'name' => 'CHARCOAL',
                'short_name' => 'CHARC',
                'code' => '011',
                'ui_color_code' => '#36454F',
            ],
            [
                'name' => 'CHOCOLATE',
                'short_name' => 'CHOC',
                'code' => '038',
                'ui_color_code' => '#7B3F00',
            ],
            [
                'name' => 'COPPER',
                'short_name' => 'COPPER',
                'code' => '036',
                'ui_color_code' => '#B87333',
            ],
            [
                'name' => 'CORAL',
                'short_name' => 'CORAL',
                'code' => '088',
                'ui_color_code' => '#FF7F50',
            ],
            [
                'name' => 'CREAM',
                'short_name' => 'CREAM',
                'code' => '091',
                'ui_color_code' => '#FFFDD0',
            ],
            [
                'name' => 'DARK GREEN',
                'short_name' => 'D/GREEN',
                'code' => '069',
                'ui_color_code' => '#006400',
            ],
            [
                'name' => 'DARK GREY',
                'short_name' => 'D/GREY',
                'code' => '059',
                'ui_color_code' => '#A9A9A9',
            ],
            [
                'name' => 'DARK BLUE',
                'short_name' => 'DBLUE',
                'code' => '029',
                'ui_color_code' => '#00008B',
            ],
            [
                'name' => 'ECRU',
                'short_name' => 'ECRU',
                'code' => '107',
                'ui_color_code' => '#C2B280',
            ],
            [
                'name' => 'EGG SHELL',
                'short_name' => 'EGGS',
                'code' => '039',
                'ui_color_code' => '#F0EAD6',
            ],
            [
                'name' => 'EMERALD',
                'short_name' => 'EMERALD',
                'code' => '068',
                'ui_color_code' => '#50C878',
            ],
            [
                'name' => 'FAWN',
                'short_name' => 'FAWN',
                'code' => '037',
                'ui_color_code' => '#E5AA70',
            ],
            [
                'name' => 'GOLD',
                'short_name' => 'GOLD',
                'code' => '081',
                'ui_color_code' => '#FFD700',
            ],
            [
                'name' => 'GRAPE',
                'short_name' => 'GRAPE',
                'code' => '072',
                'ui_color_code' => '#6F2DA8',
            ],
            [
                'name' => 'GREEN',
                'short_name' => 'GREEN',
                'code' => '060',
                'ui_color_code' => '#008000',
            ],
            [
                'name' => 'GREY',
                'short_name' => 'GRY',
                'code' => '050',
                'ui_color_code' => '#808080',
            ],
            [
                'name' => 'INDIGO',
                'short_name' => 'INDIGO',
                'code' => '027',
                'ui_color_code' => '#4B0082',
            ],
            [
                'name' => 'IVORY',
                'short_name' => 'IVORY',
                'code' => '095',
                'ui_color_code' => '#FFFFF0',
            ],
            [
                'name' => 'JADE GREEN',
                'short_name' => 'JADE GREEN',
                'code' => '067',
                'ui_color_code' => '#00A86B',
            ],
            [
                'name' => 'KHAKI',
                'short_name' => 'KHAKI',
                'code' => '061',
                'ui_color_code' => '#C3B091',
            ],
            [
                'name' => 'LIGHT GREY',
                'short_name' => 'L/GRY',
                'code' => '053',
                'ui_color_code' => '#D3D3D3',
            ],
            [
                'name' => 'LIGHT STONEWASH',
                'short_name' => 'L/STN',
                'code' => '005',
                'ui_color_code' => '#B0C4DE',
            ],
            [
                'name' => 'LEMON',
                'short_name' => 'LEMON',
                'code' => '086',
                'ui_color_code' => '#FFF44F',
            ],
            [
                'name' => 'LILAC',
                'short_name' => 'LILAC',
                'code' => '074',
                'ui_color_code' => '#C8A2C8',
            ],
            [
                'name' => 'LIME GREEN',
                'short_name' => 'LIME',
                'code' => '064',
                'ui_color_code' => '#32CD32',
            ],
            [
                'name' => 'LONG 34"',
                'short_name' => 'LONG 34"',
                'code' => '103',
                'ui_color_code' => '#C0C0C0',
            ],
            [
                'name' => 'MID BRN',
                'short_name' => 'MID BRN',
                'code' => '105',
                'ui_color_code' => '#8B5A2B',
            ],
            [
                'name' => 'MINK',
                'short_name' => 'MINK',
                'code' => '058',
                'ui_color_code' => '#A28E7A',
            ],
            [
                'name' => 'MINT GREEN',
                'short_name' => 'MINT',
                'code' => '063',
                'ui_color_code' => '#98FF98',
            ],
            [
                'name' => 'MULTI COLOUR',
                'short_name' => 'MULTICOLOUR',
                'code' => '001',
                'ui_color_code' => '#808080',
            ],
            [
                'name' => 'MUSTARD',
                'short_name' => 'MUST',
                'code' => '082',
                'ui_color_code' => '#FFDB58',
            ],
            [
                'name' => 'NATURAL',
                'short_name' => 'NAT',
                'code' => '093',
                'ui_color_code' => '#E6D5B8',
            ],
            [
                'name' => 'NAVY',
                'short_name' => 'NAVY',
                'code' => '021',
                'ui_color_code' => '#000080',
            ],
            [
                'name' => 'NUDE',
                'short_name' => 'NUDE',
                'code' => '002',
                'ui_color_code' => '#E3BC9A',
            ],
            [
                'name' => 'NAVY/WHITE',
                'short_name' => 'NWH',
                'code' => '104',
                'ui_color_code' => '#1A237E',
            ],
            [
                'name' => 'OLIVE GREEN',
                'short_name' => 'OLIVE',
                'code' => '062',
                'ui_color_code' => '#808000',
            ],
            [
                'name' => 'ORANGE',
                'short_name' => 'ORANGE',
                'code' => '084',
                'ui_color_code' => '#FFA500',
            ],
            [
                'name' => 'OXBLOOD',
                'short_name' => 'OXBLOOD',
                'code' => '048',
                'ui_color_code' => '#FFFFFF',
            ],
            [
                'name' => 'PEACH',
                'short_name' => 'PEACH',
                'code' => '085',
                'ui_color_code' => '#FFE5B4',
            ],
            [
                'name' => 'PETROL BLUE',
                'short_name' => 'PETRO',
                'code' => '073',
                'ui_color_code' => '#005F6B',
            ],
            [
                'name' => 'PINK',
                'short_name' => 'PINK',
                'code' => '044',
                'ui_color_code' => '#FFC0CB',
            ],
            [
                'name' => 'PLUM',
                'short_name' => 'PLUM',
                'code' => '071',
                'ui_color_code' => '#8E4585',
            ],
            [
                'name' => 'PORT',
                'short_name' => 'PORT',
                'code' => '049',
                'ui_color_code' => '#6D1A36',
            ],
            [
                'name' => 'PURPLE',
                'short_name' => 'PURP',
                'code' => '070',
                'ui_color_code' => '#800080',
            ],
            [
                'name' => 'RED',
                'short_name' => 'RED',
                'code' => '040',
                'ui_color_code' => '#FF0000',
            ],
            [
                'name' => 'REGULAR 32"',
                'short_name' => 'REGULAR 32"',
                'code' => '102',
                'ui_color_code' => '#FF0000',
            ],
            [
                'name' => 'ROSE',
                'short_name' => 'ROSE',
                'code' => '054',
                'ui_color_code' => '#FF0000',
            ],
            [
                'name' => 'ROYAL BLUE',
                'short_name' => 'ROYAL',
                'code' => '026',
                'ui_color_code' => '#4169E1',
            ],
            [
                'name' => 'RUST',
                'short_name' => 'RUST',
                'code' => '043',
                'ui_color_code' => '#B7410E',
            ],
            [
                'name' => 'SAGE',
                'short_name' => 'SAGE',
                'code' => '057',
                'ui_color_code' => '#BCB88A',
            ],
            [
                'name' => 'SALMON PINK',
                'short_name' => 'SALM',
                'code' => '047',
                'ui_color_code' => '#FF91A4',
            ],
            [
                'name' => 'SAND',
                'short_name' => 'SAND',
                'code' => '035',
                'ui_color_code' => '#C2B280',
            ],
            [
                'name' => 'SEA GREEN',
                'short_name' => 'SEA G',
                'code' => '065',
                'ui_color_code' => '#2E8B57',
            ],
            [
                'name' => 'SHORT 30"',
                'short_name' => 'SHORT 30"',
                'code' => '101',
                'ui_color_code' => '#D3D3D3',
            ],
            [
                'name' => 'SILVER',
                'short_name' => 'SILVER',
                'code' => '092',
                'ui_color_code' => '#C0C0C0',
            ],
            [
                'name' => 'SKY BLUE',
                'short_name' => 'SKY B',
                'code' => '023',
                'ui_color_code' => '#87CEEB',
            ],
            [
                'name' => 'SLATE GREY',
                'short_name' => 'SLATE',
                'code' => '052',
                'ui_color_code' => '#708090',
            ],
            [
                'name' => 'STEEL GREY',
                'short_name' => 'STEEL',
                'code' => '051',
                'ui_color_code' => '#71797E',
            ],
            [
                'name' => 'STONEWASH',
                'short_name' => 'STN',
                'code' => '004',
                'ui_color_code' => '#748AA6',
            ],
            [
                'name' => 'TAN',
                'short_name' => 'TAN',
                'code' => '083',
                'ui_color_code' => '#D2B48C',
            ],
            [
                'name' => 'TAUPE',
                'short_name' => 'TAUPE',
                'code' => '031',
                'ui_color_code' => '#483C32',
            ],
            [
                'name' => 'TEAL',
                'short_name' => 'TEAL',
                'code' => '022',
                'ui_color_code' => '#008080',
            ],
            [
                'name' => 'TURQUOISE',
                'short_name' => 'TURQ',
                'code' => '024',
                'ui_color_code' => '#40E0D0',
            ],
            [
                'name' => 'USED',
                'short_name' => 'USED',
                'code' => '003',
                'ui_color_code' => '#F5F5F5',
            ],
            [
                'name' => 'VIOLET',
                'short_name' => 'VIOLT',
                'code' => '075',
                'ui_color_code' => '#8F00FF',
            ],
            [
                'name' => 'WHITE',
                'short_name' => 'WHITE',
                'code' => '090',
                'ui_color_code' => '#FFFFFF',
            ],
            [
                'name' => 'WHITE/BLACK',
                'short_name' => 'WHT/BLK',
                'code' => '106',
                'ui_color_code' => '#FFFFFF',
            ],
            [
                'name' => 'WINE',
                'short_name' => 'WINE',
                'code' => '041',
                'ui_color_code' => '#722F37',
            ],
            [
                'name' => 'YELLOW',
                'short_name' => 'YELLOW',
                'code' => '080',
                'ui_color_code' => '#FFFF00',
            ],
        ];

        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
